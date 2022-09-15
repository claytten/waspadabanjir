<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reports\ReportImage;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'report_type',
        'phone',
        'address',
        'message',
        'status'
    ];

    /**
     * Get all of the images for the Report
     *
     */
    public function images()
    {
        return $this->hasMany(ReportImage::class);
    }
    
}
