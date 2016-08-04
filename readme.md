# Simple Cas Server

[![Build Status](https://travis-ci.org/leo108/simple_cas_server.svg)](https://travis-ci.org/leo108/simple_cas_server)

A simple PHP implement of CAS server

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

## Usage

1. Edit `.env` file in the project's root directory, change the options's value that start with `DB_`, you can also change `APP_LOCATE` to `cn` if you are using Chinese
2. ./artisan migrate
3. ./artisan db:seed
4. ./artisan serve
5. visit [http://localhost:8000](http://localhost:8000), login with `admin`/`secret` 

## Todo

* reset password by email
* log user login history
* event hook
* gui installation
* tar ball release