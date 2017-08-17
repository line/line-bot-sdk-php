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

How to generate HTML phpdoc
--

Use `make doc`

Pull request policy
--

- Please *DON'T* include the generated HTML phpdoc in pull request

Release Flow
--

1. Generate HTML phpdoc
1. Make a git tag (this project uses [semantic versioning](http://semver.org/))
1. Push the tag to origin
1. Edit [GitHub releases](https://github.com/line/line-bot-sdk-php/releases)

You can execute steps 1-3 with `make release` command.

That's all. It will be publish on [composer](https://packagist.org/packages/linecorp/line-bot-sdk) automatically.

e.g.

```
$ make release VERSION=1.2.3
# Then, edit GitHub releases
```

Testing of HTTP client
--

Test cases of HTTP client send HTTP request actually to [req_mirror](https://github.com/moznion/req_mirror). req_miror is an HTTP server that parrots received a request as a response.
`make install-devtool` downloads an executable binary of req_mirror to your `devtool/` directory and test runner launches req_mirror server at `beforeClass` phase.
After, each test case sends request to the launched server and verify the request.

