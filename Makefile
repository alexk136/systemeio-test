PHP           := php
CONSOLE       := bin/console --ansi
COMPOSER      := $(shell which composer) --ansi
CSFIXER       := ./vendor/bin/php-cs-fixer --ansi

include .env
export $(shell sed 's/=.*//' .env)

install: export COMPOSER_MEMORY_LIMIT=-1
install:
	$(PHP) $(COMPOSER) install
.PHONY: install

check:
	$(PHP) $(COMPOSER) check
	$(PHP) $(COMPOSER) validate --strict
	$(PHP) $(CONSOLE) doctrine:schema:validate --skip-sync -v
	$(PHP) $(CONSOLE) debug:container --deprecations
.PHONY: check./