### Made with Symfony 6.1

This App requires PHP >= 8.1


## To run the application

##### Install PHP dependencies with Composer
#### `Composer install`

##### To Generate the SSL keys
#### `php bin/console lexik:jwt:generate-keypair`

##### To Generate DB (Mysql)
#### `php bin/console doctrine:database:create `
#### `php bin/console doctrine:schema:update --force `

##### then run symfony server
#### `symfony serve`

