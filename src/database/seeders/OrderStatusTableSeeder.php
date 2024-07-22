<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class OrderStatusTableSeeder extends Seeder
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
                    'status' => 'PROGRAMADO',
                    'description' => 'Pedido programado'
                ],
                [
                    'status' => 'PENDIENTE',
                    'description' => 'Pedido con chofer asignado pendiente de envio'
                ],
                [
                    'status' => 'EN ENVIO',
                    'description' => 'Pedido en proceso de envio'
                ],
                [
                    'status' => 'ENTREGADO',
                    'description' => 'Pedido se ha entregado al cliente'
                ],
                [
                    'status' => 'EN RETIRO',
                    'description' => 'Pedido en proceso de retiro'
                ],
                [
                    'status' => 'RETIRADO',
                    'description' => 'Pedido retirado'
                ],
                [
                    'status' => 'CANCELADO',
                    'description' => 'Pedido cancelado'
                ],
                [
                    'status' => 'ARCHIVADO',
                    'description' => 'Pedido archivado antes de eliminar'
                ],
        ];

        //OrderStatus::insert($status);
        foreach ($status as $statusData) {
            OrderStatus::updateOrCreate(
                ['status' => $statusData['status']],
                ['description' => $statusData['description']]
            );
        }
    }
}
