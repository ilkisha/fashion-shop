## Run with Docker

docker compose up -d --build
docker compose exec php composer install
docker compose exec php php bin/console doctrine:migrations:migrate
# optional
docker compose exec php php bin/console doctrine:fixtures:load

Open http://localhost:8080
