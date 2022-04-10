<?php

namespace App\Models\Maps\FieldDetailLocations;

use App\Models\Maps\Fields\Field;
use Illuminate\Database\Eloquent\Model;

class FieldDetailLocation extends Model
{
    protected $table = 'fields_detail_locations';

    public $timestamps = false;

    protected $fillable = [
      'field_id',
      'district',
      'village',
    ];

    /**
     * Get the field that owns the FieldImage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id', 'id');
    }
}
