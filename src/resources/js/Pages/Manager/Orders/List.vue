<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pedidos
            </h2>
            <div>
                <a class="btn-blue mr-1" :href="route('orders.create')">
                    Nuevo Pedido
                </a>
                <a class="btn-blue" @click="showFilter = true">
                    Filtro
                </a>
            </div>
        </template>

        <div v-show="showFilter">
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <div class="mt-5 md:mt-0 md:col-span-2">

                        <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-md font-bold border-b">
                            <div>Filtro</div>
                        </div>
                        <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-md grid grid-cols-3">
                            <div class="col-span-1">
                                <h3 class="text-base font-medium text-gray-900">Fecha</h3>
                                <Datepicker class="mt-4 w-11/12" v-model="filter.date" range multiCalendars
                                    :closeOnAutoApply="true" :enableTimePicker="false"></Datepicker>

                                <h3 class="text-base font-medium text-gray-900 pt-6">Cliente</h3>
                                <select id="client" name="client" v-model="filter.client"
                                    class=" mt-4 w-11/12  block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option disabled value="">Seleccione un cliente</option>
                                    <option v-for="client in this.clients" :key="client.id" :value="client.id">
                                        {{ client.fullname }} - {{ client.street }} {{ client.strnum }} {{
                                                client.city_ltxt
                                        }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-span-1">
                                <h3 class="text-base font-medium text-gray-900">Dirección</h3>
                                <input type="text" v-model="filter.street" name="street" id="street"
                                    class="mt-4 w-11/12 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />

                                <h3 class="text-base font-medium text-gray-900 pt-6">Empresa</h3>
                                <select id="company" name="company" v-model="filter.company"
                                    class=" mt-4 w-11/12  block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Seleccione una empresa</option>
                                    <option v-for="empresa in this.empresas" :key="empresa.id" :value="empresa.id">
                                        {{ empresa.razon_social }}</option>
                                </select>
                            </div>

                            <div class="col-span-1">
                                <h3 class="text-base font-medium text-gray-900">Estado</h3>
                                <input type="text" v-model="filter.state" name="state" id="state"
                                    class="mt-4 w-11/12 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />

                                <h3 class="text-base font-medium text-gray-900 pt-6">Chofer</h3>
                                <select v-model="filter.driver" id="driver" name="driver"
                                    class="mt-4 w-11/12 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="" selected>Selecciones un Chofer</option>
                                    <option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{
                                            driver.fullname
                                    }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-md font-bold border-b">
                    <div>
                        <a class="text-base cursor-pointer text-indigo-400" @click.prevent="clearFilter">Limpiar
                            Filtro</a>
                        <a class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-500 hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 cursor-pointer"
                            @click.prevent="getOrders">Filtrar</a>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <table class="w-full whitespace-nowrap">
                        <tr class="text-left font-bold bg-blue-500 text-white">
                            <th class="px-6 py-4 text-center">ID</th>
                            <th class="px-6 py-4 text-center">Desde</th>
                            <th class="px-6 py-4 text-center">Hasta</th>
                            <th class="px-6 py-4 text-center">Cliente</th>
                            <th class="px-6 py-4 text-center">Domicilio</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                        <tr v-for="order in orders.data" :key="order.id"
                            class="hover:bg-gray-100 focus-within:bg-gray-100 text-sm ">
                            <td class="border-t px-6 py-4 text-center">
                                {{ order.id }}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                {{ order.f_inicio }} <br> {{ order.h_inicio }}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                {{ order.f_retiro }} <br> {{ order.h_retiro }}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                {{ order.client[0].fullname }}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                {{ order.client[0].address.street }} {{ order.client[0].address.strnum }} <br>
                                {{ order.client[0].address.zipcode }}

                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                {{ order.status }}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                <!-- <button type="button" class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <PlusSmIcon class="h-5 w-5" aria-hidden="true" />
                                </button> -->
                                <a type="button" :href="route('orders.edit', order.id)"
                                    class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-blue-300 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <PencilIcon class="h-5 w-5" aria-hidden="true" />
                                </a>
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
import { PencilIcon } from '@heroicons/vue/solid'
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

export default defineComponent({
    props: {
        drivers: Object,
        clients: Object,
        //states:  Object,
        //orden: Object,
        empresas: Object
    },
    components: {
        AppLayout,
        JetButton,
        PencilIcon,
        Datepicker
    },
    data() {
        return {
            orders: "",
            filter: {
                company: "",
                street: "",
                date: null,
                client: "",
                state: "",
                driver: ""
            },
            showFilter: true,
            length: 10
        }
    },
    methods: {
        async getOrders() {
            let filter = `&length=${this.length}`

            if (this.filter.date) {
                filter += `&date=${JSON.stringify(this.filter.date)}`
            }

            if (this.filter.street.length > 0) {
                filter += `&street=${JSON.stringify(this.filter.street)}`
            }

            if (this.filter.company) {
                filter += `&company=${JSON.stringify(this.filter.company)}`
            }

            if (this.filter.client) {
                filter += `&client=${this.filter.client}`
            }

            if (this.filter.state.length > 0) {
                filter += `&state=${this.filter.state}`
            }

            if (this.filter.driver) {
                filter += `&driver=${this.filter.driver}`
            }

            const get = `${route('orders.list')}?${filter}`

            const response = await fetch(get, { method: 'GET' })
            this.orders = await response.json()
        },
        clearFilter(){
                this.filter.company= "",
                this.filter.street= "",
                this.filter.date= null,
                this.filter.client= "",
                this.filter.state= "",
                this.filter.driver= ""
                this.getOrders()
            }
    },
    created() {
        this.getOrders()
    }
})
</script>
