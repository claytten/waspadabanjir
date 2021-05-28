<?php

namespace App\Http\Controllers\Admin\Subscribers;

use App\Models\Users\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\Subscribers\Repositories\Interfaces\SubscribeRepositoryInterface;
use App\Models\Address\Regencies\Repositories\Interfaces\RegencyRepositoryInterface;
use App\Models\Address\Districts\Repositories\Interfaces\DistrictRepositoryInterface;
use App\Models\Maps\Fields\Repositories\Interfaces\FieldRepositoryInterface;
use App\Models\Reports\Repositories\Interfaces\ReportRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\Subscribers\Repositories\SubscribeRepository;
use App\Models\Subscribers\Subscribe;
use App\Notifications\SubscribeProcessed;
use App\Notifications\SubscribeBroadcastProcessed;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SubscribeController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * @var SubscribeRepositoryInterface
     */
    private $subscribeRepo;

    /**
     * @var RegencyRepositoryInterface
     */
    private $regencyRepo;

    /**
     * @var DistrictRepositoryInterface
     */
    private $districtRepo;

    /**
     * @var FieldRepositoryInterface
     */
    private $fieldRepo;

    /**
     * @var ReportRepositoryInterface
     */
    private $reportRepo;

    /**
     * Subscriber Controller Constructor
     *
     * @param SubscribeRepositoryInterface $SubscribeRepository
     * @return void
     */
    public function __construct(
      UserRepositoryInterface $userRepository,
      SubscribeRepositoryInterface $subscribeRepository,
      RegencyRepositoryInterface $regencyRepository,
      DistrictRepositoryInterface $districtRepository,
      FieldRepositoryInterface $fieldRepository,
      ReportRepositoryInterface $reportRepository
    ) {
        $this->middleware('permission:subscriber-list',['only' => ['index']]);
        $this->middleware('permission:subscriber-create', ['only' => ['create','store']]);
        $this->middleware('permission:subscriber-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:subscriber-delete', ['only' => ['destroy']]);
        // binding repository
        $this->userRepo = $userRepository;
        $this->subscribeRepo = $subscribeRepository;
        $this->regencyRepo = $regencyRepository;
        $this->districtRepo = $districtRepository;
        $this->fieldRepo = $fieldRepository;
        $this->reportRepo = $reportRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      if ($request->ajax()) {
          $subscribe = $this->subscribeRepo->findSubscribeById($request->id);
          $subscribeRepo = new SubscribeRepository($subscribe);
          $subscribeRepo->updateSubscribe([
              'status'    => $request->status,
          ]);
          $address[] = array(
              "regency_name"  => $subscribe->regency->name,
              "province_name" => $subscribe->regency->province->name
          );

          return response()->json([
              'code'  => 200,
              'status'=> 'success',
              'data'  => $subscribe,
              'address'=> $address[0]
          ]);
      }
      $subscribers = $this->subscribeRepo->listSubscribes()->sortBy('name');
      $cacheSub = Cache::has('adminWA') ? Cache::get('adminWA') : null;
      
      return view('admin.subscribers.index', compact('subscribers', 'cacheSub'));
    }

    /**
     * Sending Personal Broadcast 
     *
     * @return \Illuminate\Http\Response
     */
    public function personalBroadcast(Request $request)
    {
      $to = "whatsapp:".$request->phoneTo;
      $message = $request->body;
      $sendMessage = $this->subscribeRepo->sendWhatsAppMessage($message, $to);

      return response()->json([
        'code'    => 200,
        'success' => $sendMessage
      ]);
    }

    /**
     * Sending Multiple Message
     * 
     * @return \Illuminate\Http\Response
     */
    public function multipleBroadcast(Request $request)
    {
      if($request->type === 'all') {
        $subscribers = $this->subscribeRepo->listSubscribes()->sortBy('name');
      } else {
        $subscribers = $this->subscribeRepo->findSubscriberByAddress($request->regency_id);
      }
      
      foreach($subscribers->where('status', 1) as $item) {
        $item->body = $request->body;
        $item->notify(new SubscribeBroadcastProcessed($item));
      }

      return response()->json([
        'code'  => 200,
        'status'=> 'success',
      ]);
    }

    /**
     * Getting id and name regency by subscriber
     *
     * @return \Illuminate\Http\Response
     */
    function getRegency(Request $request) {
      return response()->json([
        'code'  => 200,
        'success'=> true,
        'data'  => $this->subscribeRepo->findRegencyBySubscriber()
      ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('_token', '_method');
        $subscribe = $this->subscribeRepo->createSubscribe($data);
        $address[] = array(
            "regency_name"  => $subscribe->regency->name,
            "province_name" => $subscribe->regency->province->name
        );
        $subscribe->notify(new SubscribeProcessed($subscribe));

        return response()->json([
          'code'  => 200,
          'status'=> 'success',
          'data'  => $subscribe,
          'address'=> $address[0]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subscribe = $this->subscribeRepo->findSubscribeById($id);
        return view('admin.subscribers.edit', compact('subscribe'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->except('_token', '_method');
        $subscribe = $this->subscribeRepo->findSubscribeById($id);
        $subscribeRepo = new SubscribeRepository($subscribe);

        $subscribeRepo->updateSubscribe($data);
        $address[] = array(
            "regency_name"  => $subscribe->regency->name,
            "province_name" => $subscribe->regency->province->name
        );

        return response()->json([
            'code'  => 200,
            'status'=> 'success',
            'data'  => $subscribe,
            'address'=> $address[0],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subscribe = $this->subscribeRepo->findSubscribeById($id);
        $subscribeRepo = new SubscribeRepository($subscribe);
        $subscribeRepo->deleteSubscribe();

        return response()->json([
            'code'  => 200,
            'status'=> 'success'
        ]);
    }

    public function listenToReplies(Request $request) {
      $from = $request->input('From');
      $body = $request->input('Body');
      try {
        $admin = Cache::rememberForever('adminWA', function () use ($request) {
          return $this->userRepo->findUserByUsername('superadmin');
        });
        $role = $admin->role;
        if($admin->$role->phone === ltrim($from, 'whatsapp:')) {
          $message = strval($this->adminMenu($admin, $role, $from, $body));
        } else {
          $message = strval($this->guestMenu($from, $body));          
        }
        $this->subscribeRepo->sendWhatsAppMessage($message, $from);
      } catch (RequestException $th) {
        $response = json_decode($th->getResponse()->getBody());
        $this->subscribeRepo->sendWhatsAppMessage($response->message, $from);
      }
      return;
    }

    private function adminMenu($admin, $role, $from, $body) {
      if($body == 'MENU') {
        $message = strval($this->userRepo->defaultMenu($admin->$role->name));
      } else {
          $message = "Silahkan gunakan kata *MENU* untuk melihat semua perintah 😃";
      }
      return $message;
    }

    private function guestMenu($from, $body) {
      $findNumber = $this->subscribeRepo->findSubscriberByPhone(ltrim($from, 'whatsapp:'));
      if(!empty($findNumber)) {
        $subRepo = new SubscribeRepository($findNumber);
        if ($findNumber->status === 1) {
          $answerID = Cache::has($from) ? Cache::get($from) : null;
          if ($answerID !== null) {
            switch($answerID[0]) {
              case 'menu':
                Cache::forget($from);
                $message = strval($this->subscribeRepo->defaultMenu($findNumber->name));
                break;
              case 'a':
                $message = strval($this->listDistrictMenu($from, $body));
                break;
              case 'c':
                if(strtolower($body) === '1' || (isset($answerID[1]) ? $answerID[1] : null) === '1') {
                  $message = strval($this->OptionReportMenu('1', $from, $body, $findNumber));
                } elseif (strtolower($body) === '2' || (isset($answerID[1]) ? $answerID[1] : null) === '2') {
                  $message = strval($this->OptionReportMenu('2', $from, $body, $findNumber));
                } elseif (strtolower($body) === '3' || (isset($answerID[1]) ? $answerID[1] : null) === '3') {
                  $message = strval($this->OptionReportMenu('3', $from, $body, $findNumber));
                } elseif (strtolower($body) === 'menu') {
                  Cache::forget($from);
                  $message = $this->subscribeRepo->defaultMenu('');
                } else {
                  $message = "Maaf kata kunci yang kamu gunakan tidak ada. Silahkan ulangi lagi sesuai daftar yang ada pada pelaporan.";
                }
                break;
              default:
                $message = "Kata kunci tidak sesuai. Silahkan ketik *menu* untuk menampilkan daftar kata kunci layanan.";
                break;
            }
          } else {
            $message = $this->listDefaultMenu($from, $findNumber, $body, $subRepo);
          }
        } elseif ($body == 'subscribe') {
          $subRepo->updateSubscribe([ 'status' => 1 ]);
          $message = "Selamat datang kembali Kak {$findNumber->name}. Silahkan ketik *menu* untuk menampilkan daftar layanan";
        } else {
          $message = "Nomor Whatsapp kamu dalam status tidak berlangganan. Silahkan ketik *subscribe* untuk kembali berlangganan";
        }
        
      } else {
        $answerID = Cache::has($from) ? Cache::get($from) : null;
        if(strtolower($body) === 'mulai' || $answerID !== null) {
          if((!empty($answerID[0]) ? $answerID[0] : null) === 'mulai') {
            $message = strval($this->registerStep1($from, $body, $answerID));
          } else {
            Cache::put($from, array('mulai'), 600);
            $message = "Siapa namamu ?.\n\nContohnya : Samsudi Yahya";
          }
        } else {
          $formReport = route('form.report');
          $message = "Selamat datang di portal informasi banjir di Kabupaten Klaten. Silahkan jawab pertanyaan berikut untuk melengkapi proses registrasi. \n\nKetik *mulai* untuk memulai proses registrasi. \n🛑 Jika ada pertanyaan bisa melakukan pertanyaan di link berkut ({$formReport})";
        }
      }
      return $message;
    }

    private function listDefaultMenu($from, $findNumber, $body, $subRepo) {
      switch(strtolower($body)) {
        case 'menu':
          if (Cache::has($from)) {
            Cache::forget($from);
          }
          $message = $this->subscribeRepo->defaultMenu($findNumber->name);
          break;
        case 'a':
          Cache::forget($from);
          Cache::put($from, array('a'), 600);
          $message = strval($this->districtRepo->listDistrictByRegencies());
          break;
        case 'b':
          Cache::forget($from);
          $message = strval($this->fieldRepo->listFieldsAndGeo());
          break;
        case 'c':
          Cache::forget($from);
          Cache::put($from, array('c'), 600);
          $message = "--MENU PILIHAN LAPORAN--\n\nBerikut merupakan pilihan dalam opsi laporan. Jenis laporan apa yang kamu inginkan?\n1. Kritik & Saran\n2. Laporan Kejadian Banjir\n3. Pertanyaan\n\nBalas *SATU ANGKA* saja yaa.";
          break;
        case 'd':
          Cache::forget($from);
          $subRepo->updateSubscribe([ 'status' => 0]);
          $message = "Selamat tinggal kak {$findNumber->name}. jika ingin berlangganan kembali ketik *subscribe*.";
          break;
        default:
          Cache::forget($from);
          $message = "Kata kunci tidak sesuai. Silahkan ketik *menu* untuk menampilkan daftar kata kunci layanan.";
          break;
      }
      return $message;
    }

    private function listDistrictMenu($from, $body) {
      if(strtolower($body) === 'menu') {
        Cache::forget($from);
        return $message = $this->subscribeRepo->defaultMenu('');
      }
      $address = $this->districtRepo->createListDistrictMenu();
      $verifyInput = array_search($body, array_column($address, 'id'));
      if( $verifyInput !== false) {
        $fields = $this->fieldRepo->findFieldByAddress(strtolower($address[$verifyInput]['name']))->where('status', 1);
        if(count($fields) > 0) {
          $countFields = count($fields);
          $message = "Terdapat {$countFields} yang ada pada {$address[$verifyInput]['name']}:";
          $coundColumn = 1;
          foreach($fields as $item) {
            $detailFields = route('maps.show', $item['id']);
            $message .= "\n{$coundColumn}. Daerah Kecamatan {$item['name']}";
            $message .= "\n  -Waktu & Tgl Kejadian : {$item['time']}, {$item['date']}";
            $message .= "\n  -Detail Lokasi : {$item['locations']}";
            $message .= "\n  -Deskripsi : {$item['description']}";
            $message .= "\n  -Detail informasi peta dan gambar : {$detailFields}\n";
            $coundColumn += 1;
          }
        } else {
          $message = "Di Kecamatan {$address[$verifyInput]['name']} tidak ada banjir.";
        }
        $message .= "\n\nSilahkan ketik *menu* jika ingin menampilkan daftar layanan portal banjir. ";
        return $message;
      } else {
        return "Mohon maaf, pilihanmu tidak ada dalam daftar diatas. silahkan pilih sesuai petunjuk.";
      }
    }

    private function OptionReportMenu($answerID, $from, $body, $findNumber) {
      if(strtolower($body) === 'menu') {
        Cache::forget($from);
        return $message = $this->subscribeRepo->defaultMenu('');
      }

      if(isset(Cache::get($from)[1])) {
        return $this->responseReportMenu(Cache::get($from)[1], $from, $body, $findNumber);
      }

      Cache::forget($from);
      Cache::put($from, array('c', $answerID), 600);
      $message = "Silahkan ketik isi laporan yang kamu inginkan. Namun kamu hanya bisa mengirim pesan teks saja yaa.";
      return $message;
    }

    private function responseReportMenu($answerID, $from, $body, $findNumber) {
      $data= array([
        'name'        => $findNumber['name'],
        'report_type' => ($answerID === '1') ? 'suggest' : ( $answerID === '2' ? 'report' : 'ask'),
        'phone'       => ($answerID === '2') ? $findNumber['phone'] : null,
        'address'     => ($answerID === '2') ? $findNumber['address'] : null,
        'message'     => $body,
        'from'        => $from
      ]);

      return $this->reportRepo->storeReportWhatsapp($data[0]);
    }

    private function registerStep1($from, $body, $answerID) {
      if(!empty($answerID[1]) ? $answerID[1] : null === 'name') {
        return $this->registerStep2($from, $body, $answerID);
      }
      Cache::forget($from);
      $data = array(
        'name'  => $body
      );
      Cache::put($from, array('mulai', 'name'), 600);
      Cache::put("store_{$from}", $data, 600);
      $message = "Dimana kota yang kamu tinggali sekarang?.\n\nContohnya : Klaten";
      $message .= "\n\nKetik *kembali* jika ingin kembali ke pengisian sebelumnya.";
      return $message;
    }

    private function registerStep2($from, $body, $answerID) {
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
      $setRegency = $this->regencyRepo->findRegencyByName(strtolower($body));
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
        $this->subscribeRepo->createSubscribe($data);
        Cache::forget($from);
        Cache::forget("store_{$from}");
        Cache::forget("address_{$from}");
        return "Terima Kasih sudah melakukan registrasi. ketik *menu* untuk menampilkan daftar layanan informasi";
      } else {
        return "Kota yang dicari tidak ditemukan. silahkan ketik ulang kota kamu.";
      }
    }

    private function registerStepOption($from, $body) {
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
      $this->subscribeRepo->createSubscribe($data);
      Cache::forget($from);
      Cache::forget("store_{$from}");
      Cache::forget("address_{$from}");
      return "Hore. Data telah tersimpan. ketik *menu* untuk melihat daftar layanan.";
    }
}
