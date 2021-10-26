# Exchange Rate

## Local setup

```shell
#to start all containers
docker-compose up -d --build

#only needed the first time
composer install

#only needed the first time - run migrations
symfony console doctrine:migration:migrate

```