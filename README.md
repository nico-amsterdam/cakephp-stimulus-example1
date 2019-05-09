# 1. CakePHP with Stimulus.js

A demo showing [Stimulus](https://stimulusjs.org) and [CakePHP](https://cakephp.org) 3.x working together.
As example, it shows partial page rendering; replacing/adding server-side generated HTML snippets on the web page.

[See it here](https://cakephp-stimulusjs.herokuapp.com)

Deploy it yourself: [![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/nico-amsterdam/cakephp-stimulus-example1)

## 1.1 Why Stimulus

What Stimulus does can be done with other javascript libraries as well, but what I like is:
- The HTML is not cluttered with script code or vague class names
- It's clear which controllers are used and where (data-controller attribute)
- It's clear where the controller will make changes (data-target attribute)
- It's clear what triggers the controller (data-action attribute)
- The controllers have a life-cycle and are automatically mounted and unmounted if the DOM-tree is modified.


## 1.2 About the demo

TODO: what the demo demonstrates.

# 2 Installation

## 2.1 Install PHP 7 and CakePHP

1. Install [PHP](https://www.php.net/manual/en/install.php) and enable/install the INTL extension
2. Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.

## 2.2 Clone/Fork this example project 

```bash
git clone https://github.com/nico-amsterdam/cakephp-stimulus-example1

composer update --lock
```

## 2.3 Run CakePHP 

You can now either use your machine's webserver to view the default home page, or start
up the built-in webserver with:

```bash
bin/cake server -p 8765
```

Then visit `http://localhost:8765` to see the welcome page.

## 2.4 Create a new CakePHP project

Run `php composer.phar create-project --prefer-dist cakephp/app [app_name]`.

If Composer is installed globally, run

```bash
composer create-project --prefer-dist cakephp/app myapp
```

## 2.5 Install & run Stimulus

### 2.5.1 Add Stimulus configuration files to an existing CakePHP project

Copy the following files from the example:
- `.babelrc`
- `composer.json`
- `composer.lock`
- `package.json`
- `package-lock.json`
- `webpack.config.json`

`mkdir stimulus`

Copy `index.js` to the stimulus directory.

Create the `controllers` directory inside the `stimulus` directory.

### 2.5.2 Install NPM

For windows use this command line tool [npm-windows-upgrade](https://github.com/felixrieseberg/npm-windows-upgrade). Chocolatey installs a very old NPM version.

Upgrade NPM: 
`npm install -g npm@latest`

or try the most recent version: 
`npm install -g npm@next`


#### 2.5.3 Install Webpack, Babel, polyfills and Stimulus-starter

Since NPM 5.7.0, install from lock-file only:
```bash
npm ci
```

To upgrade `package-lock.json` and the project run `npm install`

### 2.6 Make javascript modifications/additions

Webpack can watch files and recompile whenever they change. Start the webpack watcher with this command:
```bash
npm start
```
Write your Stimulus controllers in the `stimulus/controllers` directory.

I noticed that when `Visual studio code` is running, the Webpack watcher didn't pick up the changes. Stop `VS code` when running the Webpack watcher.

[More info](https://stimulusjs.org)

