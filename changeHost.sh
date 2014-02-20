#!/bin/sh

if [ "$1" != "--oh" ] || [ "$2" = "" ] || [ "$3" != "--nh" ] || [ "$4" = "" ]; then
	echo "Usage : ./changeHost.sh --oh oldhost --nh newhost"
	return 0
fi
old=$2
new=$4
test=`grep -E "ServerName(\t|\ )*$old$" /etc/apache2/sites-enabled/acsilserver` 
if [ "$test" ]; then
	sed -E -i "s/^(\ |\t)*ServerName(\t|\ )*$old/\tServerName\ $new/" /etc/apache2/sites-enabled/acsilserver
	service apache2 restart
else
	echo "./changeHost.sh: old host not found in /etc/apache/sites-enabled/acsilserver"
	return 0
fi
