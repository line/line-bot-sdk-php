#!/bin/bash

FILES=$(cd ./$(git rev-parse --show-cdup); find `pwd` -name '*.php' | grep -v /vendor/)

NOT_INCLUDE_COPYRIGHT_FILES=()

for FILE in $FILES; do
  if ! grep -q 'LINE Corporation licenses this file to you under the Apache License' $FILE; then
    NOT_INCLUDE_COPYRIGHT_FILES=("${NOT_INCLUDE_COPYRIGHT_FILES[@]}" $FILE)
  fi
done

EXIT_CODE=0
if [ ${#NOT_INCLUDE_COPYRIGHT_FILES[@]} -gt 0 ]; then
  echo '[ERROR] Detected file(s) that does not include copyright'
  EXIT_CODE=1
fi

for FILE in ${NOT_INCLUDE_COPYRIGHT_FILES[@]}; do
  echo $FILE
done

exit $EXIT_CODE

