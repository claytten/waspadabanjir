<?php

namespace App\Notifications;

use App\Broadcasting\Messages\WhatsAppMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Broadcasting\WhatsAppChannel;
use App\Models\Subscribers\Subscribe;

class SubscribeProcessed extends Notification implements ShouldQueue
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
    return (new WhatsAppMessage)->content("Halo kak {$this->subscriber->name}, Terima Kasih Telah Berlangganan! Kamu dapat mengikuti info terkini tentang banjir di Kabupaten Klaten dari BPBD Kabupaten Klaten. Silahkan ketik MENU untuk melihat menu utama.");
  }
}
