<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class onAskingReportToAdmin implements ShouldQueue,ShouldBeUnique
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private $message, $recipient;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($user)
  {
    $this->message = $user['body'];
		$this->recipient = $user['phone'];
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
