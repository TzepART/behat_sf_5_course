# BDD, Behat, Mink and other Wonderful ThingsRESTing with Symfony v5

Well hi there! This repository holds the code and script
for the [Behat PHP course on KnpUniversity](https://knpuniversity.com/screencast/behat).

## Setup the Project

1. Make sure you have [Docker](https://www.docker.com/).

2. In a terminal, move into the project, then install the composer dependencies:

```bash
docker-compose up -d
```

3. Inside `app` container (example):

```bash
docker exec -it behat_sf_5_course_app_1 /bin/bash
```

run:
```bash
composer install --optimize-autoloader --no-scripts --no-progress --no-plugins
```

3. Inside `app` container load up your database

This project uses an Sqlite database, which normally is supported by PHP
out of the box.

To load up your database file, run:

```bash
php ./bin/console doctrine:database:create && \
php ./bin/console doctrine:schema:update --force && \
php ./bin/console doctrine:fixtures:load --no-interaction
```

This will create - and populate - an `app/app.db` file.

4. You'll find the site at http://localhost:8080.

You can login with:

user: admin
pass: admin

4. Run behat tests:
```bash
./vendor/bin/behat
```
