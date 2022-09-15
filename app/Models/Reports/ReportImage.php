<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reports\Report;

class ReportImage extends Model
{
  use HasFactory;

  protected $table = 'reports_has_images';

  public $timestamps = false;

  protected $fillable = [
    'report_id',
    'src'
  ];

  /**
   * Get the reports that owns the ReportImage
   *
   */
  public function reports()
  {
      return $this->belongsTo(Report::class, 'report_id', 'id');
  }
}
