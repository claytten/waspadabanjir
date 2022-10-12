<?php

namespace App\Http\Controllers\Admin\Subscribers;

use App\Models\Users\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\Subscribers\Repositories\Interfaces\SubscribeRepositoryInterface;
use App\Models\Address\Regencies\Repositories\Interfaces\RegencyRepositoryInterface;
use App\Models\Address\Districts\Repositories\Interfaces\DistrictRepositoryInterface;
use App\Models\Maps\Fields\Repositories\Interfaces\FieldRepositoryInterface;
use App\Models\Reports\Repositories\Interfaces\ReportRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Jobs\onCompleteSubscribe;
use App\Jobs\onSubscribeProcessing;
use App\Models\Subscribers\Repositories\SubscribeRepository;
use App\Models\Subscribers\Subscribe;
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
    $this->middleware('permission:subscriber-list', ['only' => ['index']]);
    $this->middleware('permission:subscriber-create', ['only' => ['create', 'store']]);
    $this->middleware('permission:subscriber-edit', ['only' => ['edit', 'update']]);
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
        'status' => 'success',
        'data'  => $subscribe,
        'address' => $address[0]
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
    $to = "whatsapp:" . $request->phoneTo;
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
    if ($request->type === 'all') {
      $subscribers = $this->subscribeRepo->listSubscribes()->sortBy('name');
    } else {
      $subscribers = $this->subscribeRepo->findSubscriberByAddress($request->regency_id);
    }

    foreach ($subscribers->where('status', 1) as $item) {
      $item->body = $request->body;
      onSubscribeProcessing::dispatch($item);
    }

    return response()->json([
      'code'  => 200,
      'status' => 'success',
    ]);
  }

  /**
   * Getting id and name regency by subscriber
   *
   * @return \Illuminate\Http\Response
   */
  function getRegency(Request $request)
  {
    return response()->json([
      'code'  => 200,
      'success' => true,
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
    $chkSub = $this->subscribeRepo->checkUniquePhone($data['phone']);
    if (!empty($chkSub)) {
      return response()->json([
        'code' => 200,
        'status' => 'error',
        'data'  => '',
        'message' => 'Nomor Sudah Digunakan. Silakan Coba Lagi.'
      ]);
    }
    $subscribe = $this->subscribeRepo->createSubscribe($data);
    $address[] = array(
      "regency_name"  => $subscribe->regency->name,
      "province_name" => $subscribe->regency->province->name
    );
    $subscribe->phoneTo = 'subscriber';
    onCompleteSubscribe::dispatch($subscribe);

    return response()->json([
      'code'  => 200,
      'status' => 'success',
      'message' => 'Data Subscriber telah dibuat!',
      'data'  => $subscribe,
      'address' => $address[0]
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
      'status' => 'success',
      'message' => 'Data Subscriber telah diperbaharui!',
      'data'  => $subscribe,
      'address' => $address[0],
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
      'status' => 'success'
    ]);
  }

  public function listenToReplies(Request $request)
  {
    $from = $request->input('From');
    $body = $request->input('Body');
    $nuMedia = $request->input('NumMedia');
    $media = $request->has('MediaUrl0') ? $request->input('MediaUrl0') : null;
    $ext = $request->has('MediaContentType0') ? $request->input('MediaContentType0') : null;
    try {
      $admin = Cache::rememberForever('adminWA', function () use ($request) {
        return $this->userRepo->findUserByEmail('superadmin@gmail.com');
      });
      if ($admin->phone === ltrim($from, 'whatsapp:')) {
        $message = strval($this->adminMenu($admin, $from, $body));
      } else {
        if (Cache::has('dailyUsersUsage')) {
          Cache::increment('dailyUsersUsage');
        } else {
          Cache::forever('dailyUsersUsage', 1);
        }
        $message = strval($this->guestMenu($from, $body, $nuMedia, $ext, $media));
      }
      if (strlen($message) > 1600) {
        $message = str_split($message, 1600);
        foreach ($message as $item) {
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

  private function adminMenu($admin, $from, $body)
  {
    switch (strtolower($body)) {
      case 'menu':
        $message = strval($this->userRepo->defaultMenu($admin->name));
        break;
      case 'a':
        $dailyUsage = Cache::get('dailyUsersUsage') === null ? '0' : Cache::get('dailyUsersUsage');
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

  private function guestMenu($from, $body, $nuMedia, $ext, $media)
  {
    $findNumber = $this->subscribeRepo->findSubscriberByPhone(ltrim($from, 'whatsapp:'));
    if (!empty($findNumber)) {
      if ($findNumber->status === 1) {
        $answerID = Cache::has($from) ? Cache::get($from) : null;
        if ($nuMedia !== null && $nuMedia > 0) { // if request from media (image, video, audio, document, stiker)
          if ($answerID !== null) { // its filtering for answering report 
            switch (count($answerID)) {
              case 1:
                $message = strval($this->subscribeRepo->filterFileMenu($nuMedia, $ext));
                break;
              case 2:
                $message = strval($this->subscribeRepo->filterFileMenu($nuMedia, $ext));
                break;
              case 3:
                $message = strval($this->subscribeRepo->filterFileMenu($nuMedia, $ext));
                break;
              case 4:
                if ($answerID[0] == 'c' && $answerID[1] == '2' && $answerID[3] === 'image' && strpos($ext, 'image') !== false) {
                  $nameFile = strval($this->subscribeRepo->uploadImageFromWA($from, $media, $ext));
                  $message = strval($this->subscribeRepo->responseReportMenu($answerID[1], $from, strval($answerID[2]), $findNumber, $this->reportRepo, $nameFile));
                } else {
                  $message = strval($this->subscribeRepo->filterFileMenu($nuMedia, $ext));
                }
                break;
              default:
                $message = strval($this->subscribeRepo->filterFileMenu($nuMedia, $ext));
                break;
            }
          } else {
            $message = strval($this->subscribeRepo->filterFileMenu($nuMedia, $ext));
          }
        } else {
          if ($answerID !== null) { // if request from texting and emoji
            switch ($answerID[0]) {
              case 'menu':
                Cache::forget($from);
                $message = strval($this->subscribeRepo->defaultMenu($findNumber->name));
                break;
              case 'a':
                $message = strval($this->subscribeRepo->listDistrictMenu($from, $body, $this->districtRepo, $this->fieldRepo));
                break;
              case 'c':
                if (strtolower($body) === '1' || (isset($answerID[1]) ? $answerID[1] : null) === '1') {
                  $message = strval($this->subscribeRepo->OptionReportMenu('1', $from, $body, $findNumber, $this->reportRepo));
                } elseif (strtolower($body) === '2' || (isset($answerID[1]) ? $answerID[1] : null) === '2') {
                  $message = strval($this->subscribeRepo->OptionReportMenu('2', $from, $body, $findNumber, $this->reportRepo));
                } elseif (strtolower($body) === '3' || (isset($answerID[1]) ? $answerID[1] : null) === '3') {
                  if ((isset($answerID[3]) ? $answerID[3] : null) === 'image') {
                    $message = strval("Silahkan kirim *Satu Foto* pendukung laporan.\nNamun kamu hanya bisa mengirim foto (.jpg|.jpeg|.png) kurang dari 2MB.\nJika mengirim foto lebih dari satu. Maka, foto yang pertama yang akan tersimpan.\n\nKetik *kembali* jika ingin mengubah isi laporan.\nKetik *menu* jika ingin kembali ke menu utama.");
                  } else {
                    $message = strval($this->subscribeRepo->OptionReportMenu('3', $from, $body, $findNumber, $this->reportRepo));
                  }
                } elseif (strtolower($body) === 'menu' || strtolower($body) === 'kembali') {
                  Cache::forget($from);
                  $message = $this->subscribeRepo->defaultMenu($findNumber['name']);
                } else {
                  $message = "Maaf kata kunci yang kamu gunakan tidak ada. Silahkan ulangi lagi sesuai daftar yang ada pada pelaporan.";
                }
                break;
              case 'd':
                if (strtolower($body) === '1' || (isset($answerID[1]) ? $answerID[1] : null) === '1') {
                  $message = strval($this->subscribeRepo->optionChangeInformation('1', $from, $body, $findNumber, $this->regencyRepo));
                  //$message = strval($this->subscribeRepo->OptionReportMenu('1', $from, $body, $findNumber, $this->reportRepo));
                } elseif (strtolower($body) === '2' || (isset($answerID[1]) ? $answerID[1] : null) === '2') {
                  $message = strval($this->subscribeRepo->optionChangeInformation('2', $from, $body, $findNumber, $this->regencyRepo));
                } elseif (strtolower($body) === '3' || (isset($answerID[1]) ? $answerID[1] : null) === '3') {
                  $message = 'kembali';
                } elseif (strtolower($body) === 'menu') {
                  Cache::forget($from);
                  $message = $this->subscribeRepo->defaultMenu($findNumber['name']);
                } else {
                  $message = "Maaf kata kunci yang kamu gunakan tidak ada. Silahkan ulangi lagi sesuai daftar yang ada pada pengaturan pengguna.";
                }
                break;
              default:
                $message = "Kata kunci tidak sesuai. Silahkan ketik *menu* untuk menampilkan daftar kata kunci layanan.";
                break;
            }
          } else {
            $message = $this->subscribeRepo->listDefaultMenu($from, $findNumber, $body, $this->districtRepo, $this->fieldRepo);
          }
        }
      } elseif ($body == 'subscribe') {
        $subRepo = new SubscribeRepository($findNumber);
        $subRepo->updateSubscribe(['status' => 1]);
        $message = "Selamat datang kembali Kak {$findNumber->name}. Silahkan ketik *menu* untuk menampilkan daftar layanan";
      } else {
        $message = "Nomor Whatsapp kamu dalam status tidak berlangganan. Silahkan ketik *subscribe* untuk kembali berlangganan";
      }
    } else {
      $answerID = Cache::has($from) ? Cache::get($from) : null;
      if (strtolower($body) === 'mulai' || $answerID !== null) {
        if ((!empty($answerID[0]) ? $answerID[0] : null) === 'mulai') {
          if($nuMedia !== null && $nuMedia > 0) {
            if(empty($answerID[1])) { // name condition
              $message = strval("Kata kunci tidak sesuai. Silakan ulangi mengirimkan nama lengkap kamu dengan benar.\n\nContohnya : Samsudi Yahya");
            } 
            if(!empty($answerID[1])) { // regency condition
              $message = strval("Kata kunci tidak sesuai. Silakan ulangi mengirimkan nama kota kamu tinggal sekarang.\n\nContohnya : Klaten\n\nKetik *kembali* jika ingin kembali ke pengisian sebelumnya.");
            }
            if(!empty($answerID[1]) && !empty($answerID[2])) { // regencies condition
              $message = strval("Mohon maaf, pilihanmu tidak ada dalam daftar diatas. silahkan pilih sesuai petunjuk.\n\n ketik *kembali* jika ingin kembali ke pencarian kota.");
            }
          } else {
            $message = strval($this->subscribeRepo->registerStep1($from, $body, $answerID, $this->regencyRepo, $nuMedia));
          }
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
