# PHP8, Behat and Mock Service with Symfony v5

## Setup the Project

1. Make sure you have [Docker](https://www.docker.com/).

2. In a terminal, move into the project, then install the composer dependencies:

```bash
make app_up
```

3. You'll find the site at http://localhost:8080.

You can login with:

user: admin
pass: admin

4. Run behat tests:
```bash
make behat_run
```

5. If you want to run specific commands inside container run:
```bash
make app_bash
```
... and write your command :)

6. Rebuild project's data:
```bash
make rebuild_db
```

7. Stop and remove app's containers:
```bash
make app_down
```

