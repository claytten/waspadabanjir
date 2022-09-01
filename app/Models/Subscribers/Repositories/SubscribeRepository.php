<?php

namespace App\Models\Subscribers\Repositories;

use App\Jobs\onAskingReportToAdmin;
use App\Jobs\onResponServiced;
use App\Models\Subscribers\Exceptions\CreateSubscribeInvalidArgumentException;
use Jsdecena\Baserepo\BaseRepository;
use App\Models\Subscribers\Subscribe;
use App\Models\Subscribers\Exceptions\SubscribeNotFoundException;
use App\Models\Subscribers\Repositories\Interfaces\SubscribeRepositoryInterface;
use Carbon\Carbon;
use App\Models\Tools\UploadableTrait;
use App\Models\Tools\PhoneFilterTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SubscribeRepository extends BaseRepository implements SubscribeRepositoryInterface
{
    use UploadableTrait, PhoneFilterTrait;

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
            $data['phone'] = $this->filterPhone($data['phone']);
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
        $params['phone'] = $this->filterPhone($params['phone']);
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
    public function sendWhatsAppMessage(string $message, string $recipient)
    {
      try {
        onResponServiced::dispatch($message, $recipient);
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
          "body" => "D.Ubah informasi pengguna",
        ],
        [
          "body" => "E.Berhenti langganan"
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

    /**
     * Listing Main Menu for Guest
     * 
     * @param string $from
     * @param object $findNumber
     * @param string $body
     * @param object $districtRepo
     * @param object $fieldRepo
     * 
     * @return string
     */
    public function listDefaultMenu(string $from, object $findNumber, string $body, object $districtRepo, object $fieldRepo) : string
    {
      switch(strtolower($body)) {
        case 'menu':
          if (Cache::has($from)) {
            Cache::forget($from);
          }
          $message = $this->defaultMenu($findNumber->name);
          break;
        case 'a':
          Cache::forget($from);
          Cache::put($from, array('a'), 600);
          $message = strval($districtRepo->listDistrictByRegencies());
          break;
        case 'b':
          Cache::forget($from);
          $message = strval($fieldRepo->listFieldsAndGeo());
          break;
        case 'c':
          Cache::forget($from);
          Cache::put($from, array('c'), 600);
          $message = "--MENU PILIHAN LAPORAN--\n\nBerikut merupakan pilihan dalam opsi laporan. Jenis laporan apa yang kamu inginkan?\n1. Kritik & Saran\n2. Laporan Kejadian Banjir\n3. Pertanyaan\n\nBalas *SATU ANGKA* saja yaa.";
          break;
        case 'd':
          Cache::forget($from);
          Cache::put($from, array('d'), 600);
          $message = "--Pengaturan Pengguna--\n\nBerikut merupakan pilihan dalam opsi pengaturan. Data apa yang kamu inginkan ubah?\n1. Nama\n2. Alamat\n\n\nBalas *SATU ANGKA* saja yaa.\nKetik *menu* jika ingin kembali.";
          break;
        case 'e':
          $subRepo = new SubscribeRepository($findNumber);
          $subRepo->updateSubscribe([ 'status' => 0 ]);
          Cache::forget($from);
          $message = "Selamat tinggal kak {$findNumber->name}. jika ingin berlangganan kembali ketik *subscribe*.";
          break;
        default:
          Cache::forget($from);
          $message = "Kata kunci tidak sesuai. Silahkan ketik *menu* untuk menampilkan daftar kata kunci layanan.";
          break;
      }
      return $message;
    }

    /**
     * List Whatsapp Subscribers
     * 
     * @return string
     */
    public function subscribersWhatsapp(): string
    {
      $subscribers = $this->model->whereDate('created_at', Carbon::today())->get();
      if(count($subscribers) > 0) {
        $message = "--MENU DAFTAR PENGGUNA ".Carbon::createFromFormat('Y-m-d H:i:s', Carbon::today())->format('d-m-Y')."--\nBerikut daftar pengguna Whatsapp di portal informasi banjir : \n";
        $coundColumn = 1;
        foreach($subscribers as $item) {
          $addressSubsribe = "{$item->regency->name}, {$item->regency->province->name}";
          $statusReport = ($item['status'] === 1 ? 'Verified' : 'Non-Verified');
          $message .= "\n{$coundColumn}. Nama pengguana : {$item['name']}";
          $message .= "\n  -Alamat : {$addressSubsribe}";
          $message .= "\n  -Nomor : {$item['phone']}";
          $message .= "\n  -Status : {$statusReport}";
          $message .= "\n  -Tanggal join: {$item['created_at']->format('d/m/Y')}\n";
          $coundColumn += 1;
        }
      } else {
        $message = "Sementara belum ada pengguna.";
      }

      return $message;
    }

    /**
     * Processing report users whatsapp to admin
     * 
     * @param int $daily
     * @param int $month
     * @param int $year
     * 
     * @return string
     */
    public function reportAdmin(int $daily, int $month, int $year, int $dailyReport): string
    {
      $activeSub = !empty($this->listSubscribes()->sortBy('name')->where('status', 1)) 
                      ? strval(count($this->listSubscribes()->sortBy('name')->where('status', 1)))
                        : '0';
      $nonActiveSub = !empty($this->listSubscribes()->sortBy('name')->where('status', 0)) 
                      ? strval(count($this->listSubscribes()->sortBy('name')->where('status', 0)))
                        : '0';
      $message = "--MENU STATISTIK--\nBerikut daftar statistik penggunaan portal informasi banjir: \n";
      $message .= "\n1. Aktivitas pengguna whatsapp harian : {$daily} request";
      $message .= "\n2. Aktivitas pengguna whatsapp bulanan : {$month} request";
      $message .= "\n3. Aktivitas pengguna whatsapp tahunan : {$year} request";
      $message .= "\n4. Jumlah laporan harian : {$dailyReport} laporan";
      $message .= "\n5. Jumlah pengguna Aktif : {$activeSub} orang";
      $message .= "\n6. Jumlah pengguna Tidak Aktif: {$nonActiveSub} orang";
      return $message;
    }

    /**
     * Registry Step 1
     * 
     * @param string $from
     * @param string $body
     * @param array $answerID
     * @param object $regencyRepo
     * 
     * @return string
     */
    public function registerStep1(string $from, string $body, array $answerID, object $regencyRepo) : string
    {
      if(!empty($answerID[1]) ? $answerID[1] : null === 'name') {
        return $this->registerStep2($from, $body, $answerID, $regencyRepo);
      }
      Cache::forget($from);
      $data = array(
        'name'  => $body
      );
      Cache::put($from, array('mulai', 'name'), 600);
      Cache::put("store_{$from}", $data, 600);
      $message = "Dimana kota yang kamu tinggal sekarang?.\n\nContohnya : Klaten";
      $message .= "\n\nKetik *kembali* jika ingin kembali ke pengisian sebelumnya.";
      return $message;
    }

    /**
     * Registry Step 2
     * 
     * @param string $from
     * @param string $body
     * @param array $answerID
     * @param object $regencyRepo
     * @param object $subscribeRepo
     * 
     * @return string
     */
    private function registerStep2(string $from, string $body, array $answerID, object $regencyRepo) 
    {
      if(!empty($answerID[2]) ? $answerID[2] : null === 'address') {
        return $this->registerStepOption($from, $body);
      }
      if(strtolower($body) === 'kembali') {
        Cache::forget($from);
        Cache::forget("store_{$from}");
        Cache::put($from, array('mulai'), 600);
        return "Siapa namamu ?.\n\nContohnya : Samsudi Yahya";
      }
      $data = Cache::has("store_{$from}") ? Cache::get("store_{$from}") : [];
      $setRegency = $regencyRepo->findRegencyByName(strtolower($body));
      if(count($setRegency) > 0) {
        if(count($setRegency) > 1) {
          Cache::forget($from);
          Cache::put($from, array('mulai', 'name', 'address'), 600);
          $data = array();
          $countRegency = count($setRegency);
          $count = 1;
          $message = "Dalam proses pencarian nama kota kamu. Telah ditemukan {$countRegency} kemiripan: ";
          foreach($setRegency as $item) {
            array_push($data, array(
              "id"    => $item['id'],
              "name"  => $item['name'],
              "choose"=> $count,
            ));
            $message .= "\n[{$count}] {$item['name']}";
            $count += 1;
          }
          $message .= "\n\n Pilih salah satu diantara pilihan diatas dengan balas *SATU ANGKA* saja";
          $message .= "\n\n ketik *kembali* jika ingin kembali ke pencarian kota.";
          Cache::put("address_{$from}", $data, 600);
          return $message;
        }
        $setRegency = $setRegency->first();
        $data['address'] = $setRegency['id'];
        $data['phone'] = ltrim($from, 'whatsapp:');
        $this->createSubscribe($data);
        Cache::forget($from);
        Cache::forget("store_{$from}");
        Cache::forget("address_{$from}");
        return "Terima Kasih sudah melakukan registrasi. ketik *menu* untuk menampilkan daftar layanan informasi";
      } else {
        return "Kota yang dicari tidak ditemukan. silahkan ketik ulang kota kamu.";
      }
    }

    /**
     * Registry Step option
     * 
     * @param string $from
     * @param string $body
     * 
     * @return string
     */
    private function registerStepOption(string $from, string $body) 
    {
      if(strtolower($body) === 'kembali') {
        Cache::forget($from);
        Cache::put($from, array('mulai', 'name'), 600);
        $message = "Dimana kota yang kamu tinggali sekarang?.\n\nContohnya : Klaten";
        $message .= "\n\nKetik *kembali* jika ingin kembali ke pengisian sebelumnya.";
        return $message;
      }
      $regencies = Cache::get("address_{$from}");
      if($body > count($regencies) || $body < 0) {
        return "Mohon maaf, pilihanmu tidak ada dalam daftar diatas. silahkan pilih sesuai petunjuk.";
      }

      $data = Cache::has("store_{$from}") ? Cache::get("store_{$from}") : [];
      $data['address'] = $regencies[$body-1]['id'];
      $data['phone'] = ltrim($from, 'whatsapp:');
      $this->createSubscribe($data);
      Cache::forget($from);
      Cache::forget("store_{$from}");
      Cache::forget("address_{$from}");
      return "Hore. Data telah tersimpan. ketik *menu* untuk melihat daftar layanan.";
    }

    /**
     * District Menu
     * 
     * @param string $from
     * @param string $body
     * @param object $districtRepo
     * @param object $fieldRepo
     * 
     * @return string
     */
    public function listDistrictMenu(string $from, string $body, object $districtRepo, object $fieldRepo) :string
    {
      if(strtolower($body) === 'menu') {
        Cache::forget($from);
        return $message = $this->defaultMenu('');
      }
      $address = $districtRepo->createListDistrictMenu();
      $verifyInput = array_search($body, array_column($address, 'id'));
      if( $verifyInput !== false) {
        $fields = $fieldRepo->findFieldByAddress(strtolower($address[$verifyInput]['name']));
        if(count($fields) > 0) {
          $message = "Terdapat beberapa area di kecamatan {$address[$verifyInput]['name']}:";
          $coundColumn = 1;
          foreach($fields as $item) {
            if($item->field->status === 1) {
              $totalVictims = $item->field->deaths + $item->field->injured + $item->field->losts;
              $detailFields = route('maps.show', $item->field_id);
              $date_in = $fieldRepo->convertDateAttribute($item->field->date_in);
              $date_in_time = $fieldRepo->convertTimeAttribute($item->field->date_in);
              $date_out_time = ($item->field->date_out !== null ? $fieldRepo->convertTimeAttribute($item->field->date_out) : false);
              $date_out = $item->field->date_out !== null ? $date_out_time.' WIB, '.$fieldRepo->convertDateAttribute($item->field->date_out) : 'Sedang Berlangsung';
              $locationCount = $item->field->detailLocations->count();
              
              $message .= "\nArea banjir {$coundColumn}";
              $message .= "\n  -Jumlah Korban : {$totalVictims}";
              $message .= "\n  -Tanggal Awal Kejadian : {$date_in_time} WIB, {$date_in}";
              $message .= "\n  -Tanggal Akhir Kejadian : {$date_out}";
              $message .= "\n  -Jumlah Kelurahan yang terdampak: {$locationCount}";
              $message .= "\n  -Berita banjir lebih rinci: {$detailFields}";

              $coundColumn += 1;
            }
          }
        } else {
          $message = "Di Kecamatan {$address[$verifyInput]['name']} tidak ada banjir.";
        }
        $message .= "\n\nSilahkan ketik *menu* jika ingin menampilkan daftar layanan portal banjir. ";
        Cache::forget($from);
        return $message;
      } else {
        return "Mohon maaf, pilihanmu tidak ada dalam daftar diatas. silahkan pilih sesuai petunjuk.";
      }
    }

    /**
     * Option Report Menu for Guest
     * 
     * @param array $answerID
     * @param string $from,
     * @param string $body,
     * @param object $findNumber
     * @param object reportRepo
     * 
     * @return string
     */
    public function OptionReportMenu(string $answerID, string $from, string $body, object $findNumber, object $reportRepo): string 
    {
      if(strtolower($body) === 'menu') {
        Cache::forget($from);
        return $message = $this->defaultMenu($findNumber['name']);
      }

      if(isset(Cache::get($from)[1])) {
        return $this->responseReportMenu(Cache::get($from)[1], $from, $body, $findNumber, $reportRepo);
      }

      Cache::forget($from);
      Cache::put($from, array('c', $answerID), 600);
      $message = "Silahkan ketik isi laporan yang kamu inginkan. Namun kamu hanya bisa mengirim pesan teks saja yaa.";
      return $message;
    }

    private function responseReportMenu(string $answerID, string $from,string $body,object $findNumber, object $reportRepo)
    {
      $data= array([
        'name'        => $findNumber['name'],
        'report_type' => ($answerID === '1') ? 'suggest' : ( $answerID === '2' ? 'report' : 'ask'),
        'phone'       => ($answerID === '2') ? $findNumber['phone'] : null,
        'address'     => ($answerID === '2') ? $findNumber['address'] : null,
        'message'     => $body,
        'from'        => $from
      ]);

      if($data[0]['report_type'] === 'ask') {
        $user = Cache::get('adminWA');
        $date = Carbon::now()->setTimezone('Asia/Jakarta')->format('H:i, d-m-Y');
        $message = "Notifikasi Pertanyaan\n";
        $message .= "\n dari : {$findNumber['name']} ({$findNumber['phone']})";
        $message .= "\n Waktu pelaporan : {$date}";
        $message .= "\n Isi pertanyaan: {$body}";
        $user->body = strval($message);
        onAskingReportToAdmin::dispatch($user);
      } else if($data[0]['report_type'] === 'report') {
        $user = Cache::get('adminWA');
        $date = Carbon::now()->setTimezone('Asia/Jakarta')->format('H:i, d-m-Y');
        $message = "Notifikasi Laporan Banjir\n";
        $message .= "\n dari : {$findNumber['name']} ({$findNumber['phone']})";
        $message .= "\n Waktu pelaporan : {$date}";
        $message .= "\n Isi laporan: {$body}";
        $user->body = strval($message);
        onAskingReportToAdmin::dispatch($user);
      }

      return $reportRepo->storeReportWhatsapp($data[0]);
    }

    /**
     * Option Change Information User From WhatsApp
     * 
     * @param string $from
     * @param string $body
     * @param object $userRepo
     * 
     * @return string
     */
    public function optionChangeInformation(string $answerID, string $from, string $body, object $findNumber, object $regencyRepo): string
    {
      if(strtolower($body) === 'menu' || strtolower($body) === 'kembali') {
        Cache::forget($from);
        return $message = $this->defaultMenu($findNumber['name']);
      }

      if(isset(Cache::get($from)[1])) {
        return $this->responChangeInformation(Cache::get($from), $from, $body, $findNumber, $regencyRepo);
      }

      //findSubscriberByPhone
      Cache::forget($from);
      Cache::put($from, array('d', $answerID), 600);
      if($answerID === '1') {
        $message = "Silakan ketik nama yang baru.\n";
        $message .= "Contoh: *Samsudi Yahya*";
      } else if($answerID === '2') {
        $message = "Silakan ketik alamat yang baru.\n";
        $message .= "Contoh: *Klaten*";
      } else {
        $message = "Mohon maaf, pilihanmu tidak ada dalam daftar diatas.";
      }
      $message .= "\n\nSilahkan ketik *menu* atau *kembali* jika ingin menampilkan daftar layanan portal banjir. ";
      return $message;
    }

    /**
     * Respon While Change Information User from WhatsApp
     * 
     * 
     */
    private function responChangeInformation(array $answerID, string $from, string $body, object $findNumber, object $regencyRepo)
    {
      $message = '';
      if($answerID[1] === '1') {
        $findNumber->name = $body;
        $findNumber->save();
        $message .= "Hore. Nama kamu telah diperbaharui menjadi {$findNumber->name}.\n\nketik *menu* atau *kembali* untuk melihat daftar layanan portal banjir.";
      } elseif($answerID[1] === '2') {
        if(!empty($answerID[2]) ? $answerID[2] : null === 'address') {
          $message .= $this->changeAddressOption($from, $body, $findNumber);
        }

        $setRegency = $regencyRepo->findRegencyByName(strtolower($body));
        if(count($setRegency) > 0) {
          if(count($setRegency) > 1) {
            Cache::forget($from);
            Cache::put($from, array('d', '2', 'address'));
            $data = array();
            $countRegency = count($setRegency);
            $count = 1;
            $message = "Dalam proses pencarian nama kota kamu. Telah ditemukan {$countRegency} kemiripan: ";
            foreach($setRegency as $item) {
              array_push($data, array(
                "id"    => $item['id'],
                "name"  => $item['name'],
                "choose"=> $count,
              ));
              $message .= "\n[{$count}] {$item['name']}";
              $count += 1;
            }
            $message .= "\n\n Pilih salah satu diantara pilihan diatas dengan balas *SATU ANGKA* saja";
            $message .= "\n\n ketik *kembali* jika ingin kembali ke pencarian kota.";
            Cache::put("address_{$from}", $data);
            return $message;
          }
          $setRegency = $setRegency->first();
          $findNumber->address = $setRegency['id'];
          $findNumber->save();
          Cache::forget($from);
          Cache::forget("address_{$from}");
          $message .= "Hore. Alamat kamu telah diperbaharui menjadi {$setRegency['name']}.\n\nketik *menu* atau *kembali* untuk melihat daftar layanan portal banjir.";
        } else {
          $message .= "Kota yang dicari tidak ditemukan. silahkan ketik ulang kota kamu.";
        }
      }
      return $message;
    }

    private function changeAddressOption(string $from, string $body, object $findNumber)  {
      if(strtolower($body) === 'kembali') {
        Cache::forget($from);
        Cache::put($from, array('d', '2'));
        $message = "Silakan ketik alamat yang baru.\n";
        $message .= "Contoh: *Klaten*";
        return $message;
      }
      $regencies = Cache::get("address_{$from}");
      if($body > count($regencies) || $body < 0) {
        return "Mohon maaf, pilihanmu tidak ada dalam daftar diatas. silahkan pilih sesuai petunjuk.";
      }

      $findNumber->address = $regencies[$body - 1]['id'];
      $findNumber->save();
      Cache::forget($from);
      Cache::forget("address_{$from}");
      return "Hore. Alamat kamu telah diperbaharui menjadi {$regencies[$body -1]['name']}.\n\nketik *menu* atau *kembali* untuk melihat daftar layanan portal banjir.";
    }
}
