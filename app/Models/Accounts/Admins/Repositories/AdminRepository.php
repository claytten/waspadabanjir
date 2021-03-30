<?php

namespace App\Models\Accounts\Admins\Repositories;

use Jsdecena\Baserepo\BaseRepository;
use App\Models\Accounts\Admins\Admin;
use App\Models\Accounts\Admins\Exceptions\CreateAdminInvalidArgumentException;
use App\Models\Accounts\Admins\Exceptions\AdminNotFoundException;
use App\Models\Accounts\Admins\Exceptions\UpdateAdminInvalidArgumentException;
use App\Models\Accounts\Admins\Repositories\Interfaces\AdminRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection as Support;

class AdminRepository extends BaseRepository implements AdminRepositoryInterface
{
    /**
     * AdminRepository constructor.
     * @param Admin $admin
     */
    public function __construct(Admin $admin)
    {
        parent::__construct($admin);
        $this->model = $admin;
    }

    /**
     * Create the admin
     *
     * @param array $data
     *
     * @return Admin
     */
    public function createAdmin(array $data): Admin
    {
        try {
            if(substr($data['phone'],0,3) == '+62') {
                $data['phone'] = preg_replace("/^0/", "+62", $data['phone']);
            } else if(substr($data['phone'],0,1) == '0') {
                $data['phone'] = preg_replace("/^0/", "+62", $data['phone']);
            } else {
                $data['phone'] = "+62".$data['phone'];
            }
            return $this->create($data);
        } catch (QueryException $e) {
            throw new CreateAdminInvalidArgumentException($e);
        }
    }

    /**
     * Update the admin
     *
     * @param array $params
     *
     * @return bool
     * @throws UpdateAdminInvalidArgumentException
     */
    public function updateAdmin(array $params) : bool
    {
        try {
            if(substr($params['phone'],0,3) == '+62') {
                $params['phone'] = preg_replace("/^0/", "+62", $params['phone']);
            } else if(substr($params['phone'],0,1) == '0') {
                $params['phone'] = preg_replace("/^0/", "+62", $params['phone']);
            } else {
                $params['phone'] = "+62".$params['phone'];
            }
            return $this->model->update($params);
        } catch (QueryException $e) {
            throw new UpdateAdminInvalidArgumentException($e);
        }
    }

    /**
     * Find the admin or fail
     *
     * @param int $id
     *
     * @return Admin
     * @throws AdminNotFoundException
     */
    public function findAdminById(int $id) : Admin
    {
        try {
            return $this->findOneOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new AdminNotFoundException($e);
        }
    }

    /**
     * @param string $text
     * @return mixed
     */
    public function searchAdmin(string $text = null) : Collection
    {
        if (is_null($text)) {
            return $this->all();
        }
        return $this->model->searchAdmin($text)->get();
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function saveCoverImage(UploadedFile $file) : string
    {
        return $file->store('admins', ['disk' => 'public']);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function deleteAdmin() : bool
    {
        if ($this->model->image) {
            $this->deleteFile($this->model->image);
        }
        return $this->delete();
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
}
