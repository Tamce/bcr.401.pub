<?php
require __DIR__.'/../vendor/autoload.php';

use App\Modules\Session;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\RoutingServiceProvider;
use Symfony\Component\Dotenv\Dotenv;

require 'helpers.php';

$app = app();
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/../.env');
$app->instance('dotenv', $dotenv);

app()->singleton(GuzzleHttp\Client::class, function () {
    return new GuzzleHttp\Client;
});
app()->alias(GuzzleHttp\Client::class, 'http.client');

app()->singleton(Session::class, function () {
    return new Session;
});
app()->alias(Session::class, 'session');

with(new EventServiceProvider($app))->register();
with(new RoutingServiceProvider($app))->register();

require 'routes.php';
