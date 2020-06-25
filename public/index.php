<?php
use Illuminate\Http\Request;

require __DIR__.'/../src/bootstrap.php';

app()->instance(Request::class, Request::createFromGlobals());
$response = app('router')->dispatch($app->get(Request::class));
$response->send();
