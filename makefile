# Variables
DOCKER = docker
DOCKER_COMPOSE = docker compose
PHP_FPM_CONTAINER = php-fpm
NGINX_CONTAINER = nginx
EXEC = $(DOCKER) exec -it $(PHP_FPM_CONTAINER)
PHP = $(EXEC) php
COMPOSER = $(EXEC) composer
SYMFONY_CONSOLE = $(PHP) bin/console

# Colors
BLUE = echo -e "\033[34m$1\033[0m"

## —— 🚀 App —————————————————————————————————————————————————————————————————
init: ## Init a new Symfony Project
	mkdir -p ./app
	$(MAKE) build
	$(MAKE) start
	$(COMPOSER) create-project symfony/skeleton:"7.0.*" .
	$(MAKE) install-phpcs
	$(MAKE) install-phpstan
	$(MAKE) install-phpunit
	@$(call BLUE,"The application is available at: http://127.0.0.1:8080/.")


cc: ## Clear cache
	$(SYMFONY_CONSOLE) cache:clear

## —— 🎻 Composer ——
composer-install: ## Install dependencies
	$(COMPOSER) install

composer-require: ## Add new depencencies
	$(COMPOSER) require $(ARGS)

composer-update: ## Update dependencies
	$(COMPOSER) update

composer-clear-cache: ## clear-cache dependencies
	$(COMPOSER) clear-cache

## —— 📦 Installation —————————————————————————————————————————————————————————————————
install-phpcs: ## Install and configure PHPCS
	$(COMPOSER) require --dev squizlabs/php_codesniffer --no-interaction
	@cp phpcs.xml.dist app/phpcs.xml.dist
	@$(call BLUE,"PHPCS installed and configured successfully.")


install-phpstan: ## Install and configure PHPStan
	$(COMPOSER) require --dev phpstan/phpstan phpstan/phpstan-symfony --no-interaction
	@cp phpstan.dist.neon app/phpstan.dist.neon
	@$(call BLUE,"PHPStan installed and configured successfully.")

install-phpunit: ## Install PHPUnit
	$(COMPOSER) require --dev phpunit/phpunit --no-interaction
	@cp phpunit.xml.dist app/phpunit.xml.dist
	@$(call BLUE,"PHPUnit installed and configured successfully.")


## —— 🐳 Docker —————————————————————————————————————————————————————————————————
build: ## Build app with Images
	$(DOCKER_COMPOSE) build

rebuild: ## Stops and removes containers, volumes, and orphaned networks, then rebuilds the services
	$(DOCKER_COMPOSE) down --volumes --remove-orphans
	$(DOCKER_COMPOSE) up --build -d

start: ## Start the app
	$(DOCKER_COMPOSE) up -d

stop: ## Stop the app
	$(DOCKER_COMPOSE) stop

prune: ## Removes all unused volumes to free up space
	$(DOCKER_COMPOSE) down -v

logs: ## Display the container logs
	$(DOCKER_COMPOSE) logs -f

exec-php-fpm: ## Opens an interactive bash shell inside the php-fpm container
	$(DOCKER_COMPOSE) exec $(PHP_FPM_CONTAINER) bash

exec-nginx: ## Opens an interactive bash shell inside the php-fpm container
	$(DOCKER_COMPOSE) exec $(NGINX_CONTAINER) sh

## —— 🧪 Quality Tools —————————————————————————————————————————————————————————————————
phpcs: ## Run PHP_CodeSniffer
	$(EXEC) vendor/bin/phpcs $(ARGS)

phpcbf: ## Fix automaticly PHPCS fixer issues
	$(EXEC) vendor/bin/phpcbf $(ARGS)

phpstan: ## Run PHPStan analysis
	$(EXEC) vendor/bin/phpstan analyse --memory-limit=1G $(ARGS)

phpunit: ## Run PHPUnit tests
	$(EXEC) vendor/bin/phpunit $(ARGS)

## —— ⚙️  Others —————————————————————————————————————————————————————————————————
help: ## List of commands
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'