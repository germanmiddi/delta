<?php

namespace App\Http\Controllers\Manager\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;


use App\Models\State;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Driver;
use App\Models\Client;
use App\Models\Address;
use App\Models\Company;
use App\Models\Service;
use App\Models\ServiceStatus;
use App\Models\ServiceType;
use Carbon\Carbon;
use Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return  Inertia::render('Manager/Orders/List',[
            'clients' => Client::all(),
            'empresas' => Company::all(),
            'drivers' => Driver::all(),
            'status'   => OrderStatus::get(),
            'toast' => Session::get('toast')
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
                             'addresses.google_address',
                             'addresses.google_area1',
                             'addresses.google_postal_code',
                             'addresses.google_latitude',
                             'addresses.google_longitude',
                             'addresses.notes'
                             )
                    ->leftjoin('addresses','addresses.client_id', '=', 'clients.id' )
                    ->get();
        
        return  Inertia::render('Manager/Orders/Create', [
            'drivers' => Driver::all(),
            'clients' => $client,
            'states'  => State::all(),
            'empresas' => Company::all(),

                              
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
        DB::beginTransaction();
        try {
            if($request->input('client_id')){
                if($request->input('date')){
                    $input_date = $request->input('date');
                    $input_date  = date('Y-m-d', strtotime($input_date));
                }else{
                    $input_date = Carbon::now();
                    $input_date  = date('Y-m-d', strtotime($input_date));
                }

                if($request->input('time')){
                    $input_time = $request->input('time');
                    $input_time   = date('H:i', strtotime( $input_time['hours'] . ':' . $input_time['minutes'])  );
                }else{
                    $hora['hours'] = Carbon::now()->format('H');
                    $hora['minutes'] = Carbon::now()->format('i');
                    $input_time   = date('H:i', strtotime( $hora['hours'] . ':' . $hora['minutes'])  );
                }
    
    
                if($request->input('driver')){
                    $order_status_id = OrderStatus::select('id')->where('status', 'EN ENVIO')->first();
                }else{
                    $order_status_id = OrderStatus::select('id')->where('status', 'AGENDADO')->first();
                }
    
                $service_status_id = ServiceStatus::select('id')->where('status', 'PENDIENTE')->first();
                $service_type_id = ServiceType::select('id')->where('type', 'ENVIO')->first();
                $order = new Order;
    
                $order->unit_price    = $request->input('price');
                $order->total_price    = $request->input('price');
                $order->client_id    = $request->input('client_id');
                $order->status_id    = $order_status_id['id'];
                $order->created_by    = Auth::user()->id;
    
                $order->save();
    
                $service = new Service;
    
                $service->date = $input_date;
                $service->time = $input_time;
                $service->order_id = $order->id;
                $service->status_id = $service_status_id['id'];
                $service->driver_id =  $request->input('driver');
                $service->type_id = $service_type_id['id'];
                $service->save();
    
                DB::commit();
                return Redirect::route('orders')->with(['toast' => ['message' => 'Pedido creado correctamente', 'status' => '200']]);
            }else{
                DB::rollback();
                return Redirect::route('orders')->with(['toast' => ['message' => 'Debe seleccionar un cliente', 'status' => '203']]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return Redirect::route('orders')->with(['toast' => ['message' => 'Se ha producido un error', 'status' => '203']]);
        }
        
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
                             'addresses.google_address',
                             'addresses.google_area1',
                             'addresses.google_postal_code',
                             'addresses.google_latitude',
                             'addresses.google_longitude',
                             'addresses.notes'
                             )
                    ->leftjoin('addresses','addresses.client_id', '=', 'clients.id' )
                    ->get();
        
        return  Inertia::render('Manager/Orders/Edit', [
            'drivers' => Driver::all(),
            'empresas' => Company::all(),
            'clients' => $client,
            'service' => $order->service()->latest()->first(),
            //'states'  => State::all(),
            'orden'   => $order,
            
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
        DB::beginTransaction();
        try {
            $input_finicio = $request->input('service.date');
            $fecha_inicio  = date('Y-m-d', strtotime($input_finicio));

            $input_hinicio = $request->input('service.time');
            $hora_inicio   = date('H:i', strtotime( $input_hinicio['hours'] . ':' . $input_hinicio['minutes'])  );

            Order::where('id', $request->id)->update([
                'unit_price' => $request->unit_price,
                'total_price'  => $request->total_price,
                'client_id' => $request->client_id,
                'status_id'  => $request->status_id
            ]);

            Service::where('id', $request->service['id'])->update([
                'date' => $fecha_inicio,
                'time' => $hora_inicio,
                'driver_id' => $request->service['driver_id']
            ]);

            DB::commit();
            return Redirect::route('orders')->with(['toast' => ['message' => 'Pedido actualizado correctamente', 'status' => '200']]);
        } catch (\Throwable $th) {
            db::rollback();
            return Redirect::route('orders')->with(['toast' => ['message' => 'Se ha producido un error', 'status' => '203']]);
        }
    }

    public function updatedashboard(Request $request)
    {
        DB::beginTransaction();
        try {
            switch ($request->form['action']) { 
                case 1: // EDITAR
                    if($request->form['service']['driver_id']){
                        $input_finicio = $request->form['service']['date'];
                        $fecha_inicio  = date('Y-m-d', strtotime($input_finicio));
                        
                        $input_hinicio = $request->form['service']['time'];
                        $hora_inicio   = date('H:i', strtotime( $input_hinicio['hours'] . ':' . $input_hinicio['minutes'])  );
                        
                        $order_status_id = OrderStatus::select('id')->where('status', 'EN ENVIO')->first();
                        Order::where('id', $request->form['service']['order_id'])->update([
                            'status_id'  => $order_status_id['id']
                        ]);
    
                        Service::where('id', $request->form['service']['id'])->update([
                            'date' => $fecha_inicio,
                            'time' => $hora_inicio,
                            'driver_id' => $request->form['service']['driver_id']
                        ]);
                    }else{
                        return response()->json(['message'=>'Debe seleccionar un conductor','title'=>'Dashboard'], 203);
                    }

                    break;
                    
                    default:
                    # code...
                    break;
                }
                DB::commit();
                return response()->json(['message'=>'Se ha actualizado correctamente','title'=>'Dashboard'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message'=>'Se ha producido un error','title'=>'Dashboard'], 203);
            db::rollback();
        }
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
        
        /*if(request('client')){
            $client_filter = json_decode(request('client'));               
            $result->where('client_id', $client_filter);
        }*/

        /*if(request('status')){            
            $result->where('order_status',request('status'));
        }*/

        /*if(request('driver')){
            $driver_filter = json_decode(request('driver'));               
            $result->where('driver_id', $driver_filter);
        }*/

        /*if(request('company')){
            $company_filter = json_decode(request('company'));               
            $result->select('orders.*')->join('clients as c', 'orders.client_id', '=', 'c.id')->where('c.company_id', $company_filter);
        }*/

        /*if(request('street')){   
            $street_filter = json_decode(request('street'));          
            $result->select('orders.*')->join('addresses as a', 'orders.client_id', '=', 'a.client_id')
                ->where('a.street', 'LIKE', '%'. $street_filter . '%');
        }*/

        //TODO: Corregir los filtros de orders
        /*if(request('date')){

            $date = json_decode(request('date'));
            
            $from = date('Y-m-d', strtotime($date[0]));
            $to = date('Y-m-d', strtotime("+1 day", strtotime($date[1])));         
            $result->whereBetween('fecha_inicio', [$from, $to]);

        }*/

        //dd(Order::select('order_status')->distinct()->get());

        return $result->orderBy("created_at", 'DESC')
                    ->paginate($length)
                    ->withQueryString()
                    ->through(fn ($order) => [
                        'id'       => $order->id,
                        'f_inicio' => Carbon::create($order->service()->select('date')->latest()->get()->value('date'))->format('d/m/Y'),
                        'h_inicio' => Carbon::create($order->service()->select('time')->latest()->get()->value('time'))->format('H:i'),
                        'client'   => $order->client()->with('address')->get(),
                        'status'   => $order->status->status,
                    ]); 

    }


    public function listdashboard(){

        $result = Order::query();

        if(request('date')){
            $date = date('Y-m-d', strtotime(request('date')));
            $result->whereHas('service', function($q) use ($date){
                $q->where('date','<=', $date);
            });
        }

        return  $result->where('status_id','<','6')
                       ->orderBy("created_at", 'DESC')
                       ->paginate(999)
                       ->withQueryString()
                       ->through(fn ($order) => [
                        'service' => $order->service()->latest()->with('driver')->first(),
                        'client'   => $order->client()->with('address')->with('company')->first(),
                        'order_status' => $order->status()->first(),
                        'order_total_price' => $order->total_price,
                        'company' => $order->client()->with('company')->first(),
                    ]);                        
    }

    public function listdashboardmap(){

        $result = Order::query();

        //$date = Carbon::now();
        if(request('date')){
            $date = $date->format('Y-m-d');
            $date = date('Y-m-d', strtotime(request('date')));
            $result->whereHas('service', function($q) use ($date){
                $q->where('date', $date);
            });
        }

        return  $result->where('status_id','<','6')
                       ->paginate(999)
                       ->withQueryString()
                       ->through(fn ($order) => [
                        'client'   => $order->client()->with('address')->with('company')->first(),
                    ]);                        


    }

}
