<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$route = app('router');

$route->namespace('App\\Controllers')->group(function () use ($route) {

    $route->get('/', function (Request $request) {
        return 'Hello';
    });
    $route->post('/cq_event', 'EventHandler@handle');

});