<?php

namespace App\Http\Controllers\Manager\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\State;
use App\Models\Order;
use App\Models\Driver;
use App\Models\Client;
use App\Models\Address;
use App\Models\Company;
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
        return  Inertia::render('Manager/Orders/List',[
            'clients' => Client::all(),
            'empresas' => Company::all(),
            'drivers' => Driver::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $client = DB::table('clients')
                    ->select('clients.*', 
                             'addresses.zipcode',
                             'addresses.street',
                             'addresses.strnum',
                             'states.state_ltxt',
                             'cities.city_ltxt'
                             )
                    ->join('addresses','addresses.client_id', '=', 'clients.id' )
                    ->join('states','addresses.state_id', '=', 'states.id' )
                    ->join('cities','addresses.city_id', '=', 'cities.id' )
                    ->get();
        
        return  Inertia::render('Manager/Orders/Create', [
            'drivers' => Driver::all(),
            'clients' => $client,
            'states'  => State::all()
                              
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


        return Redirect::route('orders');
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
    public function edit(Order $order)
    {
        $client = DB::table('clients')
                    ->select('clients.*', 
                             'addresses.zipcode',
                             'addresses.street',
                             'addresses.floor',
                             'addresses.strnum',
                             'states.state_ltxt',
                             'cities.city_ltxt'
                             )
                    ->join('addresses','addresses.client_id', '=', 'clients.id' )
                    ->join('states','addresses.state_id', '=', 'states.id' )
                    ->join('cities','addresses.city_id', '=', 'cities.id' )
                    ->get();
        
        return  Inertia::render('Manager/Orders/Edit', [
            'drivers' => Driver::all(),
            'empresas' => Company::all(),
            'clients' => $client,
            'states'  => State::all(),
            'orden'   => $order          
        ]); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input_finicio = $request->input('fecha_inicio');
        $fecha_inicio  = date('Y-m-d', strtotime($input_finicio));

        $input_hinicio = $request->input('hora_inicio');
        $hora_inicio   = date('H:i', strtotime( $input_hinicio['hours'] . ':' . $input_hinicio['minutes'])  );

        $input_fretiro = $request->input('fecha_retiro');
        $fecha_retiro  = date('Y-m-d', strtotime($input_fretiro));

        $input_hretiro  = $request->input('hora_retiro');
        $hora_retiro    = date('H:i', strtotime( $input_hretiro['hours'] . ':' . $input_hretiro['minutes']) );

        Order::where('id', $request->id)->update([
            'fecha_inicio' => $fecha_inicio,
            'hora_inicio'  => $hora_inicio,
            'fecha_retiro' => $fecha_retiro,
            'hora_retiro'  => $hora_retiro,
            'client_id'    => $request->input('client_id'),
            'driver_id'    => $request->input('driver_id'),
        ]);

        return Redirect::route('orders');
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

        $result = Order::query();
        $length = request('length');

        

        /* if(request('street')){
            $street_filter = json_decode(request('paid'));                
            $result->whereIn('street', $paid_filter);
        } */
        
        if(request('client')){
            $client_filter = json_decode(request('client'));               
            $result->where('client_id', $client_filter);
        }

        if(request('driver')){
            $driver_filter = json_decode(request('driver'));               
            $result->where('driver_id', $driver_filter);
        }

        if(request('company')){
            $company_filter = json_decode(request('company'));               
            $result->select('orders.*')->join('clients as c', 'orders.client_id', '=', 'c.id')->where('c.company_id', $company_filter);
        }

        if(request('date')){

            $date = json_decode(request('date'));
            
            $from = date('Y-m-d', strtotime($date[0]));
            $to = date('Y-m-d', strtotime("+1 day", strtotime($date[1])));
            // dd($from, $to);            
            $result->whereBetween('created_at', [$from, $to]);

        }

        return $result->orderBy("created_at", 'DESC')
                    ->paginate($length)
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
            $date = date('Y-m-d', strtotime(request('date')));
            $result->where('fecha_inicio', $date)
                ->orWhere('fecha_retiro', $date);
        }

        return  $result->orderBy("hora_inicio", 'DESC')
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
                        'driver'   => $order->driver()->get(),
                        'status_txt' => $order->fecha_inicio == $date ? 'ENVIO' : 'RETIRO',
                        'h_service' => $order->fecha_inicio == $date 
                                       ? Carbon::create($order->hora_inicio)->format('H:i')
                                       : Carbon::create($order->hora_retiro)->format('H:i'), 
                    ]);                        


    }

}
