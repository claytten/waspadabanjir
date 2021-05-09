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
     * Create the employee
     *
     * @param array $data
     *
     * @return Employee
     */
    public function createEmployee(array $data): Employee
    {
        try {
            if(isset($data['phone'])) {
                if(substr($data['phone'],0,3) == '+62') {
                    $data['phone'] = preg_replace("/^0/", "+62", $data['phone']);
                } else if(substr($data['phone'],0,1) == '0') {
                    $data['phone'] = preg_replace("/^0/", "+62", $data['phone']);
                } else {
                    $data['phone'] = "+62".$data['phone'];
                }
            }
            return $this->create($data);
        } catch (QueryException $e) {
            throw new CreateEmployeeInvalidArgumentException($e);
        }
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
            if(isset($params['phone'])) {
                if(substr($params['phone'],0,3) == '+62') {
                    $params['phone'] = preg_replace("/^0/", "+62", $params['phone']);
                } else if(substr($params['phone'],0,1) == '0') {
                    $params['phone'] = preg_replace("/^0/", "+62", $params['phone']);
                } else {
                    $params['phone'] = "+62".$params['phone'];
                }
            }
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
     * @return bool
     * @throws \Exception
     */
    public function deleteEmployee() : bool
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
