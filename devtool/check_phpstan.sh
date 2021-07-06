#!/bin/bash

# Hacky solution as PHPStan requires PHP version >= 7.1

composer require --dev phpstan/phpstan
vendor/bin/phpstan analyse
