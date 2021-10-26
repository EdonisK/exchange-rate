# Exchange Rate

## Local setup

```shell
#to start all containers
docker-compose up -d --build

#to ssh to the container
docker-compose exec php bash

#only needed the first time
composer install

#only needed the first time - run migrations
symfony console doctrine:migration:migrate

```


## Usage
Access the endpoint via GET

http://localhost:8080/api/getExchangeRate?from=USD&to=EUR

GUI: http://localhost:8080