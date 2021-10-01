<?php

namespace App\Models\Users\Repositories;

use Jsdecena\Baserepo\BaseRepository;
use App\Models\Users\User;
use App\Models\Users\Exceptions\UserNotFoundException;
use App\Models\Users\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\Tools\UploadableTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    use UploadableTrait;
    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->model = $user;
    }

    /**
     * List all the users
     *
     * @param string $order
     * @param string $sort
     *
     * @return Collection
     */
    public function listUsers(): Collection
    {
        return $this->model->where('id', '!=', auth()->user()->id)->get();
    }

    /**
     * Create the user
     *
     * @param array $data
     *
     * @return User
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        if(isset($data['phone'])) {
            if(substr($data['phone'],0,3) == '+62') {
                $data['phone'] = preg_replace("/^0/", "+62", $data['phone']);
            } else if(substr($data['phone'],0,1) == '0') {
                $data['phone'] = preg_replace("/^0/", "+62", $data['phone']);
            } else {
                $data['phone'] = "+62".$data['phone'];
            }
        }
        $store = $this->create($data);
        $store->assignRole($data['role']);
        return $store; 
    }

    /**
     * Find the user by id
     *
     * @param int $id
     *
     * @return User
     */
    public function findUserById(int $id): User
    {
        try {
            return $this->findOneOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException;
        }
    }

    /**
     * Find the user by role
     * 
     * @param string $email
     * 
     * @return User
     */
    public function findUserByEmail($email): User
    {
        try {
            return $this->model->where('email', $email)->first();
        } catch(ModelNotFoundException $e) {
            throw new UserNotFoundException();
        }
    }

    /**
     * Update user
     *
     * @param array $params
     *
     * @return bool
     */
    public function updateUser(array $params): bool
    {
        if (isset($params['password'])) {
            $params['password'] = Hash::make($params['password']);
        }
        if(isset($params['phone'])) {
            if(substr($params['phone'],0,3) == '+62') {
                $params['phone'] = preg_replace("/^0/", "+62", $params['phone']);
            } else if(substr($params['phone'],0,1) == '0') {
                $params['phone'] = preg_replace("/^0/", "+62", $params['phone']);
            } else {
                $params['phone'] = "+62".$params['phone'];
            }
        }

        $filtered = collect($params)->all();

        return $this->update($filtered);
    }

    /**
     * @param array $roleIds
     */
    public function syncRoles(array $roleIds)
    {
        $this->model->roles()->sync($roleIds);
    }

    /**
     * @return Collection
     */
    public function listRoles(): Collection
    {
        return $this->model->roles()->get();
    }

    /**
     * @param string $roleName
     *
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->model->hasRole($roleName);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isAuthUser(User $user): bool
    {
        $isAuthUser = false;
        if (Auth::user()->id == $user->id) {
            $isAuthUser = true;
        }
        return $isAuthUser;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function deleteUser() : bool
    {
        return $this->delete();
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function saveCoverImage(UploadedFile $file) : string
    {
        return $file->store('users', ['disk' => 'public']);
    }

    /**
     * Destroye File on Storage
     *
     * @param string $get_data
     *
     */
    public function deleteFile(string $get_data)
    {
        return File::delete("storage/{$get_data}");
    }

    /**
     * Default Menu WhatsApp Message
     * @param string $name name of subscriber
     * @return string
     */
    public function defaultMenu(string $name) : string
    {
      $menu = collect([
        [
          "body" => "A. Statistik Pengguna"
        ],
        [
          "body" => "B. Daftar Laporan",
        ],
        [
          "body" => "C. Daftar Pengguna",
        ]
      ]);
      $message = "--MENU UTAMA--\nHalo Kak {$name}, Berikut daftar layanan yang bisa kamu gunakan sebagai admin. Apa yang ingin kamu ketahui ?\n";
      foreach($menu as $item) {
        $message .= "{$item['body']}\n";
      }
      $message .= "\n\nBalas *SATU HURUF* saja yaa; A,B,C,D dan seterusnya";
      return $message;
    }
}
