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
                    $input_date  = $request->input('date');
                }

                if($request->input('time')){
                    $input_time = $request->input('time');
                    $input_time   = date('H:i', strtotime( $input_time['hours'] . ':' . $input_time['minutes'])  );
                }else{
                    $input_time   = $request->input('time');
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

                        if($request->form['order_status']['id'] > 1 ){
                            $order_status_id['id'] = $request->form['order_status']['id'];
                        }

                        Order::where('id', $request->form['service']['order_id'])->update([
                            'status_id'  => $order_status_id['id']
                        ]);
    
                        Service::where('id', $request->form['service']['id'])->update([
                            'date' => $fecha_inicio,
                            'time' => $hora_inicio,
                            'driver_id' => $request->form['service']['driver_id']
                        ]);
                    }else{
                        return response()->json(['message'=>'Debe seleccionar un Chofer','title'=>'Dashboard'], 203);
                    }

                    break;
                case 2: // PASAR A ENTREGADO
                    $order_status_id = OrderStatus::select('id')->where('status', 'ENTREGADO')->first();
                    $service_status_id = ServiceStatus::select('id')->where('status', 'FINALIZADO')->first();
                    Order::where('id', $request->form['service']['order_id'])->update([
                        'status_id'  => $order_status_id['id']
                    ]);

                    Service::where('id', $request->form['service']['id'])->update([
                        'status_id'  => $service_status_id['id']
                    ]);
                    break;
                case 3: //PASAR A RETIRADO
                    $order_status_id = OrderStatus::select('id')->where('status', 'RETIRADO')->first();
                    $service_status_id = ServiceStatus::select('id')->where('status', 'FINALIZADO')->first();
                    Order::where('id', $request->form['service']['order_id'])->update([
                        'status_id'  => $order_status_id['id']
                    ]);

                    Service::where('id', $request->form['service']['id'])->update([
                        'status_id'  => $service_status_id['id']
                    ]);
                    break;
                case 4: // GENERAR CAMBIO
                    // FINALIZAR SERVICIO
                    Service::where('id', $request->form['service']['id'])->update([
                        'finish'  => true
                    ]);

                    // SETEO LAS VARIABLES DE FECHA - HORA

                    $input_date = $request->form['service']['date_new'];
                    $input_date  = date('Y-m-d', strtotime($input_date));

                    $input_time = $request->form['service']['time_new'];
                    $input_time   = date('H:i', strtotime( $input_time['hours'] . ':' . $input_time['minutes'])  );

        
        
                    // GENERO LOS ESTADOS
                    $service_status_id = ServiceStatus::select('id')->where('status', 'PENDIENTE')->first();
                    $service_type_id = ServiceType::select('id')->where('type', 'CAMBIO')->first();
        

                    // GENERO UN NUEVO SERVICIO
                    $service = new Service;
        
                    $service->date = $input_date;
                    $service->time = $input_time;
                    $service->order_id = $request->form['service']['order_id'];
                    $service->status_id = $service_status_id['id'];
                    $service->type_id = $service_type_id['id'];
                    $service->driver_id = $request->form['service']['driver_id_new'];
                    $service->save();

                    // BUSCAR PRECIO DEL CLIENTE
                    $client = Client::find($request->form['client']['id']);
                    
                    // ACTUALIZAR PRECIO ORDERS 
                    $total = $request->form['order_total_price'] + $client['price'];
                    
                    Order::where('id', $request->form['service']['order_id'])->update([
                        'total_price'  => $total
                    ]);
                    
                    break;
                case 5: // GENERAR RETIRO
                    // FINALIZAR SERVICIO
                    Service::where('id', $request->form['service']['id'])->update([
                        'finish'  => true
                    ]);

                    // SETEO LAS VARIABLES DE FECHA - HORA
                    $input_date = $request->form['service']['date_new'];
                    $input_date  = date('Y-m-d', strtotime($input_date));

                    $input_time = $request->form['service']['time_new'];
                    $input_time   = date('H:i', strtotime( $input_time['hours'] . ':' . $input_time['minutes'])  );
        
                    // GENERO LOS ESTADOS
                    $service_status_id = ServiceStatus::select('id')->where('status', 'PENDIENTE')->first();
                    $service_type_id = ServiceType::select('id')->where('type', 'RETIRO')->first();
        
                    // GENERO UN NUEVO SERVICIO
                    if(isset($request->form['service']['driver_id_new'])){
                        $driver_id_new = $request->form['service']['driver_id_new'];
                    }else{
                        $driver_id_new = null;
                    }

                    $service = new Service;
        
                    $service->date = $input_date;
                    $service->time = $input_time;
                    $service->order_id = $request->form['service']['order_id'];
                    $service->status_id = $service_status_id['id'];
                    $service->type_id = $service_type_id['id'];
                    $service->driver_id = $driver_id_new;
                    $service->save();

                    break;
                case 6: // CANCELAR
                    $order_status_id = OrderStatus::select('id')->where('status', 'CANCELADO')->first();
                    $service_status_id = ServiceStatus::select('id')->where('status', 'CANCELADO')->first();
                    Order::where('id', $request->form['service']['order_id'])->update([
                        'status_id'  => $order_status_id['id']
                    ]);

                    Service::where('id', $request->form['service']['id'])->update([
                        'status_id'  => $service_status_id['id'],
                        'finish'  => true
                    ]);
                    break;
                default:
                    # code...
                    break;
                }
                DB::commit();
                return response()->json(['message'=>'El pedido N° '.$request->form['service']['order_id'].' se ha actualizado correctamente','title'=>'Dashboard'], 200);
        } catch (\Throwable $th) {
            dd($th);
            db::rollback();
            return response()->json(['message'=>'Se ha producido un error','title'=>'Dashboard'], 203);
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
        //DB::enableQueryLog();
        
        $result = Order::query();
        $result->select('orders.*');

        $length = request('length');
        
        $where_services = '1 = 1 ';

        if(request('date')){

            $date = json_decode(request('date'));
            
            $from = date('Y-m-d', strtotime($date[0]));
            $to = date('Y-m-d', strtotime("+1 day", strtotime($date[1])));         

            //$result->join('services as s', 'orders.id', '=', 's.order_id')->where('s.date', '>=',  $from);//->Where('s.date', '<',  $to);

            $result->whereHas('service', function($q) use ($from, $to){
                $q->where('date','>=', $from)
                  ->where('date', '<', $to);
            });

            $where_services .= ' AND services.date >= "' .  $from . '" AND services.date < "' .  $to . '"';

        }

        if(request('status')){  
            $status_filter = json_decode(request('status'));        
            $result->where('orders.status_id', $status_filter);
        }

        if(request('driver')){
            $driver_filter = json_decode(request('driver'));               
            $result->join('services as s', 'orders.id', '=', 's.order_id')->where('s.driver_id', $driver_filter);
            $where_services .= 'AND services.driver_id = ' .  $driver_filter;
        }
        
        if(request('company')){
            $company_filter = json_decode(request('company'));               
            $result->join('clients as c', 'orders.client_id', '=', 'c.id')->where('c.company_id', $company_filter);
        }
        
        
        if(request('street')){   
            $street_filter = json_decode(request('street'));   

            $result->join('clients as c', 'orders.client_id', '=', 'c.id')
                    ->join('addresses as a', 'c.id', '=', 'a.client_id')
                    ->where('a.google_address', 'LIKE', '%'. $street_filter . '%');
        }

        if(request('client')){
            $client_filter = json_decode(request('client'));               
            $result->where('client_id', $client_filter);
        }

        return  $result->orderBy("created_at", 'DESC')
                    ->paginate($length)
                    ->withQueryString()
                    ->through(fn ($order) => [
                        'id'       => $order->id,
                        'creado' => Carbon::create($order->created_at)->format('d/m/Y H:i'),
                        //'h_inicio' => Carbon::create($order->service()->select('time')->latest()->get()->value('time'))->format('H:i'),
                        'client'   => $order->client()->with('address')->get(),
                        'services' => $order->service()->with('type')->with('driver')->with('status')->whereRaw($where_services)->get(),
                        'status'   => $order->status->status,
                    ]); 
                    /* 
                    $query = DB::getQueryLog();
                    dd($query);
                    */
    }


    public function listdashboard(){

        $result = Order::query();

        if(request('date')){
            $date = date('Y-m-d', strtotime(request('date')));
            $result->whereHas('service', function($q) use ($date){
                $q->where('date','<=', $date)
                  ->where('finish', false);
            });
        }
        return  $result->where('status_id','<','6')
                       ->orderBy("created_at", 'DESC')
                       ->paginate(999)
                       ->withQueryString()
                       ->through(fn ($order) => [
                        'service' => $order->service()->latest()->with('driver')->with('type')->first(),
                        'client'   => $order->client()->with('address')->with('company')->first(),
                        'order_status' => $order->status()->first(),
                        'order_total_price' => $order->total_price,
                        'company' => $order->client()->with('company')->first(),
                    ]);                        
    }

    public function listdashboardmap(){

        $result = Order::query();

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
