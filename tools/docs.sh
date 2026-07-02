#!/usr/bin/env bash
set -euo pipefail

rm -rf build/site build/cache

vendor/bin/phpdoc run --config=phpdoc.dist.xml
