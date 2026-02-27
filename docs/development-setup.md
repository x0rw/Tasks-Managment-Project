## Setup postgres database 

run 
```bash
docker compose up -d
```
To launch a local postgres db for development

To test it download a postgres client and run `psql -h 127.0.0.1 -U devuser -d task_manager_db`

## Run Migrations

copy the example env `cp .env.example .env` 
then `php artisan migrate`

## Run Seeders 

Run seeders to populate the database
```
php artisan db:seed
```
```


