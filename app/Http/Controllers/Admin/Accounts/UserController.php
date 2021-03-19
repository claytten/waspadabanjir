<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Users\Requests\CreateUserRequest;
use App\Models\Users\Requests\UpdateUserRequest;
use App\Models\Users\Repositories\UserRepository;
use App\Models\Users\Repositories\Interfaces\UserRepositoryInterface;

use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tools\UploadableTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use UploadableTrait;
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
        UserRepositoryInterface $userRepository
    )
    {
        // Spatie ACL
        $this->middleware('permission:admin-list',['only' => ['index']]);
        $this->middleware('permission:admin-create', ['only' => ['create','store']]);
        $this->middleware('permission:admin-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:admin-delete', ['only' => ['destroy']]);

        $this->userRepo = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->userRepo->listUsers()->sortBy('name')->take(5);
        return view('admin.accounts.admin.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('admin.accounts.admin.create', compact('roles'));
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
        }
        if($request->is_active == "on") {
            $data['is_active'] = 1;
        } else {
            $data['is_active'] = 0;
        }
        $checking = $this->userRepo->createUser($data);

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

        return view('admin.accounts.admin.edit',compact('user','roles'));
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
        $data = $request->except('_token','_method');
        $user = $this->userRepo->findUserById($id);
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email,'.$id],
            'role' => ['required']
        ]);

        if(!empty($request->password) && !empty($request->password_confirmation)) {
            $request->validate([
                'password' => ['required', 'string', 'min:5', 'confirmed']
            ]);
            $data['password'] = Hash::make($request->password);
        } else {
            $data['password'] = $user->password;
        }

        if($request->is_active == "on") {
            $data['is_active'] = 1;
        } else {
            $data['is_active'] = 0;
        }
        $userRepo = new UserRepository($user);
        if ($request->hasFile('image') && $request->file('image') instanceof UploadedFile) {
            if(!empty($user->image)) {
                $userRepo->deleteFile($user->image);
            }
            $data['image'] = $this->userRepo->saveCoverImage($request->file('image'));
        }
        $userRepo->updateUser($data);

        return redirect()->route('admin.admin.edit', $id)->with([
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
            $users->is_active = false;
            $users->save();
            $message = 'User successfully blocked';
        } else if( $request->user_action == 'restore') {
            $users->is_active = true;
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
            'user_status' => $users->is_active
        ]);
    }
}
