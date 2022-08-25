<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ServicesTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
                [
                    'type' => 'ENVIO',
                    'description' => 'Corresponde a un tipo de servicio disponible para el envio'
                ],
                [
                    'type' => 'CAMBIO',
                    'description' => 'Corresponde a un tipo de servicio para realizar un cambio de volquete'
                ],
                [
                    'type' => 'RETIRO',
                    'description' => 'Corresponde a un tipo de servicio para el retiro de volquete'
                ],
        ];

        ServiceType::insert($types);
        
    }
}
