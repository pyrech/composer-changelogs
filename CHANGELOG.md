# Changes between versions

## 1.8.2 (2021-11-08)

* Fix PHP 8 warning about passing null to strpos() ([#78](https://github.com/pyrech/composer-changelogs/pull/78))

## 1.8.1 (2021-10-13)

* Fix semver output colors ([#74](https://github.com/pyrech/composer-changelogs/pull/74))

## 1.8.0 (2021-09-06)

* Add semver color output representation ([#73](https://github.com/pyrech/composer-changelogs/pull/73))

## 1.7.1 (2020-04-27)

* Add support for Composer ^2.0 ([#68](https://github.com/pyrech/composer-changelogs/pull/68))

## 1.7.0 (2019-10-21)

* Display VCS Revision for dev version ([#64](https://github.com/pyrech/composer-changelogs/pull/64))
* Update how the plugin autoload its classes ([#63](https://github.com/pyrech/composer-changelogs/pull/63))
* Drop support for PHP < 7.1 ([#66](https://github.com/pyrech/composer-changelogs/pull/66))

## 1.6.0 (2017-12-11)

* Adding configurable post update priority ([#46](https://github.com/pyrech/composer-changelogs/pull/46))

## 1.5.1 (2017-01-10)

* Fix compatibility with newest Composer Plugin API ([#42](https://github.com/pyrech/composer-changelogs/pull/42))

## 1.5 (2016-07-04)

* Add better description for downgrade operations ([#39](https://github.com/pyrech/composer-changelogs/pull/39))
* Remove tests skipping ([#38](https://github.com/pyrech/composer-changelogs/pull/38))
* Add support for gitlab repositories ([#37](https://github.com/pyrech/composer-changelogs/pull/37))

## 1.4 (2016-03-21)

* Update coding standards ([#25](https://github.com/pyrech/composer-changelogs/pull/25))
* Add support for bitbucket ssh urls ([#27](https://github.com/pyrech/composer-changelogs/pull/27))
* Fix tests with newer composer versions ([#28](https://github.com/pyrech/composer-changelogs/pull/28))
* Fix bug when switching from a local repository back to the original repository ([#30](https://github.com/pyrech/composer-changelogs/pull/30))
* Add GitBasedUrlGenerator to replace AbstractUrlGenerator ([#20](https://github.com/pyrech/composer-changelogs/pull/20))
* Add experimental autocommit feature ([#29](https://github.com/pyrech/composer-changelogs/pull/29))
* Add support for github ssh urls ([#32](https://github.com/pyrech/composer-changelogs/pull/32))

## 1.3 (2015-11-13)

* Add support for comparison across forks and better detect dev versions ([#19](https://github.com/pyrech/composer-changelogs/pull/19))
* Add autoloading of classes required to make the plugin always working ([#22](https://github.com/pyrech/composer-changelogs/pull/22))

## 1.2 (2015-10-22)

* Add a WordPress url generator for theme and plugin package ([#11](https://github.com/pyrech/composer-changelogs/pull/11))
* Remove new line in output in case nothing to display ([#12](https://github.com/pyrech/composer-changelogs/pull/12))
* Update documentation of local install to use the --dev flag ([#16](https://github.com/pyrech/composer-changelogs/pull/16))
* Update documentation of tests to use the `composer test` command ([#17](https://github.com/pyrech/composer-changelogs/pull/17))

## 1.1.1 (2015-10-11)

* Add support for old versions of composer (at least v1.0.0-alpha8) ([#9](https://github.com/pyrech/composer-changelogs/pull/9))

## 1.1 (2015-10-10)

* Add support for bitbucket repositories ([4e90441](https://github.com/pyrech/composer-changelogs/commit/4e9044113dc24654378f6f7676aefaebebcc1163))
* Add new line after plugin output ([8b22e38](https://github.com/pyrech/composer-changelogs/commit/8b22e38eeffc0ed4ced6e7270fcb4087fea97301))
* Add support for install and uninstall operations ([#6](https://github.com/pyrech/composer-changelogs/pull/6))
* Add support for dev versions ([#7](https://github.com/pyrech/composer-changelogs/pull/7))

## 1.0 (2015-09-26)

* Initial release
