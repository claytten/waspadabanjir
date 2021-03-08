<?php

namespace App\Models\Users\Repositories;

use Jsdecena\Baserepo\BaseRepository;
use App\Models\Users\User;
use App\Models\Users\Exceptions\CreateUserInvalidArgumentException;
use App\Models\Users\Exceptions\UserNotFoundException;
use App\Models\Users\Exceptions\UpdateUserInvalidArgumentException;
use App\Models\Users\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection as Support;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->model = $user;
    }

    /**
     * Update the user
     *
     * @param array $params
     *
     * @return bool
     * @throws UpdateUserInvalidArgumentException
     */
    public function updateUser(array $params) : bool
    {
        try {
            return $this->model->update($params);
        } catch (QueryException $e) {
            throw new UpdateUserInvalidArgumentException($e);
        }
    }

    /**
     * Find the user or fail
     *
     * @param int $id
     *
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserById(int $id) : User
    {
        try {
            return $this->findOneOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException($e);
        }
    }
    
    /**
     * @param string $text
     * @return mixed
     */
    public function searchUser(string $text = null) : Collection
    {
        if (is_null($text)) {
            return $this->all();
        }
        return $this->model->searchUser($text)->get();
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
}
