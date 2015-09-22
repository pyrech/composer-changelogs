# composer-changelogs

[![Latest Stable Version](https://poser.pugx.org/pyrech/composer-changelogs/v/stable)](https://packagist.org/packages/pyrech/composer-changelogs) [![Total Downloads](https://poser.pugx.org/pyrech/composer-changelogs/downloads)](https://packagist.org/packages/pyrech/composer-changelogs) [![Latest Unstable Version](https://poser.pugx.org/pyrech/composer-changelogs/v/unstable)](https://packagist.org/packages/pyrech/composer-changelogs)

composer-changelogs is a plugin for Composer. It displays texts after
each Composer update to easily access changelogs for updated vendors.

## Installation

You can install it either locally:

```shell
composer require "pyrech/composer-changelogs"
```

or globally:

```shell
composer global require "pyrech/composer-changelogs"
```

## Usage

That's it! Composer will enable automatically the plugin as soon it's
installed. Just run your Composer updates as usual :)

If you no longer want to display changelogs, you can either:
- run your Composer command with the option `--no-plugins`
- uninstall the package

## Further documentation

You can see the current and past versions using one of the following:

* the `git tag` command
* the [releases page on Github](https://github.com/pyrech/composer-changelogs/releases)
* the file listing the [changes between versions](CHANGELOG.md)

And finally some meta documentation:

* [versioning and branching models](VERSIONING.md)
* [contribution instructions](CONTRIBUTING.md)

## Credits

* [Loïck Piera](https://github.com/pyrech)
* [All contributors](https://github.com/pyrech/composer-changelogs/graphs/contributors)

## License

composer-changelogs is licensed under the MIT License - see the [LICENSE](LICENSE)
file for details.
