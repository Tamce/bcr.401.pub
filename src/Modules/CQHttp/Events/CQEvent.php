<?php
namespace App\Modules\CQHttp\Events;

use Exception;

abstract class CQEvent
{
    protected $time;
    protected $data;

    static public function createFromArray(array $data)
    {
        if ($data['post_type'] == 'message') {
            if ($data['message_type'] == 'group') {
                return new CQGroupMessageEvent($data);
            }
            return new CQPrivateMessageEvent($data);
        }
        return new CQNullEvent($data);
    }

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->time = @$data['time'];
    }

    public function eventType()
    {
        if ($this->rawData('post_type') == 'message') {
            if ($this->rawData('message_type') == 'private') {
                return 'message.private';
            }
            if ($this->rawData('message_type') == 'group') {
                return 'message.group';
            }
        }
        return 'unknown';
    }

    public function rawData($key = null)
    {
        if (is_null($key))
            return $this->data;
        else
            return $this->data[$key] ?? null;
    }

    public function time()
    {
        return date('Y-m-d H:i:s', $this->time);
    }


    protected $response = ['at_sender' => false];
    public function getResponse()
    {
        if ($this->isDebug()) {
            $this->autoEscape(true);
            $this->reply("raw msg:\n".$this->rawData('raw_message')."\n========\n\nresponse:\n".json_encode($this->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), false);
        }
        return $this->response;
    }

    protected $debug = false;
    public function setDebug($debug = true)
    {
        $this->debug = $debug;
    }

    public function isDebug()
    {
        return $this->debug;
    }

    public function reply($text = null, $append = true)
    {
        if (is_null($text)) {
            return $this->response['reply'] ?? '';
        }

        if (!$append) {
            $this->response['reply'] = $text;
        } else {
            $this->response['reply'] = $this->reply().$text;
        }
        return $this;
    }

    public function autoEscape($bool = null)
    {
        if (is_null($bool)) {
            return $this->response['auto_escape'] ?? false;
        }

        $this->response['auto_escape'] = $bool;
        return $this;
    }
}
