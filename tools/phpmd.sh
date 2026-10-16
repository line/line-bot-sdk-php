#!/bin/bash

DIRS=(
  src/constants
  src/laravel
  src/parser
  examples/EchoBot/src
  examples/EchoBot/public
  examples/KitchenSink/src
  examples/KitchenSink/public
)

phpmd analyze \
  --ignore-violations-on-exit \
  --format text \
  --ruleset phpmd.xml \
  "${DIRS[@]}"
