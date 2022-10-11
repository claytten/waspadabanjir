<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Models\Users\Requests\UpdateUserRequest;
use App\Models\Users\Requests\ResetPasswordRequest;
use App\Models\Users\Repositories\UserRepository;
use App\Models\Users\Repositories\Interfaces\UserRepositoryInterface;

use App\Http\Controllers\Controller;
use App\Models\Users\Requests\ProfileUserAvatarRequest;
use App\Models\Users\Requests\ProfileUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * Profile Controller Constructor
     *
     * @param UserRepositoryInterface $UserRepository
     * @return void
     */
    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
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

        if (substr($user['phone'], 0, 3) == '+62') {
            $user['phone'] = substr($user['phone'], 3);
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
    public function updateProfile(ProfileUserRequest $request, $userId)
    {
        $data = $request->except('_token', '_method');
        $regex = '/^(\+62|62|8|08)(\d{3,4}-?){2}\d{3,4}$/';
        if (preg_match($regex, $data['phone']) == false) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Nomor telepon harus dengan awalan +62 atau 08'
            ]);
        }
        $user = $this->userRepo->findUserById($userId);
        $userRepo = new UserRepository($user);
        $userRepo->updateUser($data);

        return redirect()->route('admin.edit.profile', $user->id)->with([
            'status'    => 'success',
            'message'   => 'Profil Admin Berhasil Diperbarui'
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
    public function updateProfileAvatar(ProfileUserAvatarRequest $request, $userId)
    {
        dd('hehe');
        $data = $request->except('_token', '_method');
        $user = $this->userRepo->findUserById($userId);
        $userRepo = new UserRepository($user);
        if ($request->hasFile('image') && $request->file('image') instanceof UploadedFile) {
            if (!empty($user->image)) {
                $userRepo->deleteFile($user->image);
            }
            $data['image'] = $this->userRepo->saveCoverImage($request->file('image'));
            $userRepo->updateUser($data);
        } else {
            return redirect()->route('admin.edit.profile', $user->id)->with([
                'status'    => 'danger',
                'message'   => "Berkas foto tidak boleh kosong!"
            ]);
        }

        return redirect()->route('admin.edit.profile', $user->id)->with([
            'status'    => 'success',
            'message'   => 'Foto Profil Admin Berhasil Diubah!'
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
            'message'   => "Password tidak cocok. Silakan coba lagi"
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
    public function updateProfileAddress(Request $request, $userId)
    {
        $request->validate([
            'address' =>  ['required', 'string']
        ]);
        $data = $request->except('_token', '_method');
        $user = $this->userRepo->findUserById($userId);
        $userRepo = new UserRepository($user);
        $userRepo->updateUser($data);

        return response()->json([
            'status'    => 'success',
            'message'   => 'Data Alamat Admin Berhasil Diubah!'
        ]);
    }
}
