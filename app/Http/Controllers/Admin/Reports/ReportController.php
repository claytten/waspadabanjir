<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Models\Reports\Repositories\Interfaces\ReportRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\Reports\Repositories\ReportRepository;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * @var ReportRepositoryInterface
     */
    private $reportRepo;

    /**
     * Report Controller Constructor
     *
     * @param ReportRepositoryInterface $reportRepository
     * @return void
     */
    public function __construct(
        ReportRepositoryInterface $reportRepository
    ) {
        $this->middleware('permission:reports-list',['only' => ['index']]);
        $this->middleware('permission:reports-create', ['only' => ['create','store']]);
        $this->middleware('permission:reports-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:reports-delete', ['only' => ['destroy']]);
        // binding repository
        $this->reportRepo = $reportRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $report = $this->reportRepo->findReportById($request->id);
            $reportRepo = new ReportRepository($report);
            $reportRepo->updateReport([
                'status'    => $request->status,
            ]);

            return response()->json([
                'code'  => 200,
                'status'=> 'success',
                'data'  => $report
            ]);
        }
        $reports = $this->reportRepo->listReports()->sortBy('name');
        return view('admin.reports.index',compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.reports.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('_token','_method');

        $this->reportRepo->createReport($data);

        return redirect()->route('admin.reports.index')->with([
            'status'    => 'success',
            'message'   => 'Create Report successfully!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $report = $this->reportRepo->findReportById($id);

        return view('admin.reports.show', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $report = $this->reportRepo->findReportById($id);

        return view('admin.reports.edit', compact('report'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->except('_token', '_method');
        $report = $this->reportRepo->findReportById($id);
        $reportRepo = new ReportRepository($report);
        $reportRepo->updateReport($data);

        return redirect()->route('admin.reports.index')->with([
            'status'    => 'success',
            'message'   => 'Update Report Successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $report = $this->reportRepo->findReportById($id);
        $reportRepo = new ReportRepository($report);
        $reportRepo->deleteReport();

        return response()->json([
            'code'  => 200,
            'status'=> 'success'
        ]);
    }
}
