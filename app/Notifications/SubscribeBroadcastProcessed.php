<?php

namespace App\Notifications;

use App\Broadcasting\Messages\WhatsAppMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Broadcasting\WhatsAppChannel;
use App\Models\Subscribers\Subscribe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SubscribeBroadcastProcessed extends Notification implements ShouldQueue
{
  use Queueable;

  public $subscriber;

  public function __construct(Subscribe $subscriber)
  {
    $this->subscriber = $subscriber;
  }
  
  public function via($notifiable)
  {
    return [WhatsAppChannel::class];
  }
  
  public function toWhatsApp($notifiable)
  {
    Log::debug($notifiable);
    return (new WhatsAppMessage)->content($this->subscriber->body);
  }
}
