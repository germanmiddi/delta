<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    
    protected $fillable = [
        'razon_social',
        'cuit',
        'billing_type'
    ];
    
    use HasFactory;
    use SoftDeletes;
}
