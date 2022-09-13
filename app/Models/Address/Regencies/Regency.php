<?php

namespace App\Models\Address\Regencies;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Address\Districts\District;
use App\Models\Address\Provinces\Province;
use App\Models\Subscribers\Subscribe;

class Regency extends Model
{
    use HasFactory;

    protected $table = 'regencies';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
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

    /**
     * Get all of the subscribers for the Regency
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscribers()
    {
        return $this->hasMany(Subscribe::class);
    }
}
