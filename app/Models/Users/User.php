<?php

namespace App\Models\Users;

use App\Models\Address\Villages\Village;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'admin';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'name',
        'address',
        'phone',
        'image',
        'role',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get Phone Number
     * 
     * @return string $this->phone
     */
    public function routeNotificationForWhatsApp()
    {
        $user = Cache::get('adminWA');
        return $user->phone;
    }
}
