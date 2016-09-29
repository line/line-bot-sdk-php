COMPOSER_BIN = ./vendor/bin

.PHONY: default test doc phpcs phpmd check

default: check

test:
	$(COMPOSER_BIN)/phpunit ./tests

doc:
	yes yes | $(COMPOSER_BIN)/apigen generate --source=./src --destination=./docs --title line-bot-sdk-php

phpcs:
	$(COMPOSER_BIN)/phpcs --standard=PSR2 src/ tests/ examples/EchoBot/src examples/EchoBot/public examples/KitchenSink/src examples/KitchenSink/public

phpmd:
	$(COMPOSER_BIN)/phpmd ./src text cleancode,codesize,controversial,design,unusedcode,naming | grep -v 'Avoid using static access to class'
	$(COMPOSER_BIN)/phpmd ./tests text cleancode,codesize,controversial,design,unusedcode,naming | grep -v 'Avoid using static access to class'
	$(COMPOSER_BIN)/phpmd ./examples/EchoBot/src text cleancode,codesize,controversial,design,unusedcode,naming | grep -v 'Avoid using static access to class'
	$(COMPOSER_BIN)/phpmd ./examples/EchoBot/public text cleancode,codesize,controversial,design,unusedcode,naming | grep -v 'Avoid using static access to class'
	$(COMPOSER_BIN)/phpmd ./examples/KitchenSink/src text cleancode,codesize,controversial,design,unusedcode,naming | grep -v 'Avoid using static access to class'
	$(COMPOSER_BIN)/phpmd ./examples/KitchenSink/public text cleancode,codesize,controversial,design,unusedcode,naming | grep -v 'Avoid using static access to class'

copyright:
	bash ./devtool/check_copyright.sh

check: test copyright phpcs phpmd

