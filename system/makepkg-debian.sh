#!/bin/sh
##
## makepkg-debian.sh for in  /srv/http/acsilserver/system
## 
## Made by emmanuel ohland
## Login   <ohland_e@epitech.net>
## 
## Started on  Fri Feb 21 16:04:50 2014 emmanuel ohland
## Last update Tue Feb 25 15:06:07 2014 emmanuel ohland
##

PKGNAME='acsilserver'
PKGVER=`git -C .. tag | tail -1 | sed 's/^.//'`
PKGDIR="pkg/$PKGNAME-$PKGVER"

build() {
  mkdir -p "$PKGDIR"
  cp -r ../{app,bin,src,vendor,web,README.md,TODO} $PKGDIR
  cp -r debian $PKGDIR
  sed -i "s/0.1/$PKGVER/" $PKGDIR/debian/files

  php $PKGDIR/app/console doctrine:schema:create --dump-sql > \
  $PKGDIR/debian/sql/install/mysql

  #TODO: don't copy insead remvoing
  rm -f  $PKGDIR/app/config/parameters.yml
  rm -fr $PKGDIR/app/cache
  rm -fr $PKGDIR/app/logs
  rm -fr $PKGDIR/web/uploads
}

build

# vim:set ts=2 sw=2 et:
