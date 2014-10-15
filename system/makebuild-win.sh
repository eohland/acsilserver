#!/bin/bash
##
## makebuild-win.sh for in  /srv/http/acsilserver/system
## 
## Made by emmanuel ohland
## Login   <ohland_e@epitech.net>
## 
## Started on  Sat Mar 01 01:07:27 2014 emmanuel ohland
## Last update Wed Oct 15 14:27:12 2014 emmanuel ohland
##

BUILDNAME='acsilserver_win'
BUILDVER=`git --work-tree='..' tag | tail -1 | sed 's/^.//'`
BUILDDIR="build-win/$BUILDNAME.$BUILDVER"
BUILDAPPDIR="$BUILDDIR/acsilserver-master"

build() {
  log "Copying base files..."
  mkdir -p "$BUILDAPPDIR"
  mkdir "$BUILDDIR/tmp"
  unzip win/LightTPD.zip -d "$BUILDDIR"
  unzip win/PHP -d "$BUILDDIR"
  cp -r ../webapp/{app,bin,src,vendor,web} "$BUILDAPPDIR"

  log "Cleaning unwanted files..."
  #TODO: don't copy insead remvoing
  rm -f  $BUILDAPPDIR/app/config/parameters.yml
  rm -f  $BUILDAPPDIR/app/acsilserver.db
  rm -fr $BUILDAPPDIR/app/cache/*
  rm -fr $BUILDAPPDIR/app/logs/*
  rm -fr $BUILDAPPDIR/web/uploads/*

  log "Creating configuration..."
  cp -r win/config/* $BUILDDIR
  sed -i 's/# \(path:\s*%database_path%\)/\1/' \
    "$BUILDAPPDIR/app/config/config.yml"

  cd $BUILDAPPDIR
  log "Creating database..."
  SCONSOLE='php app/console'
  $SCONSOLE doctrine:database:create -e prod
  $SCONSOLE doctrine:schema:create -e prod
  #$SCONSOLE assets:install -e prod
  #$SCONSOLE assetic:dump -e prod --no-debug
  #$SCONSOLE cache:clear -e prod

  log "Cleaning cache and logs..."
  rm -fr app/cache/*
  rm -fr app/logs/*
  cd -

  log "Creating archive file..."
  cd "$BUILDDIR"
  zip -r -9 "../$BUILDNAME.$BUILDVER.zip" .
  cd -
  #log "Removing build directory..."
  rm -fr "$BUILDDIR"
  log "Done."
}

log() {
  echo -e "\033[1m$1\033[0m"
}

build

# vim:set ts=2 sw=2 et:
