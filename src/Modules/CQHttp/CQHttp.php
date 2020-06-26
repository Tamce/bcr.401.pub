<?php
namespace App\Modules\CQHttp;

use App\Modules\CQHttp\Events\CQEvent;
use App\Modules\CQHttp\Events\CQGroupMessageEvent;
use App\Modules\CQHttp\Events\CQPrivateMessageEvent;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Events\Dispatcher;

class CQHttp
{
    protected $event;
    protected $apiBase;
    protected $client;
    public function __construct(Dispatcher $e)
    {
        $this->event = $e;
        $this->client = new Client([
            'base_uri' => rtrim($_ENV['CQHTTP_API'] ?? 'localhost:5700', '/')
        ]);
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
        app()->alias(CQEvent::class, CQPrivateMessageEvent::class);
        app()->alias(CQEvent::class, CQGroupMessageEvent::class);
        $this->event->dispatch($e->eventType(), $e);
        return $e->getResponse();
    }

    public function sendMessage($type, $targetId, $message, $autoEscape = false)
    {
        if (!in_array($type, ['user', 'group'])) {
            throw new Exception('Send Message Type Error!');
        }

        try {
            $request = [
                "$type" => $targetId,
                'message' => $message,
                'auto_escape' => $autoEscape,
            ];
            $response = $this->client->post('send_msg', [
                'json' => $request,
                'timeout' => 5,
            ]);
            $data = json_decode($response->getBody()->getContents());
            if (empty(@$data['retcode'])) {
                app('logger')->warning('Unexpected cqhttp api response.', [
                    'api' => 'send_msg',
                    'request' => $request,
                    'response' => $response->getBody()->getContents(),
                ]);
                return -1;
            }
            return $data['retcode'];
        } catch (Exception $e) {
            app('logger')->warning('Exception when calling cqhttp api.', [
                'api' => 'send_msg',
                'request' => $request,
                'exception' => $e->getMessage(),
            ]);
            return -1;
        }
    }

    public function sendPrivateMessage($qq, $message, $autoEscape = false)
    {
        return $this->sendMessage('user', $qq, $message, $autoEscape);
    }

    public function sendGroupMessage($groupId, $message, $autoEscape = false)
    {
        return $this->sendMessage('group', $groupId, $message, $autoEscape);
    }

    /**
     * Return the global instance
     *
     * @return CQHttp
     */
    static public function instance()
    {
        return app(CQHttp::class);
    }
}