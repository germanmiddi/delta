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
                    'status' => 'AGENDADO',
                    'description' => 'Pedido agendado'
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
        ];

        OrderStatus::insert($status);
        
    }
}
