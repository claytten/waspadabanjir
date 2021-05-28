<?php

namespace App\Models\Subscribers\Repositories;

use App\Models\Subscribers\Exceptions\CreateSubscribeInvalidArgumentException;
use Jsdecena\Baserepo\BaseRepository;
use App\Models\Subscribers\Subscribe;
use App\Models\Subscribers\Exceptions\SubscribeNotFoundException;
use App\Models\Subscribers\Repositories\Interfaces\SubscribeRepositoryInterface;
use App\Models\Tools\UploadableTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Twilio\Rest\Client;

class SubscribeRepository extends BaseRepository implements SubscribeRepositoryInterface
{
    use UploadableTrait;

    /**
     * SubscribeRepository constructor.
     *
     * @param Subscribe $subscribe
     */
    public function __construct(Subscribe $subscribe)
    {
        parent::__construct($subscribe);
        $this->model = $subscribe;
    }

    /**
     * List all the subscribes
     *
     * @param string $order
     * @param string $sort
     *
     * @return Collection
     */
    public function listSubscribes(string $order = 'id', string $sort = 'desc', $except = []) : Collection
    {
        return $this->model->orderBy($order, $sort)->get()->except($except);
    }

    /**
     * Create the Subscribe
     *
     * @param array $data
     *
     * @return Subscribe
     */
    public function createSubscribe(array $data): Subscribe
    {
        try {
            if(isset($data['phone'])) {
                if(substr($data['phone'],0,3) == '+62') {
                    $data['phone'] = preg_replace("/^0/", "+62", $data['phone']);
                } else if(substr($data['phone'],0,1) == '0') {
                    $data['phone'] = preg_replace("/^0/", "+62", $data['phone']);
                } else {
                    $data['phone'] = "+62".$data['phone'];
                }
            }
            return $this->model->create($data);
        } catch (QueryException $e) {
            throw new SubscribeNotFoundException($e);
        }
    }

    /**
     * Find the subscribe by id
     *
     * @param int $id
     *
     * @return Subscribe
     */
    public function findSubscribeById(int $id): Subscribe
    {
        try {
            return $this->findOneOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new SubscribeNotFoundException;
        }
    }

    /**
     * Find the subscribe by phone number
     * 
     * @param string $phone
     * 
     */
    public function findSubscriberByPhone(string $phone)
    {
      try {
        return $this->model->where('phone', $phone)->first();
      } catch(ModelNotFoundException $e) {
        throw new SubscribeNotFoundException($e);
      }
    }

    /**
     * Filtering subscriber by address
     */
    public function findSubscriberByAddress(int $id): Collection
    {
      return $this->model->where('address', $id)->get();
    }

    /**
     * Find All unique Regency by subsriber address
     * 
     */
    public function findRegencyBySubscriber()
    {
      $subscribers = $this->listSubscribes()->sortBy('name')->where('status', 1);
      $regency = array();
      foreach($subscribers as $item) {
        array_push($regency, array(
          'id'  => $item->regency->id,
          'name'=> $item->regency->name,
          'province' => $item->regency->province->name
        ));
      }
      return array_unique($regency, SORT_REGULAR);
    }

    /**
     * Update subscribe
     *
     * @param array $params
     *
     * @return bool
     */
    public function updateSubscribe(array $params): bool
    {
        if(isset($params['phone'])) {
            if(substr($params['phone'],0,3) == '+62') {
                $params['phone'] = preg_replace("/^0/", "+62", $params['phone']);
            } else if(substr($params['phone'],0,1) == '0') {
                $params['phone'] = preg_replace("/^0/", "+62", $params['phone']);
            } else {
                $params['phone'] = "+62".$params['phone'];
            }
        }
        return $this->model->update($params);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function deleteSubscribe() : bool
    {
        return $this->delete();
    }

    /**
     * Sends a WhatsApp message  to user using
     * @param string $message Body of sms
     * @param string $recipient Number of recipient
     */
    public function sendWhatsAppMessage(string $message, string $recipient) : bool
    {
      try {
        $twilio_whatsapp_number = config('services.twilio.whatsapp_from');
        $account_sid = config('services.twilio.sid');
        $auth_token = config('services.twilio.token');

        $client = new Client($account_sid, $auth_token);
        $response = $client->messages->create($recipient, array('from' => "whatsapp:$twilio_whatsapp_number", 'body' => $message));
        if($response->status === "queued") {
          return true;
        } else {
          return false;
        }
      } catch(QueryException $e) {
        throw new CreateSubscribeInvalidArgumentException($e);
      }
    }

    /**
     * Default Menu WhatsApp Message
     * @param string $name name of subscriber
     * @return string
     */
    public function defaultMenu(string $name = '') : string
    {
      $menu = collect([
        [
          "body" => "A.Pencarian banjir berdasarkan Kecamatan"
        ],
        [
          "body" => "B.Daftar banjir terkini seluruh Kabupaten Klaten",
        ],
        [
          "body" => "C.Lapor (Kejadian banjir / kritik & saran / tanya)",
        ],
        [
          "body" => "D.Berhenti langganan"
        ]
      ]);
      $nameTeam = config('app.name');
      $message = "--MENU UTAMA--\nHalo Kak {$name}, Kami dari {$nameTeam} memiliki beberapa portal informasi yang bisa kamu akses. Apa yang ingin kamu ketahui?\n";
      foreach($menu as $item) {
        $message .= "{$item['body']}\n";
      }
      $message .= "\n\nBalas *SATU HURUF* saja yaa; A,B,C,D dan seterusnya";
      return $message;
    }
}
