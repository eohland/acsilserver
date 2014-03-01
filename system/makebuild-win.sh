#!/bin/sh
##
## makebuild-win.sh for in  /srv/http/acsilserver/system
## 
## Made by emmanuel ohland
## Login   <ohland_e@epitech.net>
## 
## Started on  Sat Mar 01 01:07:27 2014 emmanuel ohland
## Last update Sun Mar 02 01:02:03 2014 emmanuel ohland
##

BUILDNAME='acsilserver_win'
BUILDVER=`git -C .. tag | tail -1 | sed 's/^.//'`
BUILDDIR="build-win/$BUILDNAME.$BUILDVER"
BUILDAPPDIR="$BUILDDIR/acsilserver-master"

build() {
  mkdir -p "$BUILDAPPDIR"
  unzip win/LightTPD.zip -d "$BUILDDIR"
  unzip win/PHP -d "$BUILDDIR"
  cp -r ../{app,bin,src,vendor,web,README.md,TODO} "$BUILDAPPDIR"

  #TODO: don't copy insead remvoing
  rm -f  $BUILDAPPDIR/app/config/parameters.yml
  rm -fr $BUILDAPPDIR/app/cache
  rm -fr $BUILDAPPDIR/app/logs
  rm -fr $BUILDAPPDIR/web/uploads

  cp -r win/config/* $BUILDDIR
  sed -i 's/# \(path:\s*%database_path%\)/\1/' \
    "$BUILDAPPDIR/app/config/config.yml"

  cd $BUILDAPPDIR
  SCONSOLE='php app/console'
  $SCONSOLE doctrine:database:create
  $SCONSOLE doctrine:schema:create
  #$SCONSOLE assets:install --env=prod
  #$SCONSOLE assetic:dump --env=prod --no-debug
  #$SCONSOLE cache:clear --env=prod
  cd -

  cd "$BUILDDIR"
  zip -r -9 "../$BUILDNAME.$BUILDVER.zip" .
  cd -
  #rm -fr "$BUILDDIR"
}

build

# vim:set ts=2 sw=2 et:
