#!/bin/bash

# Workaround as PHPStan requires PHP version >= 7.1

composer require --quiet --no-interaction --dev phpstan/phpstan
vendor/bin/phpstan analyse
composer remove --quiet --no-interaction --dev phpstan/phpstan
