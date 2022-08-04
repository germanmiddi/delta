<?php

namespace App\Http\Controllers\Manager\Drivers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Driver;
use Illuminate\Support\Facades\Redis;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return  Inertia::render('Manager/Drivers/List');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return  Inertia::render('Manager/Drivers/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Driver::create($request->all());
        return response()->json(['message'=>'Chofer creado con éxito'], 200);
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
    public function edit(Request $request)
    {
        try {
            Driver::where('id', $request->id)->update($request->all());
            return response()->json(['message'=>'Chofer actualizada con éxito','title'=>'Capsula'], 200);
         } catch (\Throwable $th) {
             return response()->json(['message'=>'Se ha producido un error','title'=>'Capsula'], 203);
         }
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

    public function list(){

        return  Driver::orderBy("created_at", 'DESC')
                        ->paginate(999)
                        ->withQueryString()
                        ->through(fn ($driver) => [
                            'id'        => $driver->id,
                            'fullname'  => $driver->fullname,
                            'email'     => $driver->email,
                            'phone'     => $driver->phone,
                            'cellphone' => $driver->cellphone,
                            'dni'       => $driver->dni,
                            'truck'     => $driver->truck,
                            'notes'     => $driver->notes
                        ]);

    }


}
