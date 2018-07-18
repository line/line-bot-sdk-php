.PHONY: default test doc phpcs phpmd check install-devtool copyright release clean install reinstall

default: check

test:
	composer test

doc:
	composer doc

phpcs:
	composer cs

phpmd:
	composer md

copyright:
	devtool/check_copyright.sh

check: test copyright phpcs phpmd

clean:
	rm -rf vendor composer.lock

install:
	composer install

reinstall: clean install
