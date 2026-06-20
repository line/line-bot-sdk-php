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

phpmd --ignore-violations-on-exit "$(IFS=,; echo "${DIRS[*]}")" text phpmd.xml
