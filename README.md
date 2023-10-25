# laravel-package-boilerplate

![laravel workflow](https://github.com/deanhowe/laravel-package-boilerplate/actions/workflows/Laravel/badge.svg)

> [!WARNING]
> This is not quite ready for general consumption - use at your own risk 🤨

Boilerplate for Laravel packages. Use it as a starting point for your own Laravel packages.

Includes [Pest](https://pestphp.com/) & [Pint](https://laravel.com/docs/10.x/pint) along with PHPUnit and PHPCodeSniffer configurations, a known good Travis CI configuration and a couple of base test cases.
We recommend reading the [composer docs](https://getcomposer.org/doc/04-schema.md) for more information about what you might need to add/remove from the `composer.json` file. 
Included are the most common things you might need, but you may need to add more and you __*will*__ need to remove some (anything with a `*-suggestions` key for a start).

We use `orchestra/testbench` & `orchestra/testbench-dusk` as the basis of the provided testing set-up, but we have added quite a few shortcuts for you starting with:

> `composer testbench:artisan`

and 
 
> `composer duskbench:artisan`

You can also drop the `artisan` from the command which achieves the same thing.

You can also substitute `artisan` for the following commands:

 * `build` -  Run builds for workbench
 * `create-sqlite` - Create sqlite database file (package|workbench)
 * `clear` - Purge skeleton folder to original state `package:purge-skeleton` - runs `config:clear` etc, etc)
 * `devtool` - Configure Workbench for package development
 * `discover` - Rebuild the cached package manifest `package:discover`
 * `drop-sqlite` - Drop sqlite database file (package|workbench)
 * `install` - Setup Workbench for package development
 * `prepare` -Rebuild the cached package manifest (the same as `package:discover`)
 * `serve` - Serve the application on the PHP development server
 * `test` - Run the package tests

Not forgetting `duskbench:install-chromedriver` (re-install the latest chrome driver)



If that was not enough we also give you :

```bash
composer pint
```

```bash
composer test
```

```bash
composer stan
```

```bash
composer types
```

```bash
composer lint
```

```bash
composer dump
```

```bash
composer nuke
```


How do I use it?
----------------
###### Step 1
```bash
composer create-project deanhowe/laravel-package-boilerplate <YOUR_NEW_PACKAGE_DIRECTORY>
```

This will generate a starting boilerplate for your app.

###### Step 2
You'll want to update your `composer.json` with your required namespace and other details - you can do this by running
```bash
 vendor/bin/artisan app:name InsertYourProjectNameHere
 ```

Run `composer testenbech:install` - this will set up the environment for testing.

    mv <orchestra/testbench-core/laravel>/database/database.sqlite.example <orchestra/testbench-core/laravel>/database/database.sqlite

or

    touch <orchestra/testbench-core/laravel>/database/database.sqlite


Test cases
----------

The package includes three test cases:

* `TestCase` - Effectively the normal Laravel test case. Use it the same way you would your normal Laravel test case
* `SimpleTestCase` - Extends the default PHPUnit test case, so it doesn't set up a Laravel application, making it quicker and well-suited to properly isolated unit tests
* `BrowserKitTestCase` - Sets up BrowserKit

There is a script in the `composer.json` file that could be considered a bit of a risk -`composer nuke` - this command :
* __*removes*__ the `./vendor` directory and the `./composer.lock` file.
* re-installs ALL the composer dependencies.
* runs `clear` & `prepare` commands for both the `testbench` and `duskbench` workbenches.
* updates the Chrome driver.

Also includes my [Artisan Standalone](https://github.com/matthewbdaly/artisan-standalone) package as a development dependency. As a result, you should be able to run Artisan commands as follows:

~~`vendor/bin/artisan make:model Example`~~

```bash
vendor/bin/artisan make:model Example
```
~~
