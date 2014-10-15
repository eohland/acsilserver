#!/bin/bash
##
## makepkg-debian.sh for in  /srv/http/acsilserver/system
## 
## Made by emmanuel ohland
## Login   <ohland_e@epitech.net>
## 
## Started on  Fri Feb 21 16:04:50 2014 emmanuel ohland
## Last update Wed Oct 15 14:27:36 2014 emmanuel ohland
##

PKGNAME='acsilserver'
PKGVER=`git --work-tree='..' tag | tail -1 | sed 's/^.//'`
PKGDIR="pkg/$PKGNAME-$PKGVER"

build() {
  mkdir -p "$PKGDIR"
  cp -r ../webapp/{app,bin,src,vendor,web,README.md} $PKGDIR
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

package() {
  cd $PKGDIR
  debuild -uc -us -i -b
}

build
if which debuild >/dev/null 2>&1
then
  package
fi

# vim:set ts=2 sw=2 et:
