#!/bin/bash

set -eu

VER='0.0.1'
OS="$(tr '[A-Z]' '[a-z]' <<<$(uname))"
ARCH="$(tr '[A-Z]' '[a-z]' <<<$(arch))"

if [ ${ARCH} = 'i386' ] ; then
  ARCH='386'
elif [ ${ARCH} = 'x86_64' ] ; then
  ARCH='amd64'
fi

BIN_URL="https://github.com/moznion/req_mirror/releases/download/${VER}/req_mirror_${OS}_${ARCH}_${VER}"
BIN="$(pwd)/$(git rev-parse --show-cdup)/devtool/req_mirror"

curl --fail --location --output ${BIN} ${BIN_URL}

chmod 755 ${BIN}

