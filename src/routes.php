<?php

use Illuminate\Http\Request;

$route = app('router');

$route->get('/', function (Request $request) {
    return 'Hello';
});