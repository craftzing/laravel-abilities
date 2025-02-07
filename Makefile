.PHONY: up
up:
	docker compose up --detach
	docker compose exec php84 composer install

.PHONY: down
down:
	docker compose down

.PHONY: test-php83
test-php83:
	docker compose exec php83 composer test

.PHONY: test-php84
test-php84:
	docker compose exec php84 composer test
