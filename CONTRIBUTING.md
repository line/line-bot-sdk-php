# How to contribute to LINE Bot SDK for PHP project

First of all, thank you so much for taking your time to contribute!
LINE Bot SDK for PHP is not very different from any other open source projects you are aware of.
It will be amazing if you could help us by doing any of the following:

- File an issue in [the issue tracker](https://github.com/line/line-bot-sdk-php/issues) to report bugs and propose new features and improvements.
- Ask a question using [the issue tracker](https://github.com/line/line-bot-sdk-php/issues) (__Please ask only about this SDK__).
- Contribute your work by sending [a pull request](https://github.com/line/line-bot-sdk-php/pulls).

## Development

### Install dependencies

Run `make install` to install all dependencies for development.

### Understand the project structure

The project structure is as follows:

- `src`: The main library code.
- `test`: Test code.
- `examples`: Example projects that use the library.
- `tools`: Development tools including code generation scripts.
- `docs`: Auto-generated [Documentation](https://line.github.io/line-bot-sdk-php/) files by phpDocumentor.

### Edit OpenAPI templates

Almost all code is generated with OpenAPI Generator based on [line-openapi](https://github.com/line/line-openapi)'s YAML files.
Thus, you cannot edit almost all code under `src/clients/` and `src/webhook/` directly.

You need to edit the custom templates under [tools/custom-template](tools/custom-template) instead.

After editing the templates, run `./tools/gen-oas-client.sh` to generate the code, and then commit all affected files.
If not, CI status will fail.

When you update code, be sure to check consistencies between generated code and your changes.

### Add unit tests

We use [PHPUnit](https://phpunit.de/) for unit tests.
Please add tests to the appropriate test directories to verify your changes.

Especially for bug fixes, please follow this flow for testing and development:
1. Write a test before making changes to the library and confirm that the test fails.
2. Modify the code of the library.
3. Run the test again and confirm that it passes thanks to your changes.

### Run your code in your local

You can use the [example projects](examples/) to test your changes locally before submitting a pull request.

### Run CI tasks in your local

Run `make` or `make check` to check comprehensively. Following tests will run.

- [PHPUnit](https://github.com/sebastianbergmann/phpunit) `make test`
- Copyright check `make copyright`
- [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) `make phpcs`
- [PHPMD](https://phpmd.org/) `make phpmd`
- [PHPStan](https://phpstan.org/) `make phpstan`

### Documentation

https://line.github.io/line-bot-sdk-php/

We use [phpDocumentor](https://www.phpdoc.org/) to generate API documentation.
**Please make sure your new or modified code is covered by proper PHPDoc comments.**
Good documentation ensures that contributors and users can easily read and understand how the methods and classes work.

To generate documentation locally, make sure phpDocumentor is installed, then run:

```
$ wget https://github.com/phpDocumentor/phpDocumentor/releases/download/v3.3.1/phpDocumentor.phar
$ php phpDocumentor.phar run -d src -t docs
```

## Contributor license agreement

When you are sending a pull request and it's a non-trivial change beyond fixing typos, please make sure to sign
[the ICLA (individual contributor license agreement)](https://cla-assistant.io/line/line-bot-sdk-php). Please
[contact us](mailto:dl_oss_dev@linecorp.com) if you need the CCLA (corporate contributor license agreement).