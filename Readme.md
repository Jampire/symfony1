Project setup:
* configure app/.env file from a .env-sample file
* enter project folder
* start docker: 'docker-compose start -d'
* enter php container: 'docker exec -it php bash'
* update composer: 'composer install', 'composer update'
* run migrations: 'doctrine:migrations:migrate'