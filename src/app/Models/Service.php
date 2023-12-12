<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'services';

    protected $fillable = [
        'id',
        'date',
        'time',
        'order_id',
        'status_id',
        'driver_id',
        'type_id',
        'finish',
        'price',
        'created_ay,D
'
    ];

    public function type()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function status()
    {
        return $this->belongsTo(ServiceStatus::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
