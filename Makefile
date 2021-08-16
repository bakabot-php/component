ci: csdiff psalm phpunit cleanup

cleanup:
	docker-compose down -v

composer:
	docker-compose run --rm composer validate
	docker-compose run --rm composer install --quiet --no-cache --ignore-platform-reqs
	docker-compose run --rm composer normalize --quiet --dry-run

csdiff: composer
	docker-compose run --rm php vendor/bin/php-cs-fixer fix --dry-run --diff --verbose

csfix: composer
	docker-compose run --rm php vendor/bin/php-cs-fixer fix

psalm: composer
	docker-compose run --rm php vendor/bin/psalm --show-info=true

phpunit: composer
	docker-compose run --rm php -dxdebug.mode=coverage vendor/bin/phpunit
