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
        //dd($request);
        DB::beginTransaction();
        try {
            $msg = '';
            //dd($request);
            // VERIFICO SI NO SE ENVIA EL CLIENTE Y TAMPOCO SE CREARA UNO NUEVO.
            if($request->input('client_id') == null && !$request->input('newClient')){
                DB::rollback();
                return Redirect::route('orders')->with(['toast' => ['message' => 'Debe seleccionar un cliente', 'status' => '203']]);
            }
            // VERIFICO SI SE CREARÁ UN NUEVO CLIENTE
            if($request->input('newClient')){
                $client = new Client;
                $client->fullname = $request->input('fullname');
                $client->dni = $request->input('dni');
                $client->email = $request->input('email');
                $client->phone = $request->input('phone');
                $client->cellphone = $request->input('cellphone');
                $client->client_type = $request->input('client_type');
                $client->company_id = $request->input('company_id');
                $client->price = $request->input('price') ?? 0;
                $client->notes = $request->input('notes');

                $client->save();

                $adrc = new Address;
                $adrc->client_id = $client->id;
                $adrc->google_address = $request->input('google_address') ?? '';
                $adrc->google_area1 = $request->input('google_area1');
                $adrc->google_postal_code = $request->input('google_postal_code');
                $adrc->google_latitude  = $request->input('google_latitude');
                $adrc->google_longitude  = $request->input('google_longitude');
                $adrc->notes = $request->input('notesAdrc');

                $adrc->save();

                $id_client = $client->id;
                $price_client = $client->price;
                $msg = 'Cliente creado correctamente - ';
            }else{
                $id_client =  $request->input('client_id');
                $price_client =  $request->input('price');
            }

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
    
    
                if($request->input('driver') && $request->input('date') && $request->input('time')){
                    $order_status_id = OrderStatus::select('id')->where('status', 'ENTREGADO')->first();
                    $service_status_id = ServiceStatus::select('id')->where('status', 'FINALIZADO')->first();
                }else{
                    $service_status_id = ServiceStatus::select('id')->where('status', 'PENDIENTE')->first();
                    $order_status_id = OrderStatus::select('id')->where('status', 'PROGRAMADO')->first();
                }
    
                $service_type_id = ServiceType::select('id')->where('type', 'ENVIO')->first();
                $order = new Order;
    
                $order->unit_price  = $price_client;
                $order->total_price = $price_client;
                $order->client_id   = $id_client;
                $order->status_id   = $order_status_id['id'];
                $order->created_by  = Auth::user()->id;

                $order->notes       = $request->input('notes');
    
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
                $msg .= 'Pedido creado correctamente';
                return Redirect::route('orders')->with(['toast' => ['message' => $msg, 'status' => '200']]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Redirect::route('orders')->with(['toast' => ['message' => 'Se ha producido un error', 'status' => '203']]);
        }
        
    }

    public function storedashboard(Request $request)
    {
        //dd($request->form);
        DB::beginTransaction();
        try {
            $msg = '';
            // VERIFICO SI NO SE ENVIA EL CLIENTE Y TAMPOCO SE CREARA UNO NUEVO.
            if(!isset($request->form['client_id']) && !$request->form['newClient']){
                DB::rollback();
                return response()->json(['message'=>'Debe seleccionar un cliente','title'=>'Dashboard'], 203);
            }
            // VERIFICO SI SE CREARA UN NUEVO CLIENTE
            if($request->form['newClient']){
                $client = new Client;
                $client->fullname = $request->form['fullname_new'] ?? '';
                $client->price = $request->form['price_new'] ?? 0;
                $client->save();

                $adrc = new Address;
                $adrc->client_id = $client->id;
                $adrc->google_address = $request->form['google_address_new'] ?? '';
                $adrc->google_area1 = $request->form['google_area1_new']  ?? '';
                $adrc->google_postal_code = $request->form['google_postal_code_new']  ?? '';
                $adrc->google_latitude  = $request->form['google_latitude_new']  ?? null;
                $adrc->google_longitude  = $request->form['google_longitude_new']  ?? null;

                $adrc->save();

                $id_client = $client->id;
                $price_client = $client->price;
                $msg = 'Cliente creado correctamente - ';
            }else{
                $id_client = $request->form['client_id'];
                $price_client = $request->form['price_unit_new'];

                Client::where('id', $request->form['client_id'])->update([
                    'price' => $price_client,
                ]);
            }

            if(isset($request->form['date'])){
                $input_date = $request->form['date'];
                $input_date  = date('Y-m-d', strtotime($input_date));
            }else{
                $input_date  = null;
            }

            if(isset($request->form['time'])){
                $input_time = $request->form['time'];
                $input_time   = date('H:i', strtotime( $input_time['hours'] . ':' . $input_time['minutes'])  );
            }else{
                $input_time   = null;
            }

            if(isset($request->form['driver']) && isset($request->form['date']) && isset($request->form['time'])){
                $order_status_id = OrderStatus::select('id')->where('status', 'ENTREGADO')->first();
                $service_status_id = ServiceStatus::select('id')->where('status', 'FINALIZADO')->first();
            }else{
                $service_status_id = ServiceStatus::select('id')->where('status', 'PENDIENTE')->first();
                $order_status_id = OrderStatus::select('id')->where('status', 'PROGRAMADO')->first();
            }
            $service_type_id = ServiceType::select('id')->where('type', 'ENVIO')->first();

            // CONTROLO CHOFER
            if(isset($request->form['driver'])){
                $driver_id = $request->form['driver'];
            }else{
                $driver_id = null;
            }

            $order = new Order;

            $order->unit_price    = $price_client;
            $order->total_price    = $price_client;
            $order->client_id    = $id_client;
            $order->status_id    = $order_status_id['id'];
            $order->created_by    = Auth::user()->id;

            $order->save();

            $service = new Service;

            $service->date = $input_date;
            $service->time = $input_time;
            $service->order_id = $order->id;
            $service->status_id = $service_status_id['id'];
            $service->driver_id =  $driver_id;
            $service->type_id = $service_type_id['id'];
            $service->save();

            DB::commit();
            $msg .= 'Pedido creado correctamente';
            return response()->json(['message'=>$msg,'title'=>'Dashboard'], 200);
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            return response()->json(['message'=>'Se ha producido un error','title'=>'Dashboard'], 203);
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
            'orden'   => $order,
            'services' => $order->service()->with('type')->with('driver')->with('status')->get()
            
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
            if($request->input('service.date')){
                $input_date = $request->input('service.date');
                $fecha_inicio  = date('Y-m-d', strtotime($input_date));
            }else{
                $fecha_inicio  = null;
            }

            if($request->input('service.time')){
                $input_time = $request->input('service.time');
                $hora_inicio   = date('H:i', strtotime( $input_time['hours'] . ':' . $input_time['minutes'])  );
            }else{
                $hora_inicio   = null;
            }

            Order::where('id', $request->id)->update([
                'unit_price' => $request->unit_price,
                'total_price'  => $request->total_price,
                'client_id' => $request->client_id,
                'status_id'  => $request->status_id,
                'notes'     => $request->input('notes'),
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
                    if($request->form['driver_id'] && $request->form['time'] && $request->form['date']){
                        
                        // Controla el formato de fecha y hora antes de almacenar...
                        if($request->form['date']){
                            $input_date = $request->form['date'];
                            $fecha_inicio  = date('Y-m-d', strtotime($input_date));
                        }else{
                            $fecha_inicio  = null;
                        }
                        
                        if($request->form['time']){
                            $input_time = $request->form['time'];
                            $hora_inicio   = date('H:i', strtotime( $input_time['hours'] . ':' . $input_time['minutes'])  );
                        }else{
                            $hora_inicio   = null;
                        }
                        
                        if(ServiceType::select('id')->where('type', 'RETIRO')->first()->id == Service::select('type_id')->where('id', $request->form['service_id'])->first()->type_id){
                            $order_status_id = OrderStatus::select('id')->where('status', 'RETIRADO')->first();
                        }else{
                            $order_status_id = OrderStatus::select('id')->where('status', 'ENTREGADO')->first();
                        }

                        $service_status_id = ServiceStatus::select('id')->where('status', 'FINALIZADO')->first();

                        Order::where('id', $request->form['order_id'])->update([
                            'status_id'  => $order_status_id['id']
                        ]);

                        Service::where('id', $request->form['service_id'])->update([
                            'date' => $fecha_inicio,
                            'time' => $hora_inicio,
                            'driver_id' => $request->form['driver_id'],
                            'status_id'  => $service_status_id['id'],
                            'finish'  => true
                        ]);
                    
                    }else{
                        return response()->json(['message'=>'Debe completar todos los datos','title'=>'Dashboard'], 203);
                    }

                    break;
                case 2: // GENERAR CAMBIO
                    // SETEO LAS VARIABLES DE FECHA - HORA
                    // Controla el formato de fecha y hora antes de almacenar.
                    if(isset($request->form['date'])){
                        $input_date = $request->form['date'];
                        $input_date  = date('Y-m-d', strtotime($input_date));
                    }else{
                        $input_date  = null;
                    }
        
                    if(isset($request->form['time'])){
                        $input_time = $request->form['time'];
                        $input_time   = date('H:i', strtotime( $input_time['hours'] . ':' . $input_time['minutes'])  );
                    }else{
                        $input_time   = null;
                    }
        
                    if(isset($request->form['driver_id']) && isset($request->form['date']) && isset($request->form['time'])){
                        $order_status_id = OrderStatus::select('id')->where('status', 'RETIRADO')->first();
                        $service_status_id = ServiceStatus::select('id')->where('status', 'FINALIZADO')->first();
                    }else{
                        $service_status_id = ServiceStatus::select('id')->where('status', 'PENDIENTE')->first();
                        $order_status_id = OrderStatus::select('id')->where('status', 'PROGRAMADO')->first();
                    }
                    $service_type_id = ServiceType::select('id')->where('type', 'CAMBIO')->first();

                    // GENERO UN NUEVO SERVICIO
                    $service = new Service;
        
                    $service->date = $input_date;
                    $service->time = $input_time;
                    $service->order_id = $request->form['order_id'];
                    $service->status_id = $service_status_id['id'];
                    $service->type_id = $service_type_id['id'];
                    $service->driver_id = $request->form['driver_id'];
                    $service->save();

                    // BUSCAR PRECIO DEL CLIENTE
                    //$client = Client::find($request->form['client']['id']);
                    
                    // ACTUALIZAR PRECIO ORDERS 
                    $total = $request->form['total_price'] + $request->form['price_unit_new'];
                    
                    Order::where('id', $request->form['order_id'])->update([
                        'unit_price'   => $request->form['price_unit_new'],
                        'total_price'   => $total,
                        'status_id'     => $order_status_id['id']
                    ]);
                    
                    break;

                case 3: // GENERAR RETIRO

                    // SETEO LAS VARIABLES DE FECHA - HORA
                   // Controla el formato de fecha y hora antes de almacenar. 
                   if(isset($request->form['date'])){
                        $input_date = $request->form['date'];
                        $input_date  = date('Y-m-d', strtotime($input_date));
                    }else{
                        $input_date  = null;
                    }
        
                    if(isset($request->form['time'])){
                        $input_time = $request->form['time'];
                        $input_time   = date('H:i', strtotime( $input_time['hours'] . ':' . $input_time['minutes'])  );
                    }else{
                        $input_time   = null;
                    }
        
                    if(isset($request->form['driver_id']) && isset($request->form['date']) && isset($request->form['time'])){
                        $order_status_id = OrderStatus::select('id')->where('status', 'RETIRADO')->first();
                        $service_status_id = ServiceStatus::select('id')->where('status', 'FINALIZADO')->first();
                    }else{
                        $service_status_id = ServiceStatus::select('id')->where('status', 'PENDIENTE')->first();
                        $order_status_id = OrderStatus::select('id')->where('status', 'PROGRAMADO')->first();
                    }
                    $service_type_id = ServiceType::select('id')->where('type', 'RETIRO')->first();

                    // GENERO UN NUEVO SERVICIO
                    $service = new Service;
        
                    $service->date = $input_date;
                    $service->time = $input_time;
                    $service->order_id = $request->form['order_id'];
                    $service->status_id = $service_status_id['id'];
                    $service->type_id = $service_type_id['id'];
                    $service->driver_id = $request->form['driver_id'];
                    $service->save();

                    // ACTUALIZO LA ORDEN
                    Order::where('id', $request->form['order_id'])->update([
                        //'total_price'   => $total,
                        'status_id'     => $order_status_id['id']
                    ]);

                    break;
                case 4: // CANCELAR
                    $order_status_id = OrderStatus::select('id')->where('status', 'CANCELADO')->first();
                    $service_status_id = ServiceStatus::select('id')->where('status', 'CANCELADO')->first();
                    Order::where('id', $request->form['order_id'])->update([
                        'status_id'  => $order_status_id['id']
                    ]);

                    Service::where('id', $request->form['service_id'])->update([
                        'status_id'  => $service_status_id['id'],
                        'finish'  => true
                    ]);
                    break;
                case 5: // PAGAR
                        Order::where('id', $request->form['order_id'])->update([
                            'payment'  => true
                        ]);
                        break;
                case 6: // ACTUALIZAR NOTES
                    Order::where('id', $request->form['order_id'])->update([
                        'notes'  => $request->form['notes']
                    ]);
                    break;
                
                default:

                    # code...
                    break;
                }
                DB::commit();
                return response()->json(['message'=>'El pedido N° '.$request->form['order_id'].' se ha actualizado correctamente','title'=>'Dashboard'], 200);
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
                        'client'   => $order->client()->with('address')->get(),
                        'services' => $order->service()->with('type')->with('driver')->with('status')->whereRaw($where_services)->get(),
                        'status'   => $order->status->status,
                    ]); 
    }


    public function listdashboard(){

        $result = Order::query();
        //$result->join('services','orders.id', '=', 'services.order_id');

        if(request('date')){
            $date = json_decode(request('date'));
            $from = date('Y-m-d', strtotime($date[0]));
            $to = date('Y-m-d', strtotime("+1 day", strtotime($date[1])));         

            //$result->join('services as s', 'orders.id', '=', 's.order_id')->where('s.date', '>=',  $from);//->Where('s.date', '<',  $to);
            
            $result->whereHas('service', function($q) use ($from, $to){
                $q->where('date','>=', $from)
                  ->where('date', '<', $to);
            });
        }

        if(request('client')){
            $client_filter = json_decode(request('client'));               
            $result->where('client_id', $client_filter);
        }

        if(request('driver')){
            $driver_filter = json_decode(request('driver'));               
            $result->whereIn('orders.id', function ($sub) use ($driver_filter) {
                $sub->selectRaw('orders.id')
                    ->from('orders')
                    ->join('services', 'orders.id', '=', 'services.order_id')
                    ->whereIn('services.id', function ($sub) {
                        $sub->selectRaw('max(services.id)')
                            ->from('orders')
                            ->join('services', 'orders.id', '=', 'services.order_id')
                            ->groupby('orders.id');
                    })
                    ->where('services.driver_id',$driver_filter);
            });
        }

        if(request('street')){   
            $street_filter = json_decode(request('street'));  
            $result->whereIn('id', function ($sub) use($street_filter) {
                        $sub->selectRaw('orders.id')
                            ->from('orders')
                            ->join('clients', 'orders.client_id', '=', 'clients.id')
                            ->join('addresses', 'clients.id', '=', 'addresses.client_id')
                            ->where('addresses.google_address', 'LIKE', '%'.$street_filter.'%');
                    });
        }

        return $result->orderBy("orders.created_at", 'DESC')
                    ->paginate(999)
                    ->withQueryString()
                    ->through(fn ($order) => [
                        'order' => $order,
                        'service' => $order->service()->latest()->with('driver','status','type')->first(),
                        'client'   => $order->client()->with('address')->with('company')->first(),
                        'order_status' => $order->status()->first(),
                        'company' => $order->client()->with('company')->first(),
                    ]);    
    }

    /* public function listdashboard(){

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
                        'order_unit_price' => $order->unit_price,
                        'company' => $order->client()->with('company')->first(),
                    ]);                        
    } */

    public function listdashboardmap(){
        $result = Order::query();
        
        if(request('date')){
            $date = json_decode(request('date'));
            $from = date('Y-m-d', strtotime($date[0]));
            $to = date('Y-m-d', strtotime("+1 day", strtotime($date[1])));         

            //$result->join('services as s', 'orders.id', '=', 's.order_id')->where('s.date', '>=',  $from);//->Where('s.date', '<',  $to);
            
            $result->whereHas('service', function($q) use ($from, $to){
                $q->where('date','>=', $from)
                  ->where('date', '<', $to);
            });
        }

        if(request('client')){
            $client_filter = json_decode(request('client'));               
            $result->where('client_id', $client_filter);
        }

        if(request('driver')){
            $driver_filter = json_decode(request('driver'));               
            $result->whereIn('orders.id', function ($sub) use ($driver_filter) {
                $sub->selectRaw('orders.id')
                    ->from('orders')
                    ->join('services', 'orders.id', '=', 'services.order_id')
                    ->whereIn('services.id', function ($sub) {
                        $sub->selectRaw('max(services.id)')
                            ->from('orders')
                            ->join('services', 'orders.id', '=', 'services.order_id')
                            ->groupby('orders.id');
                    })
                    ->where('services.driver_id',$driver_filter);
            });
        }

        if(request('street')){   
            $street_filter = json_decode(request('street'));  
            $result->whereIn('id', function ($sub) use($street_filter) {
                        $sub->selectRaw('orders.id')
                            ->from('orders')
                            ->join('clients', 'orders.client_id', '=', 'clients.id')
                            ->join('addresses', 'clients.id', '=', 'addresses.client_id')
                            ->where('addresses.google_address', 'LIKE', '%'.$street_filter.'%');
                    });
        }

        return $result->orderBy("orders.created_at", 'DESC')
                    ->paginate(999)
                    ->withQueryString()
                    ->through(fn ($order) => [
                        'client'   => $order->client()->with('address')->with('company')->first(),
                    ]);    

        /* return  $result->where('status_id','<','6')
                       ->paginate(999)
                       ->withQueryString()
                       ->through(fn ($order) => [
                        'client'   => $order->client()->with('address')->with('company')->first(),
                    ]);      */                   


    }

    public function listservices($id){
        //$order =  Order::where("id", $id)->first();
        //dd($order->service);
        //return $order->service;
/* 
        $result = Order::query();
        $result->where('id', $id); */

        return  Order::where("id", $id)
                        ->paginate(999)
                        ->withQueryString()
                        ->through(fn ($order) => [
                            'services'  => $order->service()->with('driver','status','type')->get()
                        ]);
    }

}
