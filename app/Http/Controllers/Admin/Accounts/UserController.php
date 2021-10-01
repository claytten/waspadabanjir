<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Users\Requests\CreateUserRequest;
use App\Models\Users\Requests\UpdateUserRequest;
use App\Models\Users\User;
use App\Models\Users\Repositories\UserRepository;
use App\Models\Users\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\Address\Provinces\Repositories\Interfaces\ProvinceRepositoryInterface;
use App\Models\Accounts\Admins\Repositories\AdminRepository;
use App\Models\Accounts\Employees\Repositories\EmployeeRepository;

use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tools\UploadableTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    use UploadableTrait;
    /**
     * @var ProfinceRepositoryInterface
     */
    private $provinceRepo;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * Admin Controller Constructor
     *
     * @param UserRepositoryInterface $userRepository
     * @return void
     */
    public function __construct(
        ProvinceRepositoryInterface $provinceRepository,
        UserRepositoryInterface $userRepository
    )
    {
        // Spatie ACL
        $this->middleware('permission:admin-list',['only' => ['index']]);
        $this->middleware('permission:admin-create', ['only' => ['create','store']]);
        $this->middleware('permission:admin-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:admin-delete', ['only' => ['destroy']]);

        $this->userRepo = $userRepository;
        $this->provinceRepo = $provinceRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (Cache::has('adminWA')) {
                Cache::forget('adminWA');
            }
            $user =  Cache::rememberForever('adminWA', function () use ($request) {
                return $this->userRepo->findUserById($request->id);
            });
            $data[] = array(
                "id"  => $user->id,
                "name" => $user->name
            );
            return response()->json([
                'code'      => 200,
                'status'    => 'success',
                'data'      => $data[0]
            ]);
        }

        $users = $this->userRepo->listUsers()->sortBy('name');
        if(Cache::has('adminWA')) {
            $statusWA = Cache::get('adminWA');
        } else {
            $statusWA['id'] = null;
        }
        return view('admin.accounts.admin.index', compact('users', 'statusWA'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        $provinces = $this->provinceRepo->listProvinces()->sortBy('name');
        return view('admin.accounts.admin.create', compact('roles', 'provinces'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $data = $request->except('_token','_method');

        if ($request->hasFile('image') && $request->file('image') instanceof UploadedFile) {
            $data['image'] = $this->userRepo->saveCoverImage($request->file('image'));
        } else {
            $data['image'] = null;
        }
        if($request->is_active == "on") {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        $this->userRepo->createUser($data);

        return redirect()->route('admin.admin.index')->with([
            'status'    => 'success',
            'message'   => 'Create Admin successful!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $roles = Role::pluck('name', 'name')->all();
        $user = $this->userRepo->findUserById($id);
        $provinces = $this->provinceRepo->listProvinces()->sortBy('name');
        if(substr($user['phone'],0,3) == '+62') {
            $user['phone'] = substr($user['phone'],3);
        }

        return view('admin.accounts.admin.edit',compact('user','roles', 'provinces'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $data = $request->except('_token','_method');
        $user = $this->userRepo->findUserById($id);
        $userRepo = new UserRepository($user);

        if ($request->ajax()) {
            $chkOldPassword = Hash::check($request->oldpassword, $user->password);
            if ($chkOldPassword) {
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json([
                    'status'    => 'success',
                    'message'   => 'Password successfully changed!'
                ]);
            }
            return response()->json([
                'status'    => 'success',
                'message'   => 'Please check again old password / matching new password!'
            ]);
        }

        if($request->status == "on") {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }

        if ($request->hasFile('image') && $request->file('image') instanceof UploadedFile) {
            if(!empty($user->image)) {
                $userRepo->deleteFile($user->image);
            }
            $data['image'] = $this->userRepo->saveCoverImage($request->file('image'));
        } else {
            $data['image'] = $user->image;
        }

        $userRepo->updateUser($data);
        $user->assignRole($data['role']);

        return redirect()->route('admin.admin.index')->with([
            'status'    => 'success',
            'message'   => 'Update Account successful!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $users = $this->userRepo->findUserById($id);
        $user = new UserRepository($users);
        $message = '';
        if($request->user_action == 'block'){
            $users->status = false;
            $users->save();
            $message = 'User successfully blocked';
        } else if( $request->user_action == 'restore') {
            $users->status = true;
            $users->save();
            $message = 'User successfully restored';
        } else {
            if(!empty($users->image) ) {
                $user->deleteFile($users->image);
            }
            $message = 'User successfully destroy';
            $user->deleteUser();
        }

        return response()->json([
            'status'      => 'success',
            'message'     => $message,
            'user_status' => $users->status
        ]);
    }
}
