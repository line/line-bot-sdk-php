Hacking guide for SDK developers
==

First of all
--

Please execute `make install-devtool`.

How to run tests
--

Use `make test`.

How to execute [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
--

Use `make phpcs`.

How to execute [PHPMD](https://phpmd.org/)
--

Use `make phpmd`.

How to execute them all
--

`make`

Release Flow
--

1. Make a git tag (this project uses semantic versioning)
1. Push the tag to origin

That's all. It will be publish on [composer](https://packagist.org/packages/linecorp/line-bot-sdk) automatically.

e.g.

```
$ git tag 1.2.3
$ git push origin 1.2.3
```

