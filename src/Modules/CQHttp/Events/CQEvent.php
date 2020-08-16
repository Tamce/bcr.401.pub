<?php

namespace App\Modules\CQHttp\Events;

abstract class CQEvent
{
    protected $time;
    protected $data;
    /**
     * 当前事件所在上下文
     *
     * @var App\Modules\CQHttp\Context
     */
    protected $context;

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
        $this->context;
    }

    /**
     * 返回当前事件所在的上下文
     *
     * @return App\Modules\CQHttp\Context
     */
    public function context()
    {
        return $this->context;
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
            $this->reply("raw msg:\n" . $this->rawData('raw_message') . "\n========\n\nresponse:\n" . json_encode($this->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), false);
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

    /**
     * 获取或者设置 fast response 内容
     *
     * @param string $text 若不传该参数，则返回当前的 response 内容
     * @param boolean $append 如为 false，则使用 $text 替换 response 内容，否则追加
     * @return CQEvent|string $this
     */
    public function reply($text = null, $append = true)
    {
        if (is_null($text)) {
            return $this->response['reply'] ?? '';
        }

        if (!$append) {
            $this->response['reply'] = $text;
        } else {
            $this->response['reply'] = $this->reply() . $text;
        }
        return $this;
    }

    /**
     * 获取或设置返回的 auto_escape 字段
     *
     * @param boolean $bool
     * @return CQEvent|boolean $this
     */
    public function autoEscape($bool = null)
    {
        if (is_null($bool)) {
            return $this->response['auto_escape'] ?? false;
        }

        $this->response['auto_escape'] = $bool;
        return $this;
    }
}
