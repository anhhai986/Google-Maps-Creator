##Create new migration

    $ php artisan migrate:make create_[table_name]_table

##Run migration
Create database tables for local environment:

    $ php artisan migrate --env=local

Seed tables with data:

    $ php artisan db:seed --env=local
	
	 composer.phar dump-autoload