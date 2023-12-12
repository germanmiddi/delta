<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    protected $fillable = [
        'client_id',
        'unit_price',
        'total_price',
        'status_id',
        'created_by',
        'payment',
        'notes',
        'payment_type',
        'collector'
    ];

    use HasFactory;
    use SoftDeletes;


    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function service()
    {
        return $this->hasMany(Service::class);
    }

    public function dateFormat(){
        return 'HOLA';//$this->total_price;
    }

    public function calculate_price(){
        $totalPrice = 0;

        // Recorre todos los servicios asociados a la orden y suma sus precios
        foreach ($this->service as $service) {
            $totalPrice += $service->price;
        }

        return $totalPrice;
    }

}
