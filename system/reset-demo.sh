#!/bin/bash
##
## reset-demo.sh for in  /home/darki/projets/acsilserver/git/system
## 
## Made by emmanuel ohland
## Login   <ohland_e@epitech.net>
## 
## Started on  Sat Aug 30 16:52:07 2014 emmanuel ohland
## Last update Fri Sep 19 00:34:30 2014 emmanuel ohland
##

ACSIL_DIR="/var/www/demo.acsilserver.com"
FLAGS="--env=prod --no-debug"
SCMD="$ACSIL_DIR/app/console $FLAGS"

$SCMD cache:clear
$SCMD assetic:dump
$SCMD doctrine:database:drop --force
$SCMD doctrine:database:create
$SCMD doctrine:schema:update --force
$SCMD acsilserver:api:client:populate

rm -fr $ACSIL_DIR/upload/*
