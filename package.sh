#!/bin/sh

VERSION=`cat VERSION`
FINAL_NAME=app55-php-$VERSION
BUILD_DIR=build/$FINAL_NAME

mkdir -p $BUILD_DIR/
cp -R lib README.md LICENSE VERSION composer.json runtests.sh tests $BUILD_DIR/
find $BUILD_DIR -name '.*' -exec rm -Rf {} \;
cd build/
tar czf ../../$FINAL_NAME.tar.gz $FINAL_NAME/
rm -Rf build/
