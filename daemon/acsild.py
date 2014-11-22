#!/usr/local/bin/python
"""
Name: Acsild
Author: Matthieu 'Korrigan' Rosinski <korrigan@slash16.org>
Description: This is the daemon script which is in charge of network
discovery and metrics monitoring in Acsilserver

"""


import datetime
import time
import select
import platform

import pybonjour
import rrdtool
import psutil


_regtype = '_acsil._tcp'
_port = 80
_refresh_interval = 10


def register_callback(sdRef, flags, error, name, regtype, domain):
    if error == pybonjour.kDNSServiceErr_NoError:
        print 'Registered service {0}@{1} ({2})'.format(name, domain, regtype)


def init_rrd():
    rrdtool.create("cpu.rrd", "--step", "60", "--start", '0',
                   "DS:cpu:GAUGE:100:0:100",
                   "RRA:AVERAGE:0.5:1:600",
                   "RRA:AVERAGE:0.5:6:700",
                   "RRA:AVERAGE:0.5:24:775",
                   "RRA:AVERAGE:0.5:288:797",
                   "RRA:MAX:0.5:1:600",
                   "RRA:MAX:0.5:6:700",
                   "RRA:MAX:0.5:24:775",
                   "RRA:MAX:0.5:444:797"
    )
    rrdtool.create("ram.rrd", "--step", "60", "--start", '0',
                   "DS:ram:GAUGE:100:0:100",
                   "RRA:AVERAGE:0.5:1:600",
                   "RRA:AVERAGE:0.5:6:700",
                   "RRA:AVERAGE:0.5:24:775",
                   "RRA:AVERAGE:0.5:288:797",
                   "RRA:MAX:0.5:1:600",
                   "RRA:MAX:0.5:6:700",
                   "RRA:MAX:0.5:24:775",
                   "RRA:MAX:0.5:444:797"
    )


def collect_metrics():
    cpu = psutil.cpu_percent()
    ram = psutil.virtual_memory().percent
    rrdtool.update("cpu.rrd", 'N:%s' % cpu)
    rrdtool.update("ram.rrd", 'N:%s' % ram)

def gen_graphs():
    rrdtool.graph("cpu.png", '--start', '-2h', '--vertical-label=%', '-w 800', '-h 300',
                  "DEF:m1_num=cpu.rrd:cpu:AVERAGE",
                  "LINE1:m1_num#0000FF:cpu\r",
              )
    rrdtool.graph("ram.png", '--start', '-2h', '--vertical-label=%', '-w 800', '-h 300',
                  "DEF:m1_num=ram.rrd:ram:AVERAGE",
                  "LINE1:m1_num#0000FF:ram\r",
              )


def main():
    """
    Main entry point for acsild daemon

    """
    init_rrd()
    sd = pybonjour.DNSServiceRegister(name=platform.node(),
                                      regtype=_regtype,
                                      port=_port,
                                      callBack=register_callback)
    last_update = datetime.datetime.now()
    while 42:
        try:
            print "Updating metrics"
            collect_metrics()
            gen_graphs()
            rdy = select.select([sd], [], [], _refresh_interval)
            if sd in rdy[0]:
                pybonjour.DNSServiceProcessResult(sd)
        except Exception as e:
            print "Exception occured: {0}".format(e)
        finally:
            next_update = last_update + datetime.timedelta(seconds=_refresh_interval)
            sleep_interval = (next_update - datetime.datetime.now()).total_seconds()
            print "Sleeping for {} seconds".format(sleep_interval)
            time.sleep(max(0, sleep_interval))
            last_update = datetime.datetime.now()


if __name__ == "__main__":
    main()
