# CakePHP with Stimulus.js

A demo showing [Stimulus](https://stimulusjs.org) and [CakePHP](https://cakephp.org) 3.x working together.
As example, it shows partial page rendering; replacing and/or adding server-side generated HTML snippets on the web page.

[See it here](https://cakephp-stimulusjs.herokuapp.com)

[![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/nico-amsterdam/cakephp-stimulus-example1)

What Stimulus does can be done with other javascript libraries as well, but what I like is:
- The HTML is not cluttered with script code or vague class names
- It's clear which controllers are used and where (data-controller attribute)
- It's clear where the controller will make changes (data-target attribute)
- It's clear what triggers the controller (data-action attribute)
- The controllers have a life-cycle and are automatically mounted and unmounted if the DOM-tree is modified



## Installation CakePHP

1. Install [PHP](https://www.php.net/manual/en/install.php) and enable/install the INTL extension
2. Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
3. Run `php composer.phar create-project --prefer-dist cakephp/app [app_name]`.

If Composer is installed globally, run

```bash
composer create-project --prefer-dist cakephp/app myapp
```

You can now either use your machine's webserver to view the default home page, or start
up the built-in webserver with:

```bash
bin/cake server -p 8765
```

Then visit `http://localhost:8765` to see the welcome page.

## Installation Stimulus

TODO

## 

## Update

Since this skeleton is a starting point for your application and various files
would have been modified as per your needs, there isn't a way to provide
automated upgrades, so you have to do any updates manually.

## Configuration

Read and edit `config/app.php` and setup the `'Datasources'` and any other
configuration relevant for your application.

## Layout

The app skeleton uses a subset of [Foundation](http://foundation.zurb.com/) (v5) CSS
framework by default. You can, however, replace it with any other library or
custom styles.
