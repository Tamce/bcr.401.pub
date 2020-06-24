<?php
require __DIR__.'/../vendor/autoload.php';

use Illuminate\Events\EventServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\RoutingServiceProvider;
use Symfony\Component\Dotenv\Dotenv;

require 'helpers.php';

$app = app();
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/../.env');
$app->instance('dotenv', $dotenv);

with(new EventServiceProvider($app))->register();
with(new RoutingServiceProvider($app))->register();

require 'routes.php';

$app->instance(Request::class, Request::createFromGlobals());
$response = app('router')->dispatch($app->get(Request::class));
$response->send();
