# How to contribute to LINE Bot SDK for PHP project

First of all, thank you so much for taking your time to contribute!
LINE Bot SDK for PHP is not very different from any other open source projects you are aware of.
It will be amazing if you could help us by doing any of the following:

- File an issue in [the issue tracker](https://github.com/line/line-bot-sdk-php/issues) to report bugs and propose new features and improvements.
- Ask a question using [the issue tracker](https://github.com/line/line-bot-sdk-php/issues) (__Please ask only about this SDK__).
- Contribute your work by sending [a pull request](https://github.com/line/line-bot-sdk-php/pulls).

## Development

### Install dependencies

Run `composer install` to install all dependencies for development.

### Understand the project structure

The project structure is as follows:

- `src`: The main library code.
- `test`: Test code.
- `examples`: Example projects that use the library.
- `tools`: Development tools including copyright checking scripts.
- `generator`: Custom OpenAPI Generator codegen with Pebble templates for code generation.
- `support/docs`: Hand-written guide sources (reStructuredText) for phpDocumentor Guides.
- `build/site`: Auto-generated documentation output (not committed).

### Edit OpenAPI templates

Almost all code is generated with OpenAPI Generator based on [line-openapi](https://github.com/line/line-openapi)'s YAML files.
Thus, you cannot edit almost all code under `src/clients/` and `src/webhook/` directly.

You need to edit the Pebble templates under [generator/src/main/resources/line-bot-sdk-php-generator](generator/src/main/resources/line-bot-sdk-php-generator) instead.

After editing the templates, run `python generate-code.py` to generate the code, and then commit all affected files.
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

Run `composer check` to check comprehensively. Following tests will run.

- [PHPUnit](https://github.com/sebastianbergmann/phpunit) `composer test`
- [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer) `composer cs`
- [PHPMD](https://phpmd.org/) `composer md`
- [PHPStan](https://phpstan.org/) `composer stan`
- Copyright check `composer copyright`

### Documentation

https://line.github.io/line-bot-sdk-php/

We use [phpDocumentor](https://www.phpdoc.org/) to generate API documentation and hand-written guides.
**Please make sure your new or modified code is covered by proper PHPDoc comments.**

To generate documentation locally:

```bash
composer docs
```

This generates the site under `build/site/`. To preview in a browser:

```bash
php -S 127.0.0.1:8000 -t build/site
```

Then open http://127.0.0.1:8000/.

#### Documentation structure

| Path | Content |
|---|---|
| `support/docs/` | Hand-written guide sources (reStructuredText) |
| `phpdoc.dist.xml` | phpDocumentor configuration |
| `build/site/` | Generated output (not committed) |

The generated site contains API docs at the root (`/classes/`, `/namespaces/`, etc.) and hand-written guides under `/docs/`.

## Contributor license agreement

When you are sending a pull request and it's a non-trivial change beyond fixing typos, please make sure to sign
[the ICLA (individual contributor license agreement)](https://cla-assistant.io/line/line-bot-sdk-php). Please
[contact us](mailto:dl_oss_dev@linecorp.com) if you need the CCLA (corporate contributor license agreement).
