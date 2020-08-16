<?php

namespace App\Modules\CQHttp;

use App\Modules\CQHttp\Events\CQMessageEvent;
use Illuminate\Support\Facades\DB;

class Context
{
    protected $prefix;
    protected $id;

    static public function createFromMessageEvent(CQMessageEvent $event)
    {
        if ($event->isGroupMessage()) {
            return new self($event->getMessageSourceId());
        } else {
            return new self('private');
        }
    }

    /**
     * 构造一个用于隔离的 Context
     *
     * @param string $id 该环境的唯一标识
     */
    public function __construct(string $id)
    {
        $this->id = $id;
        $this->prefix = "{$id}_";
    }

    /**
     * 返回在当前 context 下的表格 Query Builder
     *
     * @param string $table 原始表名
     * @return Illuminate\Database\Query\Builder
     */
    public function table(string $table)
    {
        return DB::table($this->prefix . $table);
    }
}
