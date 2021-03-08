<?php

namespace App\Models\Users\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection as Support;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function updateUser(array $params) : bool;

    public function findUserById(int $id) : User;

    public function searchUser(string $text) : Collection;

    public function saveCoverImage(UploadedFile $file) : string;

    public function deleteFile(string $get_data);
}
