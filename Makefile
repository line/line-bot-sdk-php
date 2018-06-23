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

release:
ifndef VERSION
	@tput setaf 1
	@echo '[ERROR] $$VERSION is missing; it must be specified'
	@echo 'USAGE:'
	@echo '    make release VERSION=1.2.3'
	@tput sgr0
	@exit 255
endif
	make doc
	git tag $(VERSION)
	git push origin $(VERSION)

clean:
	rm -rf vendor composer.lock

install:
	composer install

reinstall: clean install
