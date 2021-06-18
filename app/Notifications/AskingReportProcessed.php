<?php

namespace App\Notifications;

use App\Broadcasting\Messages\WhatsAppMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Broadcasting\WhatsAppChannel;
use App\Models\Users\User;

class AskingReportProcessed extends Notification
{
  use Queueable;

  public $user;

  public function __construct(User $user)
  {
    $this->user = $user;
  }
  
  public function via($notifiable)
  {
    return [WhatsAppChannel::class];
  }
  
  public function toWhatsApp($notifiable)
  {
    return (new WhatsAppMessage)->content($this->user->body);
  }
}