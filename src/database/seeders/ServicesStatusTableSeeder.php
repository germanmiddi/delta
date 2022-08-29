<?php

namespace Database\Seeders;

use App\Models\ServiceStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ServicesStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = [
                [
                    'status' => 'PENDIENTE',
                    'description' => 'Servicio pendiente de envio'
                ],
                [
                    'status' => 'EN CURSO',
                    'description' => 'Servicio se encuentra en proceso'
                ],
                [
                    'status' => 'FINALIZADO',
                    'description' => 'Servicio finalizado'
                ],
                [
                    'status' => 'CANCELADO',
                    'description' => 'Servicio cancelado'
                ],
        ];

        ServiceStatus::insert($status);
        
    }
}
