<?php

namespace App\Models\Address\Regencies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Address\Districts\District;
use App\Models\Address\Provinces\Province;

class Regency extends Model
{
    use HasFactory;

    protected $table = 'regencies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'province_id',
        'name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get all of the districts for the Regency
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function countDistrict()
    {
        return $this->districts()->count();
    }

    /**
     * Get the province that owns the Regency
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function countProvince()
    {
        return $this->province()->count();
    }
}
