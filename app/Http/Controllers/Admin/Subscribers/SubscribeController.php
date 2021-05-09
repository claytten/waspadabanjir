<?php

namespace App\Http\Controllers\Admin\Subscribers;

use App\Models\Subscribers\Repositories\Interfaces\SubscribeRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    /**
     * @var SubscribeRepositoryInterface
     */
    private $subscribeRepo;

    /**
     * Subscriber Controller Constructor
     *
     * @param SubscribeRepositoryInterface $SubscribeRepository
     * @return void
     */
    public function __construct(
        SubscribeRepositoryInterface $subscribeRepository
    ) {
        $this->middleware('permission:subscriber-list',['only' => ['index']]);
        $this->middleware('permission:subscriber-create', ['only' => ['create','store']]);
        $this->middleware('permission:subscriber-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:subscriber-delete', ['only' => ['destroy']]);
        // binding repository
        $this->subscribeRepo = $subscribeRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
