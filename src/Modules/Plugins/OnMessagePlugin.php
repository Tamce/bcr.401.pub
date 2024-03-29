<?php
namespace App\Modules\Plugins;

use App\Modules\CQHttp\CQHttp;
use App\Modules\CQHttp\Events\CQEvent;
use App\Modules\CQHttp\Events\CQMessageEvent;

abstract class OnMessagePlugin
{
    protected $listen = 'private';
    protected $commands = [];

    public function register(CQHttp $cq)
    {
        if (empty($this->commands)) {
            return;
        }

        if (empty($this->listen)) {
            $this->listen = '*';
        }
        $cq->on("message.$this->listen", [$this, $this->listen == '*' ? 'handleWild' : 'handle']);
    }

    public function handle(CQMessageEvent $e)
    {
        $msg = $e->getRawMessage();
        if (strlen($msg) > 6 and substr($msg, 0, 6) == '%debug') {
            $msg = substr($msg, strpos($msg, '%', 1));
            $e->setDebug(true);
        }
        foreach ($this->commands as $key => $handler) {
            if ($key[0] == '^') {
                $reg = "/$key/s";
            } else {
                $reg = '/^'. ($_ENV['CMD_PREFIX'] ?? '') . $key . '/s';
            }

            if (preg_match($reg, $msg, $matches)) {
                array_shift($matches);
                if ($e->isDebug()) {
                    $e->reply("[debug:calling $handler]");
                }
                if (is_callable([$this, $handler])) {
                    return app()->call([$this, $handler], $matches);
                }
                break;
            }
        }
        if ($e->isDebug()) {
            $e->reply("[debug:match done]");
        }
    }

    public function handleWild($name, $e)
    {
        $result = [];
        foreach ($e as $ev) {
            $result[] = $this->handle($ev);
        }
        return $result;
    }
}
