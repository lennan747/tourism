<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderReplied extends Notification
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        //
        $this->reply = $order;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function via($notifiable)
    {
        // 开启通知的频道
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $order = $this->order;
        //$link =  $topic->link(['#reply' . $this->reply->id]);

        // 存入数据库里的数据
        return $order;
    }
}
