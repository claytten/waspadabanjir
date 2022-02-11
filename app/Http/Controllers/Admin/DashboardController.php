<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maps\Fields\Field;
use App\Models\Reports\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Counting Map each month
     */
    public function countMapEachMonth($year)
    {
        $field = Field::whereYear('date_in', $year)->where('status', 1)->get();
        $report = Report::whereYear('created_at', $year)->get();
        
        $fieldChartCount = $field->groupBy(function($date) {
            return Carbon::parse($date->date_in)->format('m'); // grouping by months
        });
        $fieldmcount = [];
        $fieldArr = [];

        foreach ($fieldChartCount as $key => $value) {
            $fieldmcount[(int)$key] = count($value);
        }

        for($i = 1; $i <= 12; $i++){
            if(!empty($fieldmcount[$i])){
                $fieldArr[$i] = $fieldmcount[$i];    
            }else{
                $fieldArr[$i] = 0;    
            }
        }

        $fieldCount = $field->count();
        $reportCount = $report->count();
        $villageCount = 0;
        $victimCount = 0;
        foreach($field as $data) {
            $villageCount += $data->detailLocations->count();
            $victimCount += $data->deaths + $data->injured + $data->losts;
        }
        return response()->json([
            'status'            => 'success',
            'chart'             => $fieldArr,
            'reportCount'       => $reportCount,
            'fieldCount'        => $fieldCount,
            'villageCount'      => $villageCount,
            'victimCount'       => $victimCount
        ]);
    }
}
