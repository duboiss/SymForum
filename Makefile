.DEFAULT_GOAL := help

PHP = docker-compose exec -T php
YARN = docker run --rm -v $(PWD):/app -w /app -u $(shell id -u):$(shell id -g) node:latest yarn

COMPOSER = $(PHP) composer
PHPUNIT = $(PHP) bin/phpunit
SYMFONY = $(PHP) bin/console


##
## Database
.PHONY: db db-reset db-cache db-validate fixtures

db: vendor db-reset fixtures ## Reset database and load fixtures

db-reset: vendor ## Reset database
	@-$(SYMFONY) doctrine:database:drop --if-exists --force
	@-$(SYMFONY) doctrine:database:create --if-not-exists
	@$(SYMFONY) doctrine:schema:update --force

db-cache: vendor ## Clear doctrine database cache
	@$(SYMFONY) doctrine:cache:clear-metadata
	@$(SYMFONY) doctrine:cache:clear-query
	@$(SYMFONY) doctrine:cache:clear-result
	@echo "Cleared doctrine cache"

db-validate: vendor ## Checks doctrine's mapping configurations are valid
	@$(SYMFONY) doctrine:schema:validate --skip-sync -vvv --no-interaction

fixtures: vendor ## Load fixtures - requires database with tables
	@$(SYMFONY) doctrine:fixtures:load --no-interaction


##
## Linting
.PHONY: lint lint-container lint-twig lint-xliff lint-yaml

lint: vendor ## Run all lint commands
	make -j lint-container lint-twig lint-xliff lint-yaml

lint-container: vendor ## Checks the services defined in the container
	@$(SYMFONY) lint:container

lint-twig: vendor ## Check twig syntax in /templates folder (prod environment)
	@$(SYMFONY) lint:twig templates -e prod

lint-xliff: vendor ## Check xliff syntax in /translations folder
	@$(SYMFONY) lint:xliff translations

lint-yaml: vendor ## Check yaml syntax in /config and /translations folders
	@$(SYMFONY) lint:yaml config translations


##
## Node.js
.PHONY: assets build watch

yarn.lock: package.json
	$(YARN) upgrade

node_modules: yarn.lock ## Install yarn packages
	@$(YARN) install

assets: node_modules ## Run Webpack Encore to compile development assets
	@$(YARN) dev

build: node_modules ## Run Webpack Encore to compile production assets
	@$(YARN) build

watch: node_modules ## Recompile assets automatically when files change
	@$(YARN) watch


##
## PHP
.PHONY: php

php: ## Exec PHP container
	@docker-compose exec php sh

composer.lock: composer.json
	@$(COMPOSER) update

vendor: composer.lock ## Install dependencies in /vendor folder
	@$(COMPOSER) install --no-progress


##
## Project
.PHONY: install update cache-clear cache-warmup ci clean reset

install: db assets ## Install project dependencies

update: vendor node_modules ## Update project dependencies
	@$(COMPOSER) update
	@$(YARN) upgrade

cache-clear: vendor ## Clear cache for current environment
	@$(SYMFONY) cache:clear --no-warmup

cache-warmup: vendor cache-clear ## Clear and warm up cache for current environment
	@$(SYMFONY) cache:warmup

ci: db-validate quality tests ## Continuous integration

clean: purge ## Delete all dependencies
	@rm -rf .env.local var vendor node_modules public/build
	@echo "Var, vendor, node_modules and public/build folders have been deleted !"

reset: clean install


##
## Quality tools
.PHONY: quality phpcsfixer-audit phpcsfixer-fix phpstan twigcs

quality: ## Run linters and others quality tools
	make -j lint phpcsfixer-audit phpstan twigcs

phpcsfixer-audit: vendor ## Run php-cs-fixer audit
	@$(PHP) ./vendor/bin/php-cs-fixer fix --diff --diff-format=udiff --dry-run --no-interaction --ansi --verbose

phpcsfixer-fix: vendor ## Run php-cs-fixer fix
	@$(PHP) ./vendor/bin/php-cs-fixer fix --verbose

phpstan: vendor ## Run phpstan
	@$(PHP) ./vendor/bin/phpstan analyse --no-progress --xdebug

twigcs: vendor ## Run twigcs
	@$(PHP) ./vendor/bin/twigcs templates


##
## Tests
.PHONY: tests

tests: vendor ## Run tests
	@$(PHPUNIT)


##
## Utils
.PHONY: purge

purge: ## Purge cache and logs
	@rm -rf var/cache/* var/log/*
	@echo "Cache and logs have been deleted !"


##
## Help
.PHONY: help

help: ## List of all commands
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
