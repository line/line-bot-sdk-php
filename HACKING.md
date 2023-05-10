Hacking guide for SDK developers
==

Testing
--

Run `make` or `make check` to check comprehensively. Following tests will run.

- [PHPUnit](https://github.com/sebastianbergmann/phpunit) `make test`
- Copyright check `make copyright`
- [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) `make cs`
- [PHPMD](https://phpmd.org/) `make md`
- [PHPStan](https://phpstan.org/) `make stan`

Pull request policy
--

- Please *DON'T* include the generated HTML phpdoc in pull request

Installing [phpDocumenter](https://docs.phpdoc.org/)
--

We use `phpDocumenter` to generate API documents automatically. Required packages are not installed by composer because it requires PHP 7.2 ~ 8.0.

```
$ wget https://phpdoc.org/phpDocumentor.phar
```

Release Flow
--

Install `phpDocumenter` before releasing new version.

1. Update VERSION constant varialbe at `Constant/Meta.php`
1. Generate HTML phpdoc `php phpDocumentor.phar`
1. Make a git tag (this project uses [semantic versioning](http://semver.org/))
1. Push the tag to origin
1. Edit [GitHub releases](https://github.com/line/line-bot-sdk-php/releases)

That's all. It will be publish on [composer](https://packagist.org/packages/linecorp/line-bot-sdk) automatically.
