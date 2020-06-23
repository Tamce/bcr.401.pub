<?php
require __DIR__.'/../vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\RoutingServiceProvider;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Get the app container
 *
 * @param string|null $key
 * @return Container|Object
 */
function app($key = null)
{
    static $container = null;
    if (empty($container)) {
        $container = new Container;
    }
    if (empty($key)) {
        return $container;
    } else {
        return $container->get($key);
    }
}

$app = app();
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/../.env');
$app->instance('dotenv', $dotenv);

with(new EventServiceProvider($app))->register();
with(new RoutingServiceProvider($app))->register();

require 'routes.php';

$response = app('router')->dispatch(Request::createFromGlobals());
$response->send();
