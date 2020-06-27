<?php
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

require __DIR__.'/../src/bootstrap.php';

app()->instance(Request::class, Request::createFromGlobals());
try {
    $response = app('router')->dispatch($app->get(Request::class));
} catch (NotFoundHttpException $e) {
    $response = Response::create('<h1><center>404 Not Found</center></h1>', 404);
}
$response->send();
