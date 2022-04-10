<?php

namespace App\Models\Users\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Users\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function listUsers(): Collection;

    public function createUser(array $params) : User;

    public function findUserById(int $id) : User;

    public function findUserByEmail($email): User;

    public function updateUser(array $params): bool;

    public function syncRoles(array $roleIds);

    public function listRoles() : Collection;

    public function hasRole(string $roleName) : bool;

    public function deleteUser() : bool;

    public function saveCoverImage(UploadedFile $file) : string;

    public function deleteFile(string $get_data);

    public function defaultMenu(string $name) : string;
}
