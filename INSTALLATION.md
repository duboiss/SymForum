## Installation
Clone project and move inside :
```sh
$ git clone https://github.com/DuboisS/symforum.git
$ cd symforum
```

### Step 1 - Dependencies
Install PHP and Node.js dependencies :
```sh
$ composer install
$ yarn install
```

### Step 2 - Database
Configure database informations in .env file (or .env.local if you plan to contribute).

Create database, execute the migrations and launch fixtures if you wish :
```sh
$ php bin/console doctrine:database:create --if-not-exists
$ php bin/console doctrine:schema:update --force
$ php bin/console doctrine:fixtures:load --no-interaction
```

### Step 3 - Assets
Build webpack encore assets :
```sh
$ yarn encore prod
```


## Usage
Launch server :
```sh
$ symfony serve
// or php bin/console server:run
```

Enjoy !
