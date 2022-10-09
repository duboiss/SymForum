# SymForum
<p align="center">
<a href="https://opensource.org/licenses/MIT"><img alt="License MIT badge" src="https://img.shields.io/badge/License-MIT-yellow.svg"></a>
<a href="https://github.com/DuboisS/SymForum/actions"><img alt="CI badge" src="https://github.com/DuboisS/SymForum/workflows/CI/badge.svg"></a>
<a href="https://insight.symfony.com/projects/39b38022-6788-4113-a277-3fec71115743"><img alt="Symfony Insight badge" src="https://insight.symfony.com/projects/39b38022-6788-4113-a277-3fec71115743/mini.svg"></a>
</p>

SymForum is a simple PHP forum built with the [Symfony 6](https://symfony.com/) framework.
This project is under development, it is **not currently intended for the production**.
The goal is to eventually make a bundle allowing any symfony developer to add a forum on his existing site.

### Features
- Forums with sub-forums in categories
- Breadcrumbs, antispam, reports...
- Threads and forums locked/unlocked
- Messages with WYSIWYG, likes
- Statistics (online users record, totals...)
- Control panel for moderators and administrators

### Code Quality
To get a quality code, SymForum uses:
- Linters: including [PHPLint](https://github.com/php-parallel-lint/PHP-Parallel-Lint), [TwigCs](https://github.com/friendsoftwig/twigcs), [ESLint](https://eslint.org/) and symfony linters (container, twig, xliff, yaml)
- [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
- [PHPStan](https://github.com/phpstan/phpstan) (max level)
- [SymfonyInsight](https://insight.symfony.com/)

## Getting Started
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites
- [Docker & Docker Compose](https://www.docker.com/get-started)
- [Make](https://www.gnu.org/software/make/): windows users go [here](http://gnuwin32.sourceforge.net/packages/make.htm)


### Installation
All steps are detailed in [INSTALLATION.md](INSTALLATION.md)


## Running the tests
Create the SQLite database (located in /var folder) :
```sh
$ docker compose exec php bin/console doctrine:database:create --env=test
```

Use the following command to run tests :
```sh
$ make tests
```


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## Acknowledgment
- Gux : OptionService, breadcrumb twig filter, help for docker
- [Capuchon](https://github.com/rampinflorian) : prototypes testings and suggestions

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
