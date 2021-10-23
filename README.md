# BDD, Behat, Mink and other Wonderful ThingsRESTing with Symfony v5

Well hi there! This repository base the code and script
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
composer install
```

3. Inside `app` container load up your database

To load up your database, run:

```bash
php ./bin/console d:m:m --no-interaction && \
php ./bin/console doctrine:fixtures:load --no-interaction
```

4. You'll find the site at http://localhost:8080.

You can login with:

user: admin
pass: admin

4. Run behat tests:
```bash
./vendor/bin/behat
```
