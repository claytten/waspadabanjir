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

    public const F_LEVEL = [
      [
        'id' => 1,
        'name' => 'Siaga 1',
        'color' => "#E76667",
        'desc' => 'Debit air meningkat atau tidak surut kurun waktu 6 jam. Warga harus mengungsi dari lokasi bencana banjir'
      ],
      [
        'id' => 2,
        'name' => 'Siaga 2',
        'color' => "#F8996A",
        "desc" => 'Debit air meningkatan signifikan dan meluas dari pintu air. Warga diminta segera mengungsi.'
      ],
      [
        'id' => 3,
        'name' => 'Siaga 3',
        'color' => "#F1F16A",
        'desc' => 'Terdapat peningkatan debit air disekitar pintu air. Warga diminta berhati-hati.'
      ],
      [
        'id' => 4,
        'name' => 'Siaga 4',
        'color' => "#00CC80",
        'desc' => 'Belum ada peningkatan debit air disekitar pintu air. Warga masih tergolong aman.'
      ]
    ];

    protected $table = 'fields';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'area',
        'description',
        'deaths',
        'losts',
        'injured',
        'date_in',
        'date_out',
        'status',
        'level'
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
