shell:
	docker compose run --rm php sh

test:
	docker compose run --rm php vendor/bin/phpunit --testdox

ecs:
	docker compose run --rm php vendor/bin/ecs check --fix

phpstan:
	docker compose run --rm php vendor/bin/phpstan --memory-limit=-1

qa: ecs phpstan
