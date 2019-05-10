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
- The controllers have a life-cycle and are automatically connected and disconnected if the DOM-tree is modified.


## 1.2 About the demo

What the demo demonstrates: 
- Partial page rendering with [Stimulus](https://stimulusjs.org)
- The 'partials' are HTML snippets loaded from the server via ajax calls. These snippets are used to modify the page.
- The loaded HTML snippets can contain references to Stimulus controllers. These are automatically connected. If they are removed from the page, they are automatically disconnected. In the demo, the area below the 'number of prizes' is dynamically changed, and it contains another Stimulus controller to update the prize. The area also contains input fields. Entered form data is kept when the area is updated.


Stimulus needs Node to built the resulting javascript file. It generates stimulus_v1_0.js. The Node Package Manager (NPM) creates a subdirectory `node_modules` with zillions of files. The good news is: these are only needed during development, not in production. 
- Babel is used, so modern javascript can be used en is transpiled to support browsers
- Webpack is used to watch for saved changes and update automatically. With webpack and a stimulus helper we also don't need to register new controllers.


- Only a few commands are needed to install and run it locally, because the example doesn't use a database
- I took the css files from the CakePHP starter project and only made additions to the style.css
- The same code used to generate the HTML snippets is also used when generating the page initially and after form submission.
- To support Internet Explorer 11, the [Stimulus polyfill](https://stimulusjs.org/handbook/installing#browser-support) is used.
- In the loader-controller the fetch API is used for the ajax calls. To support Internet Explorer 11, the whatwg/fetch polyfill is used.
- In the HTML the names of the referenced stimulus controllers contain two dashes. That is because I made controller subdirectories for common- and page-specific controllers, and / maps to -- in the [Stimulus filename mapping](https://stimulusjs.org/handbook/installing#controller-filenames-map-to-identifiers)
- The nginx configuration (`config/nginx.conf`) is made for production use: HTTP traffic is redirected to HTTPS, the browser is instructed to cache static assets, dynamic content is not cached and security headers are set.
- The CakePHP SecurityComponent is enabled. This gives a few challanges when used with client-code: it doesn't the submitted data when fields are dynamically added are removed, hidden field values are not supposed to change, the form action url cannot be changed. Luckely these can selectively be unlocked, and this is happens in the beforeFilter of the example1controller.
- Don't bother to make funny remarks in the live-demo, because the data is only stored in the session. That's why a src/Form/Example1Form extending App\Form is used; it contains the field definitions and form validation. If you use a database, you won't need this, because then you will have the tables and entities. Define the tables (following CakePHP naming conventions) in the database, including primary, foreign and unique keys, and let `cake bake all` generate all the initial code.
- Sorry, but I didn't make unit tests.
- Fork my github repository for improvements, other examples, or as a starter.


# 2 Installation

## 2.1 Install PHP 7 and CakePHP

1. Install [PHP](https://www.php.net/manual/en/install.php) and enable/install the INTL extension
2. Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.

## 2.2 Clone/Fork this example project 

```bash
git clone https://github.com/nico-amsterdam/cakephp-stimulus-example1

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


#### 2.5.3 Install Webpack, Babel, polyfills and Stimulus-starter

Since NPM 5.7.0, install from lock-file only:
```bash
npm ci
```

To upgrade `package-lock.json` and the project run `npm install`

# 3 Make javascript modifications/additions

Webpack can watch files and recompile whenever they change. Start the webpack watcher with this command:
```bash
npm start
```
Write your Stimulus controllers in the `stimulus/controllers` directory.

[More info](https://stimulusjs.org/handbook/introduction)

I noticed that when `Visual Studio Code` is running, the Webpack watcher didn't pick up the changes, so exit `Visual Studio Code` when you make changes in the Stimulus controllers.

