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
        'created_by'
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

}
