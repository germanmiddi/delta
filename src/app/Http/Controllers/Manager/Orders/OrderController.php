<?php

namespace App\Http\Controllers\Manager\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

use App\Models\Order;
use App\Models\Driver;
use App\Models\Client;
use App\Models\Address;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return  Inertia::render('Manager/Orders/List');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $client = DB::table('clients')
                    ->join('addresses','addresses.client_id', '=', 'clients.id' )
                    ->join('states','addresses.state_id', '=', 'states.id' )
                    ->join('cities','addresses.city_id', '=', 'cities.id' )
                    ->get();

        return  Inertia::render('Manager/Orders/Create', [
            'drivers' => Driver::all(),
            'clients' => $client
                              
        ]); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $input_finicio = $request->input('fecha_inicio');
        $fecha_inicio  = date('Y-m-d', strtotime($input_finicio));

        $input_hinicio = $request->input('hora_inicio');
        $hora_inicio   = date('H:i', strtotime( $input_hinicio['hours'] . ':' . $input_hinicio['minutes'])  );

        $input_fretiro = $request->input('fecha_fin');
        $fecha_retiro  = date('Y-m-d', strtotime($input_fretiro));

        $input_hretiro  = $request->input('hora_fin');
        $hora_retiro    = date('H:i', strtotime( $input_hretiro['hours'] . ':' . $input_hretiro['minutes']) );

        $order = new Order;
        $order->fecha_inicio = $fecha_inicio;
        $order->hora_inicio  = $hora_inicio;
        $order->fecha_retiro = $fecha_retiro;
        $order->hora_retiro  = $hora_retiro;
        $order->client_id    = $request->input('client_id');
        $order->driver_id    = $request->input('driver');
        $order->order_status = 'AGENDADO';

        $order->save();
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

    public function list(){

        return Order::orderBy("created_at", 'DESC')
                    ->paginate(999)
                    ->withQueryString()
                    ->through(fn ($order) => [
                        'id'       => $order->id,
                        'f_inicio' => Carbon::create($order->fecha_inicio)->format('d/m/Y'),
                        'h_inicio' => Carbon::create($order->hora_inicio)->format('H:i'),
                        'f_retiro' => Carbon::create($order->fecha_retiro)->format('d/m/Y'),
                        'h_retiro' => Carbon::create($order->hora_retiro)->format('H:i'),
                        'client'   => $order->client()->with('address')->get(),
                        'status'   => $order->order_status,
                        'driver'   => $order->driver()->get()
                    ]); 

    }


    public function listdashboard(){

        $result = Order::query();

        if(request('date')){
            $date = request('date');                
            $result->where('fecha_inicio', $date);
        }

        return  $result->orderBy("hora_inicio", 'DESC')
                       ->paginate(999)
                       ->withQueryString();


    }

}
