<?php

namespace App\Models\Subscribers;

use App\Models\Address\Regencies\Regency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Subscribe extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'subscribers';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'phone',
        'status'
    ];

    /**
     * Get the regency that owns the Admin
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function regency()
    {
        return $this->belongsTo(Regency::class, 'address', 'id');
    }

    /**
     * Get Phone Number
     * 
     * @return string $this->phone
     */
    public function routeNotificationForWhatsApp()
    {
        return $this->phone;
    }
}
