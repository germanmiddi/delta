<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Client extends Model
{
    
    protected $fillable = [
        'fullname',
        'dni',
        'email',
        'phone',
        'cellphone',
        'client_type',
        'company_id',
        'price',
        'notes'
    ];
    
    use HasFactory;
    use SoftDeletes;

    public function address(){
        return $this->hasOne(Address::class)->withDefault(function() {
            return new Address();
        });
    } 
    
    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }
     
}
