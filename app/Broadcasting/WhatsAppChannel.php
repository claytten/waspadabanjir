<?php

namespace App\Broadcasting;

use Illuminate\Notifications\Notification;
use Twilio\Rest\Client;

class WhatsAppChannel
{
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toWhatsApp($notifiable);


        $to = $notification->subscriber->routeNotificationForWhatsApp();
        $from = config('services.twilio.whatsapp_from');


        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));

        $response = $twilio->messages->create('whatsapp:' . $to, [
            "from" => 'whatsapp:' . $from,
            "body" => $message->content
        ]);

        return $response;
    }
}