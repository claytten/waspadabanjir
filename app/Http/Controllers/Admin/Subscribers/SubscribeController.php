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
        $item->phoneTo = 'subscriber';
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
        $subscribe->phoneTo = 'subscriber';
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
      if(Cache::has('dailyUsersUsage')) {
        Cache::increment('dailyUsersUsage');
      } else {
        Cache::forever('dailyUsersUsage', 1);
      }
      try {
        $admin = Cache::rememberForever('adminWA', function () use ($request) {
          return $this->userRepo->findUserByEmail('superadmin@gmail.com');
        });
        if($admin->phone === ltrim($from, 'whatsapp:')) {
          $message = strval($this->adminMenu($admin, $from, $body));
        } else {
          $message = strval($this->guestMenu($from, $body));          
        }
        if(strlen($message) > 1600) {
          $message = str_split($message, 1600);
          foreach($message as $item) {
            $this->subscribeRepo->sendWhatsAppMessage($item, $from);
          }
        } else {
          $this->subscribeRepo->sendWhatsAppMessage($message, $from);
        }
        
      } catch (RequestException $th) {
        $response = json_decode($th->getResponse()->getBody());
        $this->subscribeRepo->sendWhatsAppMessage($response->message, $from);
      }
      return;
    }

    private function adminMenu($admin, $from, $body) {
      switch(strtolower($body)) {
        case 'menu':
          $message = strval($this->userRepo->defaultMenu($admin->name));
          break;
        case 'a':
          $dailyUsage = (Cache::get('dailyUsersResult') === null) ? '0' : Cache::get('dailyUsersResult');
          $monthlyUsage = (Cache::get('monthlyUsersResult') === null) ? '0' : Cache::get('monthlyUsersResult');
          $yearlyUsage = (Cache::get('yearlyUsersResult') === null) ? '0' : Cache::get('yearlyUsersResult');
          $dailyReport = $this->reportRepo->dailyReport(date('Y-m-d')) > 0 ? strval($this->reportRepo->dailyReport(date('Y-m-d'))) : '0';
          $message = strval($this->subscribeRepo->reportAdmin($dailyUsage, $monthlyUsage, $yearlyUsage, $dailyReport));
          break;
        case 'b':
          $message = strval($this->reportRepo->reportWhatsapp());
          break;
        case 'c':
          $message = strval($this->subscribeRepo->subscribersWhatsapp());
          break;
        default:
          $message = "Kata kunci tidak sesuai. Silahkan ketik *menu* untuk menampilkan daftar kata kunci layanan.";
          break;
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
                $message = strval($this->subscribeRepo->listDistrictMenu($from, $body, $this->districtRepo, $this->fieldRepo));
                break;
              case 'c':
                if(strtolower($body) === '1' || (isset($answerID[1]) ? $answerID[1] : null) === '1') {
                  $message = strval($this->subscribeRepo->OptionReportMenu('1', $from, $body, $findNumber, $this->reportRepo));
                } elseif (strtolower($body) === '2' || (isset($answerID[1]) ? $answerID[1] : null) === '2') {
                  $message = strval($this->subscribeRepo->OptionReportMenu('2', $from, $body, $findNumber, $this->reportRepo));
                } elseif (strtolower($body) === '3' || (isset($answerID[1]) ? $answerID[1] : null) === '3') {
                  $message = strval($this->subscribeRepo->OptionReportMenu('3', $from, $body, $findNumber, $this->reportRepo));
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
            $message = $this->subscribeRepo->listDefaultMenu($from, $findNumber, $body, $this->districtRepo, $this->fieldRepo);
          }
        } elseif ($body == 'subscribe') {
          $this->subscribeRepo->updateSubscribe([ 'status' => 1 ]);
          $message = "Selamat datang kembali Kak {$findNumber->name}. Silahkan ketik *menu* untuk menampilkan daftar layanan";
        } else {
          $message = "Nomor Whatsapp kamu dalam status tidak berlangganan. Silahkan ketik *subscribe* untuk kembali berlangganan";
        }
        
      } else {
        $answerID = Cache::has($from) ? Cache::get($from) : null;
        if(strtolower($body) === 'mulai' || $answerID !== null) {
          if((!empty($answerID[0]) ? $answerID[0] : null) === 'mulai') {
            $message = strval($this->subscribeRepo->registerStep1($from, $body, $answerID, $this->regencyRepo));
          } else {
            Cache::put($from, array('mulai'), 600);
            $message = "Siapa namamu ?\n\nContohnya : Samsudi Yahya";
          }
        } else {
          $formReport = route('form.report');
          $message = "Selamat datang di portal informasi banjir di Kabupaten Klaten. Silahkan jawab pertanyaan berikut untuk melengkapi proses registrasi. \n\nKetik *mulai* untuk memulai proses registrasi. \n🛑 Jika ada pertanyaan bisa melakukan pertanyaan di link berkut ({$formReport})";
        }
      }
      return $message;
    }
}
