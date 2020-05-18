# Task Manager
How to make it work:

<ul>
    <li>Clone the project using Command Line or downloading the ZIP archive</li>
    <li>Execute <code>composer install</code> and <code>npm install</code></li>
</ul>

##### Make sure you have an MYSQL instance runing on 127.0.0.1:3306
Otherwise, make the correct changes in .env file for DATABASE_URL var.

Then, execute, <code>php bin/console doctrine:database:create --if-not-exists
</code>

php app/console doctrine:database:create
php app/console doctrine:schema:update --force
php app/console doctrine:fixtures:load

