.DEFAULT_GOAL := help

COMPOSER = composer
PHPUNIT = php bin/phpunit
SYMFONY = php bin/console
SYMFONY_BIN = symfony
YARN = yarn


##
## Database
.PHONY: db db-reset db-cache fixtures

db: vendor db-reset fixtures ## Reset database and load fixtures

db-reset: vendor ## Reset database
	@$(EXEC_PHP) php -r 'echo "Wait database...\n"; set_time_limit(30); require __DIR__."/config/bootstrap.php"; $$u = parse_url($$_ENV["DATABASE_URL"]); for(;;) { if(@fsockopen($$u["host"].":".($$u["port"] ?? 3306))) { break; }}'
	@-$(SYMFONY) doctrine:database:drop --if-exists --force
	@-$(SYMFONY) doctrine:database:create --if-not-exists
	@$(SYMFONY) doctrine:schema:update --force

db-cache: vendor ## Clear doctrine database cache
	@$(SYMFONY) doctrine:cache:clear-metadata
	@$(SYMFONY) doctrine:cache:clear-query
	@$(SYMFONY) doctrine:cache:clear-result
	@echo "Cleared doctrine cache"

fixtures: vendor ## Load fixtures - requires database with tables
	@$(SYMFONY) d:f:l --no-interaction


##
## Lint
.PHONY: lint lint-container lint-twig lint-xliff lint-yaml

lint: vendor lint-container lint-twig lint-xliff lint-yaml ## Run all lint commands

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
.PHONY: assets

yarn.lock: package.json
	$(YARN) upgrade

node_modules: yarn.lock ## Install yarn packages
	@$(YARN)

assets: node_modules ## Run Webpack Encore to compile assets
	@$(YARN) dev


##
## PHP
composer.lock: composer.json
	@$(COMPOSER) update

vendor: composer.lock ## Install dependencies in /vendor folder
	@$(COMPOSER) install


##
## Project
.PHONY: install start update cache-clear cache-warmup clean reset

install: db assets ## Install project dependencies

start: install serve ## Install project dependencies and launch symfony web server

update: vendor node_modules ## Update project dependencies
	@$(COMPOSER) update
	@$(YARN) upgrade

cache-clear: vendor ## Clear cache for current environment
	@$(SYMFONY) cache:clear --no-warmup

cache-warmup: vendor cache-clear ## Clear and warm up cache for current environment
	@$(SYMFONY) cache:warmup

clean: purge ## Delete all dependencies
	@rm -rf .env.local node_modules var vendor
	@echo -e "Vendor and node_modules folder have been deleted !"

reset: unserve clean install


##
## Symfony bin
.PHONY: serve unserve security

serve: ## Run symfony web server in the background
	@$(SYMFONY_BIN) serve --daemon --no-tls

unserve: ## Stop symfony web server
	@$(SYMFONY_BIN) server:stop

security: vendor ## Check packages vulnerabilities (using composer.lock)
	@$(SYMFONY_BIN) check:security


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
	@echo -e "Cache and logs have been deleted !"


##
## Help
.PHONY: help

help: ## List of all commands
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
