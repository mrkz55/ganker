docker-compose exec redis redis-cli flushall
docker-compose exec php ./artisan queue:restart
