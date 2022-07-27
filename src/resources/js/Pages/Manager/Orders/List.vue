<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pedidos
            </h2>
            <a class="btn-blue" :href="route('orders.create')">
                Nuevo Pedido
            </a>  
        </template>
        
        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <table class="w-full whitespace-nowrap"> 
                        <tr class="text-left font-bold bg-blue-500 text-white">
                            <th class="px-6 py-4 text-center">ID</th>
                            <th class="px-6 py-4 text-center">Desde</th>
                            <th class="px-6 py-4 text-center">Hasta</th>
                            <th class="px-6 py-4 text-center">Cliente</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                        <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-100 focus-within:bg-gray-100 text-sm ">
                            <td class="border-t px-6 py-4 text-center">
                                {{order.id}}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                {{order.f_inicio}} <br> {{order.h_inicio}}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                {{order.f_retiro}} <br> {{order.h_retiro}}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                {{ order.client[0].address.street }} {{ order.client[0].address.strnum }} <br>
                                {{ order.client[0].address.zipcode }}

                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                {{order.status}}
                            </td>
                        </tr>
                    </table>    
                </div>                      
            </div>
        </div>  

    </AppLayout>
</template>
<script>

    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue';
    import JetButton from '@/Jetstream/Button.vue';

    export default defineComponent({
        components: {
            AppLayout,      
            JetButton,
        },
        data(){
            return{
                orders: ""
            }
        },
        methods:{
            async getOrders(){
                const get = `${route('orders.list')}` 

                const response = await fetch(get, {method:'GET'})
                this.orders = await response.json() 
            }
        },
        created(){
            this.getOrders()
        }
    })
</script>
