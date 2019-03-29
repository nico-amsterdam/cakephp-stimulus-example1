#!/usr/bin/php -q
<?php
// Check platform requirements
require dirname(__DIR__) . '/config/requirements.php';
require dirname(__DIR__) . '/vendor/autoload.php';

use App\Application;
use Cake\Console\CommandRunner;

// delete cached routes
$file = dirname(__DIR__) . '/tmp/cache/myapp_cake_routes_route_collection';
if (file_exists($file)) {
   unlink($file);
}

// Build the runner with an application and root executable name.
$runner = new CommandRunner(new Application(dirname(__DIR__) . '/config'), 'cake');
exit($runner->run($argv));
