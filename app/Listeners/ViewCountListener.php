<?php

namespace App\Listeners;

use App\Events\ViewCountEvent;
use App\Models\Works;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ViewCountListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ViewCountEvent  $event
     * @return void
     */
    public function handle(ViewCountEvent $event)
    {
        $data   = $event->data;
        $v_type = $data['v_type'] ?? 0; // 1 作品增加浏览量
        $v_id   = $data['v_id'] ?? 0;   // 对应ID，如 v_type = 1，则为 作品id

        if (!$v_type || !$v_id)
            return;

        switch ($v_type) {
            // 作品
            case 1:
                Works::query()->where('id', $v_id)->increment('view_count');
                break;
        }
    }
}
