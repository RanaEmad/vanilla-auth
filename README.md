# vanilla-auth

A Basic CRUD and authentication app implemented in vanilla PHP

# Installation

- Clone this repository
- Import the vanilla-auth.sql file in your database
- Create a .env file and fill it with the information placed in the .env.example file (this information is necessary for the app to run)
- Run `composer install` to get all the dependencies
- if you want to run the tests
  - Import the vanilla-auth-test.sql file in your database
  - Update the variables in phpunit.xml file with the corresponding variables in the .env file
  - Make sure the APP_ENV in the .env file is set to testing
  - Run `vendor/bin/phpunit --testsuite tests`
- Before running the application make sure the APP_ENV variable is set to development
- Run your server and visit the url of the project
