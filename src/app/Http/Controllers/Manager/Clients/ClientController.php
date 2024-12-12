<?php

namespace App\Http\Controllers\Manager\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Client;
use App\Models\Address;
use App\Models\Company;
use App\Models\State;
use App\Models\City;
use App\Models\Order;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return  Inertia::render('Manager/Clients/List',
        [
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
        //
        return  Inertia::render('Manager/Clients/Create',[
                    'empresas' => Company::all(),
                    //'states'  => State::all()
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
            DB::commit();
            return Redirect::route('clients')->with(['toast' => ['message' => 'Cliente creado correctamente', 'status' => '200']]);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return Redirect::route('clients')->with(['toast' => ['message' => 'Se ha producido un error', 'status' => '203']]);
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
    public function edit(Client $client)
    {      
            return  Inertia::render('Manager/Clients/Edit',[
                'empresas' => Company::all(),
                'states'  => State::all(),
                //'city'  => City::where('zipCode', $client->address->zipcode)->get(),
                'cliente' => $client,
                'address_client' => $client->address()->get()
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
            Client::where('id', $request->id)->update([
                'fullname' => $request->fullname,
                'dni' => $request->dni,
                'email' => $request->email,
                'phone' => $request->phone,
                'cellphone' => $request->cellphone,
                'client_type' => $request->client_type,
                'company_id' => $request->company_id,
                'price' => $request->price ?? 0,
                'notes' => $request->notes
            ]);     

            Address::where('client_id', $request->id)->update([
                'google_address' => $request->address['google_address'] ?? null,
                'google_area1' => $request->address['google_area1'] ?? null,
                'google_postal_code' => $request->address['google_postal_code'] ?? null,
                'google_latitude' => $request->address['google_latitude'] ?? null,
                'google_longitude' => $request->address['google_longitude'] ?? null,
                'notes' => $request->address['notes'] ?? null
            ]); 
            
            // CONTROLA SI SE DESEAN ACTUALIZAR LAS ORDENES PENDIENTES
            if($request['update_price']){
                $orders = Order::where('client_id', $request->id)->where('status_id','<','6')->get();
                foreach ($orders as $key => $data) {
                    Order::where('id', $data->id)->update([
                        'unit_price' => $request['price'] ?? 0,
                        'total_price' => ($request['price'] ?? 0) * $data->service->count(),
                    ]); 
                }
            }
            DB::commit();
            return Redirect::route('clients')->with(['toast' => ['message' => 'Cliente actualizado correctamente', 'status' => '200']]);
        } catch (\Throwable $th) {
            DB::rollback();
            return Redirect::route('clients')->with(['toast' => ['message' => 'Se ha producido un error', 'status' => '203']]);
        }
        
    }
    public function update_dashboard(Request $request){

        DB::beginTransaction();
        try {
            Client::where('id', $request->id)->update([
                'cellphone' => $request->cellphone,
            ]);   
            DB::commit();
            return response()->json(['message'=>'Cliente actualizado correctamente','title'=>'Dashboard'], 200);
        }catch (\Throwable $th) {
            DB::rollback();
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
    public function fulllist(){
        return Client::orderBy('id', 'DESC')->get();
    }
    public function list(){


        $clients = Client::orderBy("created_at", 'DESC');

        if(request('search')){
            $clients->where('id', 'like', '%'.request('search').'%')
                    ->orWhere('fullname', 'like', '%'.request('search').'%')
                    ->orWhere('dni', 'like', '%'.request('search').'%')
                    ->orWhere('email', 'like', '%'.request('search').'%')
                    ->orWhere('cellphone', 'like', '%'.request('search').'%');
       
            $clients->orWhereHas('address', function($query) {
                $query->where('google_address', 'like', '%'.request('search').'%');
                      ;

            });
        }
        $length = request('length') ?? 50;

        return  $clients->paginate($length)
                        ->withQueryString()
                        ->through(fn ($client) => [
                            'id'        => $client->id,
                            'fullname'  => $client->fullname,
                            'email'     => $client->email,
                            'address'    => $client->address()->get(),
                            'cellphone' => $client->cellphone,
                            'price'     => $client->price,
                        ]);
    }

    public function getCityByCp(){
        return City::where('zipcode', request('cp'))->get();
    }

}
