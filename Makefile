COMPOSER_BIN = ./vendor/bin

.PHONY: default test doc phpcs phpmd check install-devtool copyright release

default: check

test:
	$(COMPOSER_BIN)/phpunit ./tests

doc:
	yes yes | $(COMPOSER_BIN)/apigen generate --source=./src --destination=./docs --title line-bot-sdk-php

phpcs:
	$(COMPOSER_BIN)/phpcs --standard=PSR2 src/ tests/ examples/EchoBot/src examples/EchoBot/public examples/KitchenSink/src examples/KitchenSink/public

phpmd:
	$(COMPOSER_BIN)/phpmd ./src text cleancode,codesize,controversial,design,unusedcode,naming | \
		grep -v 'Avoid using static access to class' | \
		grep -v 'Avoid variables with short names like' | \
		grep -v 'Avoid excessively long variable names like' | \
		grep -v 'The method register uses an else expression' | \
		grep -v 'The method sendRequest uses an else expression' | \
		cat
	$(COMPOSER_BIN)/phpmd ./examples/EchoBot/src text cleancode,codesize,controversial,design,unusedcode,naming | \
		grep -v 'Avoid using static access to class' | \
		grep -v 'Avoid variables with short names like' | \
		grep -v 'Avoid excessively long variable names like' | \
		grep -v 'The method register uses an else expression' | \
		grep -v 'The method sendRequest uses an else expression' | \
		cat
	$(COMPOSER_BIN)/phpmd ./examples/EchoBot/public text cleancode,codesize,controversial,design,unusedcode,naming | \
		grep -v 'Avoid using static access to class' | \
		grep -v 'Avoid variables with short names like' | \
		grep -v 'Avoid excessively long variable names like' | \
		grep -v 'The method register uses an else expression' | \
		grep -v 'The method sendRequest uses an else expression' | \
		cat
	$(COMPOSER_BIN)/phpmd ./examples/KitchenSink/src text cleancode,codesize,controversial,design,unusedcode,naming | \
		grep -v 'Avoid using static access to class' | \
		grep -v 'Avoid variables with short names like' | \
		grep -v 'Avoid excessively long variable names like' | \
		grep -v 'The method register uses an else expression' | \
		grep -v 'The method sendRequest uses an else expression' | \
		cat
	$(COMPOSER_BIN)/phpmd ./examples/KitchenSink/public text cleancode,codesize,controversial,design,unusedcode,naming | \
		grep -v 'Avoid using static access to class' | \
		grep -v 'Avoid variables with short names like' | \
		grep -v 'Avoid excessively long variable names like' | \
		grep -v 'The method register uses an else expression' | \
		grep -v 'The method sendRequest uses an else expression' | \
		cat

copyright:
	bash ./devtool/check_copyright.sh

check: test copyright phpcs phpmd

install-devtool:
	bash ./devtool/download_req_mirror.sh

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

