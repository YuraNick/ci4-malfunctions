docker-compose build --no-cache php-malfunctions
docker-compose up
docker login -u oauth -p $Y_CONTAINER_REGISTRY_OAUTH cr.yandex

docker tag deploy-php-malfunctions:latest yuriyiarovikov/ci4-malfunctions:latest
push на dockerHub
обновить в portainer