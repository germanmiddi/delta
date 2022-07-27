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
        'notes'
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
