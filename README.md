# digi-lara
A complete multi-vendor store with all facilities

# requirements
PHP version >>> `^8.1`\
Node version >>> `18.14.0`\
Npm version >>> `9.4.1`\
Composer version >>> `2.5.3`


## How to use in localhost:

#### Backend options:
Step 1: clone the project.

step 2: open the `backend` folder.

step 3: Create your database.

Step 4: rename the .even.example to `.env`.

Step 5: change this variables in `.env`.
### .env variables:
```.dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_name
DB_USERNAME=db_username
DB_PASSWORD=db_password

SANCTUM_STATEFUL_DOMAINS=localhost
SESSION_DOMAIN=.localhost
```

Step 6: open terminal and run this commands:
```php 
composer install
php artisan config:cache
php artisan migrate
php artisan db:seed --class="PermissionSeeder"
php artisan storage:link
php artisan serve
```

#### Front options

Step 1: open the `front` folder.

step 2: open the `.env` file.

step 3: change this variables in `.env`.
### .env variables:
```.dotenv
VUE_APP_BACKEND="Your backend domain for example: http://localhost:8000"
VUE_APP_FRONT="Your front domain for example: http://localhost:3000 "
```
step 4: open terminal and run this command:
````
npm install
npm run serve
 ````

#### username and password:
```
username: 00000000000
password: 12345678
```
Hope you enjoy.