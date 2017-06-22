docker-compose exec openresty chown -R www-data:www-data /var/www/app
docker-compose exec php chown -R www-data:www-data /var/www/app
docker-compose exec elk chown -R logstash:logstash /opt/logstash/patterns
docker-compose exec elk chown -R logstash:logstash /var/log