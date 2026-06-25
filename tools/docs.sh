#!/bin/bash

PHPDOC_VERSION="v3.8.1"
PHPDOC_URL="https://github.com/phpDocumentor/phpDocumentor/releases/download/${PHPDOC_VERSION}/phpDocumentor.phar"
PHPDOC_SHA256="aa00973d88b278fe59fd8dce826d8d5419df589cb7563ac379856ec305d6b938"

if [ ! -f phpDocumentor.phar ]; then
  curl -L -o phpDocumentor.phar "$PHPDOC_URL"
  echo "$PHPDOC_SHA256  phpDocumentor.phar" | shasum -a 256 -c || {
    rm -f phpDocumentor.phar
    exit 1
  }
fi

php phpDocumentor.phar run -d src -t docs
