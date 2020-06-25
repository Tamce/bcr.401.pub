<?php
use Illuminate\Container\Container;
use Illuminate\Http\Response;

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

function json($data, $status = 200, $headers = [])
{
    $response = Response::create(json_encode($data), $status, $headers);
    return $response->withHeaders(['Content-Type' => 'application/json']);
}

function storage($path = null)
{
    $path = '/'.ltrim($path ?? '', '/');
    return realpath(dirname(__FILE__).'/../storage') . $path;
}
