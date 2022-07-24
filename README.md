### Steps to deploy app:

- docker-compose -f docker-compose.yml build
- docker-compose -f docker-compose.yml up -d

go to **citrus-mysql** container

- docker exec -ti citrus-mysql bash
- mysql -u root -p root

file **Docker/mysql/bootstrap.sql** contains commands for user create

go to **citrus-app** container

- bin/console doctrine:database:create
- bin/console doctrine:migrations:migrate
- bin/console doctrine:fixtures:load
- bin/console app:api-bootstrap

Admin panel: \
http://127.0.0.1:8888/admin/dashboard

Test user:\
login: test_user@mail.com\
pass: pass_1234