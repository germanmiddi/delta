<?php

namespace App\Http\Controllers\Manager\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Client;
use App\Models\Address;
use Illuminate\Support\Facades\Redirect;

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
        return  Inertia::render('Manager/Clients/List');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return  Inertia::render('Manager/Clients/Create'); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client = new Client;
        $client->fullname = $request->input('fullname');
        $client->dni = $request->input('dni');
        $client->email = $request->input('email');
        $client->phone = $request->input('phone');
        $client->cellphone = $request->input('cellphone');
        $client->client_type = $request->input('client_type');
        $client->company_id = $request->input('company_id');
        $client->price = $request->input('price');
        $client->notes = $request->input('notes');

        $client->save();

        $adrc = new Address;
        $adrc->client_id = $client->id;
        $adrc->state_id  = $request->input('state_id');
        $adrc->city_id = $request->input('city_id');
        $adrc->zipcode = $request->input('zipcode');
        $adrc->street = $request->input('street');
        $adrc->strnum = $request->input('strnum');
        $adrc->floor  = $request->input('floor');
        // $adrc->appartment = $request->input('appartment');
        $adrc->notes = $request->input('notesAdrc');

        $adrc->save();

        return Redirect::route('clients');
               

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

        return  Client::orderBy("created_at", 'DESC')
                        ->paginate(999)
                        ->withQueryString()
                        ->through(fn ($client) => [
                            'id'        => $client->id,
                            'fullname'  => $client->fullname,
                            'email'     => $client->email,
                            'cellphone' => $client->cellphone,
                        ]); 
    }

}
