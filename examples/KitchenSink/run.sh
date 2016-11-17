#!/bin/bash

PORT=$1
if [ -z $PORT ]; then
  cat <<EOT
[ERROR] Please specify the port
e.g.
./run.sh 8080
EOT
  exit 1
fi

SCRIPT_DIR=$(cd $(dirname ${BASH_SOURCE:-$0}); pwd)

mkdir -p $SCRIPT_DIR/public/static/tmpdir
php -S 0.0.0.0:${PORT} -t public
rm $SCRIPT_DIR/public/static/tmpdir/*

