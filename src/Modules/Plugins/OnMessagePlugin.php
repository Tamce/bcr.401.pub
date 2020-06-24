<?php
namespace App\Modules\Plugins;

use App\Modules\CQHttp\CQEvent;
use App\Modules\CQHttp\CQHttp;
use App\Modules\CQHttp\CQPrivateMessageEvent;

abstract class OnMessagePlugin
{
    protected $type = 'private';
    protected $commands = [];

    public function register(CQHttp $cq)
    {
        if (empty($this->commands)) {
            return;
        }

        if (empty($this->type)) {
            $this->type = '*';
        }
        $cq->on("message.$this->type", [$this, $this->type == '*' ? 'handleWild' : 'handle']);
    }

    public function handle(CQPrivateMessageEvent $e)
    {
        foreach ($this->commands as $key => $handler) {
            $reg = '/'. ($_ENV['CMD_PREFIX'] ?? '') . $key . '/s';
            if (preg_match($reg, $e->getRawMessage(), $matches)) {
                array_shift($matches);
                if (is_callable([$this, $handler])) {
                    app()->call([$this, $handler], $matches);
                }
                break;
            }
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