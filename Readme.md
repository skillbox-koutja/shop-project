Запуск приложения с помощью docker-compose:

```shell script
# 1. docker-init:
# 1.1. docker-down-clear:
docker-compose down -v --remove-orphans
# 1.2. shop-clear:
docker run --rm -v ${PWD}/shop:/app --workdir=/app alpine rm -f .ready
# 1.3. docker-pull:
docker-compose pull
# 1.4. docker-build:
docker-compose build
# 1.5. docker-up:
docker-compose up -d
# 2. shop-init:
# 2.1. shop-composer-install:
docker-compose run --rm shop-php-cli composer install
# 2.2. shop-wait-db:
until docker-compose exec -T shop-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done
# 2.3. shop-migrations:
docker-compose run --rm shop-php-cli php bin/console doctrine:migrations:migrate --no-interaction
# 2.4. shop-fixtures:
docker-compose run --rm shop-php-cli php bin/console doctrine:fixtures:load --no-interaction
# 2.5. shop-ready:
docker run --rm -v ${PWD}/shop:/app --workdir=/app alpine touch .ready
```
