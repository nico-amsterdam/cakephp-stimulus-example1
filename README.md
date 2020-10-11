# 1. CakePHP with Stimulus.js

A demo showing [Stimulus](https://stimulusjs.org) and [CakePHP](https://cakephp.org) 3.x working together.
As example, it shows partial page rendering; replacing/adding server-side generated HTML snippets on the web page.

[See it here](https://cakephp-stimulusjs.herokuapp.com)

Deploy it yourself: [![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/nico-amsterdam/cakephp-stimulus-example1)

Note that there is a [branch](https://github.com/nico-amsterdam/cakephp-stimulus-example1/tree/db_example1) of this demo using mysql/mariadb.


## 1.1 Why Stimulus

What Stimulus does can be done with other javascript libraries as well, but what I like is:
- The HTML is not cluttered with script code or vague class names
- It's clear which controllers are used and where (data-controller attribute)
- It's clear where the controller will make changes (data-target attribute)
- It's clear what triggers the controller (data-action attribute)
- The controllers have a life-cycle and are automatically connected and disconnected if the DOM-tree is modified.


## 1.2 About the demo

What the demo demonstrates: 
- Partial page rendering with [Stimulus](https://stimulusjs.org)
- The 'partials' are HTML snippets loaded from the server via ajax calls. These snippets are used to modify a part of the page.
- The loaded HTML snippets can contain references to Stimulus controllers. These are automatically connected. If they are removed from the page, they are automatically disconnected. In the demo, the area below the 'number of prizes' is dynamically changed, and it contains another Stimulus controller to instantly copy the prize text. The area contains input fields, and the entered data is kept when the area is updated; or actually the entered data is send to the server and the returned HTML snippet contains these values.
- The PHP code used to generate the HTML snippets, is also used when generating the whole page initially and after form submission. They are CakePHP elements.

Easy installation:
- Only a few commands are needed to install and run it locally. The example doesn't use a database


Running Stimulus:

Stimulus needs Node.js to build the resulting javascript file; stimulus_v1_0.js. When installing packages the Node Package Manager (NPM) creates a subdirectory `node_modules` with zillions of files. The good news is: these are only needed during development, not in production. 
- Babel is used, so modern javascript can be used and is transpiled to support browsers.
- Webpack is used to watch for saved changes in Stimulus controllers and update automatically the stimulus_v1_0.js file. With webpack and a stimulus helper we also don't need to register new controllers.

Styling:

- I took the css files from the [CakePHP Application Skeleton](https://github.com/cakephp/app) and only made additions to the style.css

What are those hyphens/dashes in the controller names?

- In the HTML / `Example1/index.ctp`, indeed the names of the referenced Stimulus controllers contain two dashes. I made subdirectories for common- and page-specific controllers, and / maps to -- in the [Stimulus filename to identifier mapping](https://stimulusjs.org/handbook/installing#controller-filenames-map-to-identifiers)

SecurityComponent:

- The CakePHP SecurityComponent is enabled. This gives a few challanges when used with client script code. The SecurityComponent rejects the submitted form data when fields are dynamically added or removed, hidden field values are changed, or a different form action url is used. Luckily these can selectively be unlocked, and this is happens in the beforeFilter of the Example1Controller.
- Don't bother to make funny remarks in the live demo, because the data is only stored in the session. That's why a src/Form/Example1Form extending App\Form is used; it contains the field definitions and form validation. If you use a database, you won't need this, because then you will have a model. See this [branch](https://github.com/nico-amsterdam/cakephp-stimulus-example1/tree/db_example1). Tip: define the tables (following CakePHP naming conventions) in the database, including primary, foreign and unique keys, and let `cake bake all` generate all the initial code.

Polyfills:
- To support Internet Explorer 11, the [Stimulus polyfill](https://stimulusjs.org/handbook/installing#browser-support) is used.
- In the loader_controller the fetch API is used for the ajax calls. To support Internet Explorer 11, the whatwg/fetch polyfill is used.

Nginx:
- The nginx configuration (`config/nginx.conf`) for Heroku is made for production use: HTTP traffic is redirected to HTTPS, the browser is instructed to cache static assets, dynamic content is not cached and security headers are set.
- The webpack.config.js uses a CompressionPlugin. This plugin produces stimulus_v1_0.js.gz, a compressed file of stimuls_v1_0.js. The idea is to combine this with the Nginx gzip_static setting, so Nginx doesn't have to compress this at runtime. However the Nginx server on Heroku doesn't contain the ngx_http_gzip_static_module module. On Ubuntu, this is not a problem and the gzip_static setting can be used. The CompressionPlugin is optional, and can be removed if you don't want the gz files.

Unit test:
- Uh... sorry, but I didn't make unit tests.

Fork my [github repository](https://github.com/nico-amsterdam/cakephp-stimulus-example1) for improvements, other examples, or as a starter.


# 2 Installation

## 2.1 Install PHP 7 and CakePHP

1. Install [PHP](https://www.php.net/manual/en/install.php) and enable/install the intl and mbstring extensions
2. Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.

## 2.2 Clone/Fork this example project 

```bash
git clone https://github.com/nico-amsterdam/cakephp-stimulus-example1
```

and install the PHP packages:
```bash
composer install
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


### 2.5.3 Install Webpack, Babel, polyfills and Stimulus-starter

Since NPM 5.7.0, install from lock-file only:
```bash
npm ci
```

To upgrade `package-lock.json` and the project run `npm install` instead.

# 3 Make javascript modifications/additions

Webpack can watch files and recompile whenever they change. Start the webpack watcher with this command:
```bash
npm start
```
Write your Stimulus controllers in the `stimulus/controllers` directory. You can also use one of the available [Stimulus plugins](https://stimawesome.com).

[Stimulus introduction](https://stimulusjs.org/handbook/introduction)

I noticed that when `Visual Studio Code` is running, the Webpack watcher didn't pick up the changes. In that case, exit `Visual Studio Code` when you make changes in the Stimulus controllers.

