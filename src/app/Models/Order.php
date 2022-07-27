<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    protected $fillable = [
        'fecha_inicio',
        'hora_inicio',
        'fecha_retiro',
        'hora_retiro',
        'client_id',
        'driver_id',
        'order_status',
        'created_by'
    ];

    use HasFactory;
    use SoftDeletes;


    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

}
