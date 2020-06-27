<?php
require __DIR__.'/../vendor/autoload.php';

use App\Modules\CQHttp\CQHttp;
use App\Modules\Session;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\RoutingServiceProvider;
use Symfony\Component\Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require 'helpers.php';

$app = app();
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/../.env');
$app->instance('dotenv', $dotenv);

/* Session Helper */
$app->singleton(Session::class, function () {
    $session = new Session;
    $session->start();
    return $session;
});
$app->alias(Session::class, 'session');

/* Router */
with(new EventServiceProvider($app))->register();
with(new RoutingServiceProvider($app))->register();

/* Logger */
app()->singleton('logger', function () {
    $logger = new Logger('default');
    $logger->pushHandler(new StreamHandler(storage('default.log')));
    if ($_ENV['APP_DEBUG'] ?? false) {
        $logger->pushHandler(new StreamHandler('php://stdout'));
    }
    return $logger;
});

/* CQHttp Helper */
app()->singleton(CQHttp::class, function () {
    return new CQHttp(app()->make(Dispatcher::class));
});
app()->alias(CQHttp::class, 'cqhttp');

/* Eloquent */
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => $_ENV['DB_DRIVER'],
    'host'      => $_ENV['DB_HOST'],
    'database'  => $_ENV['DB_DATABASE'],
    'username'  => $_ENV['DB_USERNAME'],
    'password'  => $_ENV['DB_PASSWORD'],
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);
$capsule->setEventDispatcher(app()->make(Dispatcher::class));
$capsule->setAsGlobal();
$capsule->bootEloquent();
$app->singleton('db', function () use ($capsule) {
    return $capsule->getConnection();
});
DB::setFacadeApplication($app);


require 'routes.php';
