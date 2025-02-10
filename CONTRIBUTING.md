Contributing
===

Contributions are welcome. If you want to ask or propose something, please 
[create an issue](https://github.com/craftzing/laravel-abilities/issues/new). If you want to contribute, please 
send in a pull request.

# ⤴️ Pull requests

Make sure to follow these rules when creating a pull request:
- Follow the [PSR-12](http://www.php-fig.org/psr/psr-12/) coding standards (though we have a PHP CS Fixer workflow in place that takes care of that for you)
- Write tests for new functionality or bug fixes and make sure test coverage is on point
- Keep the [README](README.md) file and [docs](docs) (if any) up-to-date with changes
- We follow [Semantic Versioning](http://semver.org/), so please send pull requests to the correct branch
- Update the [CHANGELOG.md](CHANGELOG.md) file with any changes/additions/... and follow the [changelog standards](http://keepachangelog.com/)

# 🏃‍➡️ Running locally

This project is fully Dockerized, meaning [Docker](https://docs.docker.com) (or [Orbstack](https://orbstack.dev) for macOS users) is the only requirement
to run this project locally. Using Docker Compose, we set up a container for each supported PHP version. 

To up the containers, run:
```shell
make up
```

You can start an interactive shell with each of these containers using:
```shell
make php<version>
```

Once you started an interactive shell with any of the containers, you can run the test suite and static analysis on 
that container using:
```shell
composer static-analysis
composer test:coverage

// ... or in a single command
composer ci
```

> [!TIP]
> When outside the containers, you can run these checks against all containers all at once using:
> ```shell
> make ci
> ```

To shut down all containers, run:
```shell
make down
```

# 📦 Laravel package development

[Laravel documentation](https://laravel.com/docs/master/packages)
