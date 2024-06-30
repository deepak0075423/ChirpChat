# Getting Started with Chirp Chat App

This is an Online Chatting and interacting platform.

### To run the project 
1) Clone the repository
2) start xampp server
   Open project in your terminal and Run the following commands

3) 'install composer'
4) 'doctrine migration:migrate'
5) 'bin/console cache:clear'
6) 'symfony server:start'

# composer
Composer is a dependency manager for PHP that allows you to manage your project's libraries and packages.

# Installation
If you don't have Composer installed, you can install it by following the official Composer installation guide.

## Usage
To install the project dependencies, navigate to the project directory and run:

composer install

## Updating Dependencies
To update your project's dependencies to their latest versions, run:

composer update

For more information on using Composer, refer to the official documentation.


# Starting the Symfony Server
Symfony provides a built-in web server to facilitate development. To start the Symfony server, follow these steps:

## Prerequisites
Ensure you have the Symfony CLI installed. You can install it by following the official installation guide.

## Starting the Server
To start the Symfony server, navigate to your project directory and run:

## bash
symfony server:start
This will start the server, and you can access your application by visiting http://127.0.0.1:8000 in your web browser.

## Stopping the Server
To stop the server, run:

symfony server:stop

Using Composer to Start the Server
If you prefer to use Composer scripts to start the server, you can add a script to your composer.json file:

{
    "scripts": {
        "start-server": "symfony server:start"
    }
}
Then, you can start the server using:

composer run-script start-server

## Additional Server Options
The Symfony server provides additional options for customization. For example, you can specify a different port or bind address:

symfony server:start --port=8080 --bind=127.0.0.1
For more information about the Symfony server and its options, refer to the Symfony local web server documentation.

## HAPPY CODING
