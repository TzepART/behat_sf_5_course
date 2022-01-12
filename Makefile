compose_local=docker-compose -f docker-compose.yaml

.PHONY: help
help:
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: app_up
app_up: ## Build and Run project
	$(compose_local) up --build -d
	$(compose_local) exec php sh -c "composer install --no-interaction"
	$(compose_local) exec php sh -c "php ./bin/console d:m:m --no-interaction"
	$(compose_local) exec php sh -c "php ./bin/console doctrine:fixtures:load --no-interaction"

.PHONY: app_down
app_down: ## Destroy project's containers
	$(compose_local) down

.PHONY: rebuild_db
rebuild_db: ## Reload fixtures data
	$(compose_local) exec php sh -c "php ./bin/console doctrine:fixtures:load --no-interaction"

.PHONY: app_bash
app_bash: ## Open command-line
	$(compose_local) exec php bash

.PHONY: behat_run
behat_run: ## Run behat tests
	$(compose_local) exec php "./vendor/bin/behat"