## Start project locally

### 2. Install Docker and docker-compose

https://docs.docker.com/compose/install/

composer install

cp .env.example .env

docker-compose up --build -d -- запустити в фоновому режимі

docker exec -it olxpricetracker /bin/bash -- відкрити контейнер

vendor/bin/doctrine-migrations migrations:migrate -- виконати міграції