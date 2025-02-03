workspace-service := workspace

.PHONY: up
up:
	docker compose up --detach

.PHONY: down
down:
	docker compose down

.PHONY: test
test:
	docker compose exec ${workspace-service} composer cs:check
	docker compose exec ${workspace-service} composer phpstan
	docker compose exec ${workspace-service} composer phpunit
