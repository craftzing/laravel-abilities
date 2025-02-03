Contributing
===

Contributions are welcome. If you want to ask or propose something, please 
[create an issue](https://github.com/craftzing/laravel-abilities/issues/new). If you want to contribute, please 
send in a pull request.

## ⤴️ Pull requests

Make sure to follow these rules when creating a pull request:
- Follow the [PSR-12](http://www.php-fig.org/psr/psr-12/) coding standards (though we have a PHP CS Fixer workflow in place that takes care of that for you)
- Write tests for new functionality or bug fixes and make sure test coverage is on point
- Keep the [README](README.md) file and [docs](docs) up-to-date with changes
- We follow [Semantic Versioning](http://semver.org/), so please send pull requests to the correct branch
- Update the [CHANGELOG.md](CHANGELOG.md) file with any changes/additions/... and follow the [changelog standards](http://keepachangelog.com/)

## 🧪 Running tests

You can run the test suite with the following command:
```bash
composer phpunit
```

## 🏅Quality assurance

```bash
composer cs:fix
composer phpstan
```

## 🛳️ Docker

### Requirements
- [Docker](https://docs.docker.com) ([Orbstack](https://orbstack.dev) for Mac users)
- [Docker compose](https://docs.docker.com/compose)

### Installation

```shell
make up
```

### Tests and quality assurance

```shell
make test
```

### Shutting down container
```shell
make down
```

## 📦 Laravel package development

[Laravel documentation](https://laravel.com/docs/master/packages)