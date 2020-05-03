# SymForum
<p align="center">
<a href="https://opensource.org/licenses/MIT"><img alt="License MIT" src="https://img.shields.io/badge/License-MIT-yellow.svg"></a>
<a href="https://travis-ci.org/github/DuboisS/SymForum"><img alt="Travis (.org) branch" src="https://img.shields.io/travis/DuboisS/SymForum/master"></a>
<img alt="PHP from Travis config" src="https://img.shields.io/travis/php-v/DuboisS/SymForum">
</p>

SymForum is a simple PHP forum built with the [Symfony 5](https://symfony.com/) framework.

This project is under development, it is **not currently intended for the production**.

The goal is to eventually make a bundle allowing any symfony developer to add a forum on his existing site.

### Features
- Forums with sub-forums in categories
- Breadcrumbs, antispam, reports...
- Threads and forums locked/unlocked
- Messages with WYSIWYG
- Statistics (online users record, totals...)
- Control panel for moderators and administrators

## Getting Started
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites
- PHP 7.4 and [composer](https://getcomposer.org/)
- MySQL 5.7
- [Node.js](https://nodejs.org/en/)
- [Yarn](https://yarnpkg.com/lang/en/)
- [Symfony binary](https://github.com/symfony/cli) (optional)


### Installation
All steps are detailed in [INSTALLATION.md](INSTALLATION.md)


## Running the tests
Create the SQLite database (located in /var folder) :
```sh
$ php bin/console doctrine:database:create --env=test
```
Use the following command to run tests :
```sh
$ php bin/phpunit
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## Acknowledgment
- Gux : OptionService, breadcrumb twig filter
- [Capuchon](https://github.com/rampinflorian) : prototypes testings and suggestions

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
