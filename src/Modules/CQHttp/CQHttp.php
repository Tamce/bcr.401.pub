<?php
namespace App\Modules\CQHttp;

use App\Modules\CQHttp\Events\CQEvent;
use Illuminate\Events\Dispatcher;

class CQHttp
{
    protected $event;
    public function __construct(Dispatcher $e)
    {
        $this->event = $e;
    }

    public function listen($events, $handler)
    {
        $this->event->listen($events, $handler);
    }

    public function on($events, $handler)
    {
        $this->event->listen($events, $handler);
    }

    public function dispatchEventFromArray(array $data)
    {
        $e = CQEvent::createFromArray($data);
        app()->instance(CQEvent::class, $e);
        $this->event->dispatch($e->eventType(), $e);
        return $e->getResponse();
    }
}