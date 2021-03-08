<?php

namespace App\Models\Accounts\Employees\Repositories;

use Jsdecena\Baserepo\BaseRepository;
use App\Models\Accounts\Employees\Employee;
use App\Models\Accounts\Employees\Exceptions\CreateEmployeeInvalidArgumentException;
use App\Models\Accounts\Employees\Exceptions\EmployeeNotFoundException;
use App\Models\Accounts\Employees\Exceptions\UpdateEmployeeInvalidArgumentException;
use App\Models\Accounts\Employees\Repositories\Interfaces\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection as Support;

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{
    /**
     * EmployeeRepository constructor.
     * @param Employee $employee
     */
    public function __construct(Employee $employee)
    {
        parent::__construct($employee);
        $this->model = $employee;
    }

    /**
     * Update the employee
     *
     * @param array $params
     *
     * @return bool
     * @throws UpdateEmployeeInvalidArgumentException
     */
    public function updateEmployee(array $params) : bool
    {
        try {
            return $this->model->update($params);
        } catch (QueryException $e) {
            throw new UpdateEmployeeInvalidArgumentException($e);
        }
    }

    /**
     * Find the employee or fail
     *
     * @param int $id
     *
     * @return Employee
     * @throws EmployeeNotFoundException
     */
    public function findEmployeeById(int $id) : Employee
    {
        try {
            return $this->findOneOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new EmployeeNotFoundException($e);
        }
    }

    /**
     * @param string $text
     * @return mixed
     */
    public function searchEmployee(string $text = null) : Collection
    {
        if (is_null($text)) {
            return $this->all();
        }
        return $this->model->searchEmployee($text)->get();
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function saveCoverImage(UploadedFile $file) : string
    {
        return $file->store('employees', ['disk' => 'public']);
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
