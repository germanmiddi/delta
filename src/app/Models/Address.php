<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{

    protected $fillable = [
        'client_id',
        'state_id',
        'city_id',
        'zipcode',
        'street',
        'strnum',
        'floor',
        'appartment',
        'notes',
        'google_address',
        'google_postal_code',
        'google_area1',
        'google_latitude',
        'google_longitude',
    ];

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function state(){
        return $this->belongsTo(State::class);
    }    

    public function city(){
        return $this->belongsTo(City::class);
    }    
    
    use SoftDeletes;


}
