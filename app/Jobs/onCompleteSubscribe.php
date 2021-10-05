<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;

class onCompleteSubscribe implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private $message, $recipient;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($subscriber)
  {
    $this->message = "Halo kak {$subscriber->name}, Terima Kasih Telah Berlangganan! Kamu dapat mengikuti info terkini tentang banjir di Kabupaten Klaten dari waspadabanjir.web.id. Silahkan ketik MENU untuk melihat menu utama.";
		$this->recipient = $subscriber['phone'];
  }

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$twilio_whatsapp_number = config('services.twilio.whatsapp_from');
		$account_sid = config('services.twilio.sid');
		$auth_token = config('services.twilio.token');

		$client = new Client($account_sid, $auth_token);
    $client->messages->create('whatsapp:' . $this->recipient, [
      "from" => 'whatsapp:' . $twilio_whatsapp_number,
      "body" => strval($this->message)
    ]);
	}
}
