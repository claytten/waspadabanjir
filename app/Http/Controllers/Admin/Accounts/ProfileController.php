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
     * @var AdminRepositoryInterface
     */
    private $adminRepo;

    /**
     * @var EmployeeRepositoryInterface
     */
    private $employeeRepo;

    /**
     * Profile Controller Constructor
     *
     * @param UserRepositoryInterface $UserRepository
     * @param AdminRepositoryInterface $AdminRepository
     * @param EmployeeRepositoryInterface $EmployeeRepsitory
     * @return void
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        AdminRepositoryInterface $adminRepository,
        EmployeeRepositoryInterface $employeeRepository
    )
    {
        $this->userRepo = $userRepository;
        $this->adminRepo = $adminRepository;
        $this->employeeRepo = $employeeRepository;
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int      $userId
     * @param  string   $role
     * @return \Illuminate\Http\Response
     */
    public function editProfile($userId, $role)
    {
        $user = $this->userRepo->findUserById($userId);
        $admin = $user->$role;
        $provinces = Cache::get('provinces');
        if($user->role == 'admin') {
            return view('admin.profiles.profile_admin', compact('admin', 'provinces')); 
        } 
        return view('admin.profiles.profile_employee', compact('admin', 'provinces'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int      $userId
     * @param  string   $role
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request, $userId, $role)
    {
        $data = $request->except('_token', '_method');
        $user = $this->userRepo->findUserById($userId);
        if($user->role == 'admin') {
            $request->validate([
                'name' => ['required', 'string', 'max:191'],
                'email' => ['required', 'email', 'max:191', 'unique:admin,email,'.$user->$role->id],
                'phone' => ['required', 'string', 'max:191'],
            ]);
            $admin = $this->adminRepo->findAdminById($user->$role->id);
            $adminRepo = new AdminRepository($admin);
            $adminRepo->updateAdmin($data);
        } else {
            $request->validate([
                'name' => ['required', 'string', 'max:191'],
                'email' => ['required', 'email', 'max:191', 'unique:employees,email,'.$user->$role->id],
                'phone' => ['required', 'string', 'max:191'],
                'id_card' => ['required', 'string', 'max:191'],
            ]);
            $admin = $this->employeeRepo->findEmployeeById($user->$role->id);
            $adminRepo = new EmployeeRepository($admin);
            $adminRepo->updateEmployee($data);
        }
        
        return redirect()->route('admin.edit.profile', [$user->id, $user->role])->with([
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
    public function updateProfileAvatar(Request $request, $userId, $role)
    {
        $data = $request->except('_token', '_method');
        $user = $this->userRepo->findUserById($userId);
        if($user->role == 'admin') {
            $admin = $this->adminRepo->findAdminById($user->$role->id);
            $adminRepo = new AdminRepository($admin);
            if ($request->hasFile('image') && $request->file('image') instanceof UploadedFile) {
                if(!empty($admin->image)) {
                    $adminRepo->deleteFile($admin->image);
                }
                $data['image'] = $this->employeeRepo->saveCoverImage($request->file('image'));
                $adminRepo->updateAdmin($data);
            } else {
                return redirect()->route('admin.edit.profile', [$user->id, $user->role])->with([
                    'status'    => 'danger',
                    'message'   => "You can't Update Blank Photo"
                ]);
            }
        } else {
            $admin = $this->employeeRepo->findEmployeeById($user->$role->id);
            $adminRepo = new EmployeeRepository($admin);
            if ($request->hasFile('image') && $request->file('image') instanceof UploadedFile) {
                if(!empty($admin->image)) {
                    $adminRepo->deleteFile($admin->image);
                }
                $data['image'] = $this->employeeRepo->saveCoverImage($request->file('image'));
                $adminRepo->updateEmployee($data);
            } else {
                return redirect()->route('admin.edit.profile', [$user->id, $user->role])->with([
                    'status'    => 'danger',
                    'message'   => "You can't Update Blank Photo"
                ]);
            }
        }
        
        return redirect()->route('admin.edit.profile', [$user->id, $user->role])->with([
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
    public function updateSetting(UpdateUserRequest $request, $userId)
    {
        $user = $this->userRepo->findUserById($userId);
        $userRepo = new UserRepository($user);
        $chkOldPassword = Hash::check($request->password_confirmation, $user->password);
        if ($chkOldPassword) {
            $user->username = $request->username;
            $user->save();
            
            return redirect()->route('admin.logout');
        }
        return redirect()->route('admin.edit.profile', [$user->id, $user->role])->with([
            'status'    => 'danger',
            'message'   => "Your confirmation password something wrong"
        ]);
    }

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
        
        return redirect()->route('admin.edit.profile', [$user->id, $user->role])->with([
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
        $data = $request->except('_token', '_method');
        $user = $this->userRepo->findUserById($userId);
        $address = array();
        if($request->ajax()) {
            if($user->role == 'admin') {
                $request->validate([
                    'address_id' =>  ['required', 'string']
                ]);
                $admin = $this->adminRepo->findAdminById($user->$role->id);
                $adminRepo = new AdminRepository($admin);
                $adminRepo->updateAdmin($data);
                $address[] = array(
                    "address_id"    => $admin->address_id,
                    "address_name"  => $admin->village->name,
                    "district_id"   => $admin->village->district->id,
                    "district_name" => $admin->village->district->name,
                    "regency_id"    => $admin->village->district->regency->id,
                    "regency_name"  => $admin->village->district->regency->name,
                    "province_id"   => $admin->village->district->regency->province->id,
                    "province_name" => $admin->village->district->regency->province->name
                );
            } else {
                $request->validate([
                    'address_id' =>  ['required', 'string']
                ]);
                $admin = $this->employeeRepo->findEmployeeById($user->$role->id);
                $adminRepo = new EmployeeRepository($admin);
                $adminRepo->updateEmployee($data);
                $address[] = array(
                    "address_id"    => $admin->address_id,
                    "address_name"  => $admin->village->name,
                    "district_id"   => $admin->village->district->id,
                    "district_name" => $admin->village->district->name,
                    "regency_id"    => $admin->village->district->regency->id,
                    "regency_name"  => $admin->village->district->regency->name,
                    "province_id"   => $admin->village->district->regency->province->id,
                    "province_name" => $admin->village->district->regency->province->name
                );
            }
        }

        return response()->json([
            'status'    => 'success',
            'message'   => 'Update Address Successfully!',
            'data'      => $address[0]
        ]);
    }
}
