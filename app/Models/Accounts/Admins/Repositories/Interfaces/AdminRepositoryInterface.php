<?php

namespace App\Models\Accounts\Admins\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Accounts\Admins\Admin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection as Support;

interface AdminRepositoryInterface extends BaseRepositoryInterface
{
    public function updateAdmin(array $params) : bool;

    public function findAdminById(int $id) : Admin;

    public function searchAdmin(string $text) : Collection;

    public function saveCoverImage(UploadedFile $file) : string;

    public function deleteFile(string $get_data);
}
