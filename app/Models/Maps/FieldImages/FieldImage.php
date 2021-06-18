<?php

namespace App\Models\Maps\FieldImages;

use App\Models\Maps\Fields\Field;
use Illuminate\Database\Eloquent\Model;

class FieldImage extends Model
{
    protected $table = 'fields_has_images';

    protected $fillable = [
        'field_id',
        'src'
    ];

    public $timestamps = false;

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