<?php

namespace App\Models\Accounts\Employees\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Accounts\Employees\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection as Support;

interface EmployeeRepositoryInterface extends BaseRepositoryInterface
{
    public function createEmployee(array $data): Employee;

    public function updateEmployee(array $params) : bool;

    public function findEmployeeById(int $id) : Employee;

    public function searchEmployee(string $text) : Collection;

    public function saveCoverImage(UploadedFile $file) : string;

    public function deleteEmployee() : bool;

    public function deleteFile(string $get_data);
}
