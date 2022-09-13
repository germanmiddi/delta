<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    
    protected $fillable = [
        'fullname',
        'dni',
        'phone',
        'cellphone',
        'email',
        'truck',
        'notes'
    ];

    use HasFactory;

    public function service()
    {
        return $this->hasMany(Service::class);
    }
}
