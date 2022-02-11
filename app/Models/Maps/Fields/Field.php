<?php

namespace App\Models\Maps\Fields;

use App\Models\Maps\FieldDetailLocations\FieldDetailLocation;
use App\Models\Maps\FieldImages\FieldImage;
use App\Models\Maps\Geometries\Geometry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $table = 'fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'color',
        'area',
        'description',
        'deaths',
        'losts',
        'injured',
        'date_in',
        'date_out',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get all of the images for the Field
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(FieldImage::class);
    }

    /**
     * Get all of the detail location for the Field
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detailLocations()
    {
        return $this->hasMany(FieldDetailLocation::class);
    }

    /**
     * The geometries that belong to the Field
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function geometries()
    {
        return $this->belongsTo(Geometry::class, 'id');
    }
}
