Paka-API
=============

The Paka API is a RESTful API based on Laravel 5 developed by me as my final school project.

Along with Paka API another two repos were developed, [Paka-Web](https://github.com/FabioAntunes/paka-web) a single page application and [Paka-APP](https://github.com/FabioAntunes/paka-app) a Cordova mobile application.

You will need a CouchDB and a MySQL server, just add those configs to the .env


### Installation:
  1. Clone the repo: <code>$ git clone git@github.com:FabioAntunes/paka-api.git</code>
  2. Enter the directory: <code>$ cd paka-api</code>
  3. Install <code>composer</code> dependencies: <code>$ composer install</code>
  4. Rename .env.example file and insert your configs 
  5. Migrate the database running <code>$ php artisan migrate</code>
  
### Launching API:
  1. Run the PHP server by doing <code>$ php artisan serv</code> or code>$ php artisan serv --host 0.0.0.0</code> if you want to access your server from outside.
