# REST API with Slim [![Build Status](https://travis-ci.org/Meabed/slim-api.svg?branch=master)](https://travis-ci.org/Meabed/slim-api) [![Packagist](https://img.shields.io/packagist/dm/meabed/slim-api.svg)](https://packagist.org/packages/meabed/slim-api) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/meabed/slim-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/meabed/slim-api/?branch=master) [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md) [![codecov](https://codecov.io/gh/Meabed/slim-api/branch/master/graph/badge.svg)](https://codecov.io/gh/Meabed/slim-api)

A RESTful API boilerplate for Slim framework. Features included:

* Jwt Authentication
* Endpoint Tests and Unit Tests
* Build Process with [Travis CI](https://travis-ci.org/)
* Event Handling
* Pagination
* API Resources
* Validation

## Getting Started

First, clone the repo:

```bash
git clone https://github.com/Meabed/slim-api
```

### Install dependencies

```bash
cd slim-api
composer install
```

### Configure the Environment

Create `.env` file:

```bash
cat .env.example > .env
```

If you want you can edit database name, database username and database password.

### Run Application

To start making RESTful requests to slim-api start the PHP local server using:

```bash
php -S localgost:3000 -t public
```

## TODO

- [ ] Prepare readme
- [ ] Docs / Wiki
- [ ] Travis CI / Codecov
- [ ] Add more functions / aliases
- [ ] Add more tests
- [ ] Add examples
