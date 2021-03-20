<?php

namespace App\Models\Address\Villages;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Address\Districts\District;
use App\Models\Accounts\Admins\Admin;
use App\Models\Accounts\Employees\Employee;

class Village extends Model
{
    use HasFactory;

    protected $table = 'villages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'district_id',
        'name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the district that owns 
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function countDistrict()
    {
        return $this->district()->count();
    }

    /**
     * Get the admin associated with the Village
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'address_id', 'id');
    }

    public function countAdmin()
    {
        return $this->admin()->count();
    }

    /**
     * Get the employee associated with the Village
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'address_id', 'id');
    }

    public function countEmployee()
    {
        return $this->employee()->count();
    }
}
