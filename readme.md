# Simple Cas Server

[![Build Status](https://travis-ci.org/leo108/simple_cas_server.svg)](https://travis-ci.org/leo108/simple_cas_server)

A simple PHP implement of CAS server

This project is deprecated, please refer to [laravel_cas_server](https://github.com/leo108/laravel_cas_server) and [php_cas_server](https://github.com/leo108/php_cas_server)

## Features

* [CAS protocol](https://apereo.github.io/cas/4.2.x/protocol/CAS-Protocol-Specification.html) v1/v2/v3 without proxy
* Users/Services Management

## Requirements

* PHP 5.5.9+
* [composer](https://getcomposer.org/)
* npm
* gulp

## Installation

1. git clone https://github.com/leo108/simple_cas_server
2. cd simple_cas_server
3. composer install
4. npm install
5. gulp

## Basic Usage

1. Edit `.env` file in the project's root directory, change the options's value that start with `DB_`
2. ./artisan migrate
3. ./artisan db:seed
4. ./artisan serve
5. visit [http://localhost:8000](http://localhost:8000), login with `admin`/`secret` 

## Configuration

All configurations are set in `.env` file

### Application Settings

`APP_LOCATE`: application Language, `en` | `cn`

### CAS Settings

`CAS_ALLOW_RESET_PWD`: allow user reset password by email, `true` | `false`. if set to `true`, you should configure mail sending options

`CAS_TICKET_LEN`: ticket length

`CAS_TICKET_EXPIRE`: ticket ttl, time in seconds

`CAS_LOCK_TIMEOUT`: lock time while validating a ticket, time in microseconds

## Todo

* <del>reset password by email</del>
* log user login history
* event hook
* gui installation
* tar ball release

## License

[MIT](http://opensource.org/licenses/MIT)
