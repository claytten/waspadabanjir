<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Users\User;
use App\Models\Users\Requests\UpdateUserRequest;
use App\Models\Users\Requests\ResetPasswordRequest;
use App\Models\Users\Repositories\UserRepository;
use App\Models\Users\Repositories\Interfaces\UserRepositoryInterface;

use App\Models\Accounts\Admins\Admin;
use App\Models\Accounts\Admins\Repositories\AdminRepository;
use App\Models\Accounts\Admins\Repositories\Interfaces\AdminRepositoryInterface;

use App\Models\Accounts\Employees\Employee;
use App\Models\Accounts\Employees\Repositories\EmployeeRepository;
use App\Models\Accounts\Employees\Repositories\Interfaces\EmployeeRepositoryInterface;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tools\UploadableTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{
    use UploadableTrait;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * Profile Controller Constructor
     *
     * @param UserRepositoryInterface $UserRepository
     * @param AdminRepositoryInterface $AdminRepository
     * @param EmployeeRepositoryInterface $EmployeeRepsitory
     * @return void
     */
    public function __construct(
        UserRepositoryInterface $userRepository
    )
    {
        $this->userRepo = $userRepository;
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int      $userId
     * @param  string   $role
     * @return \Illuminate\Http\Response
     */
    public function editProfile($userId)
    {
        $user = $this->userRepo->findUserById($userId);
        $provinces = Cache::get('provinces');

        if(substr($user['phone'],0,3) == '+62') {
            $user['phone'] = substr($user['phone'],3);
        }

        return view('admin.accounts.profile', compact('user', 'provinces'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int      $userId
     * @param  string   $role
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request, $userId)
    {
        $data = $request->except('_token', '_method');
        $user = $this->userRepo->findUserById($userId);
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email,'.$user->id],
            'phone' => ['required', 'string', 'max:191'],
        ]);
        $userRepo = new UserRepository($user);
        $userRepo->updateUser($data);
        
        return redirect()->route('admin.edit.profile', $user->id)->with([
            'status'    => 'success',
            'message'   => 'Update Profile Successfull!'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int      $userId
     * @param  string   $role
     * @return \Illuminate\Http\Response
     */
    public function updateProfileAvatar(Request $request, $userId)
    {
        $data = $request->except('_token', '_method');
        $user = $this->userRepo->findUserById($userId);
        $userRepo = new UserRepository($user);
        if ($request->hasFile('image') && $request->file('image') instanceof UploadedFile) {
            if(!empty($user->image)) {
                $userRepo->deleteFile($user->image);
            }
            $data['image'] = $this->userRepo->saveCoverImage($request->file('image'));
            $userRepo->updateUser($data);
        } else {
            return redirect()->route('admin.edit.profile', $user->id)->with([
                'status'    => 'danger',
                'message'   => "You can't Update Blank Photo"
            ]);
        }
        
        return redirect()->route('admin.edit.profile', $user->id)->with([
            'status'    => 'success',
            'message'   => 'Update Profile Successfull!'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateUserRequest  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    // public function updateSetting(UpdateUserRequest $request, $userId)
    // {
    //     $user = $this->userRepo->findUserById($userId);
    //     $userRepo = new UserRepository($user);
    //     $chkOldPassword = Hash::check($request->password_confirmation, $user->password);
    //     if ($chkOldPassword) {
    //         $user->username = $request->username;
    //         $user->save();
            
    //         return redirect()->route('admin.logout');
    //     }
    //     return redirect()->route('admin.edit.profile', [$user->id, $user->role])->with([
    //         'status'    => 'danger',
    //         'message'   => "Your confirmation password something wrong"
    //     ]);
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  ResetPasswordRequest  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(ResetPasswordRequest $request, $userId)
    {
        $user = $this->userRepo->findUserById($userId);
        $userRepo = new UserRepository($user);
        $chkOldPassword = Hash::check($request->oldpassword, $user->password);

        if ($chkOldPassword) {
            $user->password = Hash::make($request->password);
            $user->save();
            
            return redirect()->route('admin.logout')->with([
                'status'    => 'success',
                'message'   => 'Please re-login after updated account!'
            ]);
        }
        
        return redirect()->route('admin.edit.profile', $user->id)->with([
            'status'    => 'danger',
            'message'   => "Your confirmation password something wrong"
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int      $userId
     * @param  string   $role
     * @return \Illuminate\Http\Response
     */
    public function updateProfileAddress(Request $request, $userId, $role)
    {
        $request->validate([
            'address_id' =>  ['required', 'string']
        ]);
        $data = $request->except('_token', '_method');
        $user = $this->userRepo->findUserById($userId);
        $userRepo = new UserRepository($user);
        $address = array();
        $userRepo->updateUser($data);
        $address[] = array(
            "address_id"    => $user->address_id,
            "address_name"  => $user->village->name,
            "district_id"   => $user->village->district->id,
            "district_name" => $user->village->district->name,
            "regency_id"    => $user->village->district->regency->id,
            "regency_name"  => $user->village->district->regency->name,
            "province_id"   => $user->village->district->regency->province->id,
            "province_name" => $user->village->district->regency->province->name
        );

        return response()->json([
            'status'    => 'success',
            'message'   => 'Update Address Successfully!',
            'data'      => $address[0]
        ]);
    }
}
