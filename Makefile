up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear shop-clear docker-pull docker-build docker-up shop-init
test: shop-test
test-coverage: shop-test-coverage
test-unit: shop-test-unit
test-unit-coverage: shop-test-unit-coverage

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

shop-init: shop-composer-install shop-assets-install shop-oauth-keys shop-wait-db shop-migrations shop-fixtures shop-ready

shop-clear:
	docker run --rm -v ${PWD}/shop:/app --workdir=/app alpine rm -f .ready

shop-composer-install:
	docker-compose run --rm shop-php-cli composer install

shop-assets-install:
	docker-compose run --rm shop-node yarn install
	docker-compose run --rm shop-node npm rebuild node-sass

shop-oauth-keys:
	docker-compose run --rm shop-php-cli mkdir -p var/oauth
	docker-compose run --rm shop-php-cli openssl genrsa -out var/oauth/private.key 2048
	docker-compose run --rm shop-php-cli openssl rsa -in var/oauth/private.key -pubout -out var/oauth/public.key
	docker-compose run --rm shop-php-cli chmod 644 var/oauth/private.key var/oauth/public.key

shop-wait-db:
	until docker-compose exec -T shop-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

shop-schema-diff:
	docker-compose run --rm shop-php-cli php bin/console doctrine:migrations:diff --no-interaction
	docker-compose run --rm shop-php-cli php bin/console doctrine:schema:drop --full-database --no-interaction

shop-migrations:
	docker-compose run --rm shop-php-cli php bin/console doctrine:migrations:migrate --no-interaction

shop-fixtures:
	docker-compose run --rm shop-php-cli php bin/console doctrine:fixtures:load --no-interaction

shop-fixtures-shop-payment-methods:
	docker-compose run --rm shop-php-cli php bin/console doctrine:fixtures:load --append --group=shop-payment-methods --no-interaction

shop-fixtures-shop-delivery-methods:
	docker-compose run --rm shop-php-cli php bin/console doctrine:fixtures:load --append --group=shop-delivery-methods --no-interaction

shop-cache-clear:
	docker-compose run --rm shop-php-cli php bin/console cache:clear

shop-ready:
	docker run --rm -v ${PWD}/shop:/app --workdir=/app alpine touch .ready

shop-assets-dev:
	docker-compose run --rm shop-node npm run dev

shop-test:
	docker-compose run --rm shop-php-cli php bin/phpunit

shop-test-coverage:
	docker-compose run --rm shop-php-cli php bin/phpunit --coverage-clover var/clover.xml --coverage-html var/coverage

shop-test-unit:
	docker-compose run --rm shop-php-cli php bin/phpunit --testsuite=unit

shop-test-unit-coverage:
	docker-compose run --rm shop-php-cli php bin/phpunit --testsuite=unit --coverage-clover var/clover.xml --coverage-html var/coverage

build-production:
	docker build --pull --file=shop/docker/production/nginx.docker --tag ${REGISTRY_ADDRESS}/shop-nginx:${IMAGE_TAG} shop
	docker build --pull --file=shop/docker/production/php-fpm.docker --tag ${REGISTRY_ADDRESS}/shop-php-fpm:${IMAGE_TAG} shop
	docker build --pull --file=shop/docker/production/php-cli.docker --tag ${REGISTRY_ADDRESS}/shop-php-cli:${IMAGE_TAG} shop
	docker build --pull --file=shop/docker/production/postgres.docker --tag ${REGISTRY_ADDRESS}/shop-postgres:${IMAGE_TAG} shop
	docker build --pull --file=shop/docker/production/redis.docker --tag ${REGISTRY_ADDRESS}/shop-redis:${IMAGE_TAG} shop
	docker build --pull --file=centrifugo/docker/production/centrifugo.docker --tag ${REGISTRY_ADDRESS}/centrifugo:${IMAGE_TAG} centrifugo

push-production:
	docker push ${REGISTRY_ADDRESS}/shop-nginx:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/shop-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/shop-php-cli:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/shop-postgres:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/shop-redis:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/centrifugo:${IMAGE_TAG}


deploy-production:
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'rm -rf docker-compose.yml .env'
	scp -o StrictHostKeyChecking=no -P ${PRODUCTION_PORT} docker-compose-production.yml ${PRODUCTION_HOST}:docker-compose.yml
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "REGISTRY_ADDRESS=${REGISTRY_ADDRESS}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MANAGER_APP_SECRET=${MANAGER_APP_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MANAGER_DB_PASSWORD=${MANAGER_DB_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MANAGER_REDIS_PASSWORD=${MANAGER_REDIS_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MANAGER_MAILER_URL=${MANAGER_MAILER_URL}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "MANAGER_OAUTH_FACEBOOK_SECRET=${MANAGER_OAUTH_FACEBOOK_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_BASE_URL=${STORAGE_BASE_URL}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_HOST=${STORAGE_FTP_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_USERNAME=${STORAGE_FTP_USERNAME}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_PASSWORD=${STORAGE_FTP_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "CENTRIFUGO_WS_HOST=${CENTRIFUGO_WS_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "CENTRIFUGO_API_KEY=${CENTRIFUGO_API_KEY}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "CENTRIFUGO_SECRET=${CENTRIFUGO_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose pull'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose up --build -d'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'until docker-compose exec -T manager-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose run --rm manager-php-cli php bin/console doctrine:migrations:migrate --no-interaction'
