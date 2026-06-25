#!/bin/bash

ERRORS=0
COUNT=0

while IFS= read -r -d '' file; do
  COUNT=$((COUNT + 1))
  output=$(php -l "$file" 2>&1)
  if [ $? -ne 0 ]; then
    echo "$output"
    ERRORS=$((ERRORS + 1))
  fi
done < <(find src/clients examples -name '*.php' -print0)

if [ $ERRORS -eq 0 ]; then
  echo "Lint: no syntax errors ($COUNT files checked)"
else
  echo "[ERROR] $ERRORS file(s) with syntax errors"
fi

exit $ERRORS
