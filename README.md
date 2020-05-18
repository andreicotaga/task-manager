# Task Manager
Make it work:

<ul>
    <li>Clone the project using Command Line or downloading the ZIP archive</li>
    <li>Execute <code>composer install</code> and <code>npm install</code></li>
</ul>

##### Make sure you have an MYSQL instance runing on 127.0.0.1:3306
Otherwise, make the correct changes in .env file for DATABASE_URL var.

Then, execute, 
<ol>
<li><code>php bin/console doctrine:database:create --if-not-exists</code></li>
<li><code>php bin/console doctrine:schema:update --force</code></li>
<li><code>php bin/console doctrine:fixtures:load</code></li>
</ol>

When everything is done, initialize there server with the following command:
<code>php bin/console server:run</code>

You can login as<b>ADMIN</b>and as a<b>USER</b>

Credentials for ADMIN:
admin@example.com/123456

Credentials for any USER:
user1@example.com/123456


