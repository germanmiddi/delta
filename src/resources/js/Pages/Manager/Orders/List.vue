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
                <a class="btn-blue" @click="showFilter = !showFilter">
                    Filtro
                </a>
            </div>
        </template>
        <Toast :toast="this.toastMessage" :type="this.labelType" @clear="clearMessage"></Toast>

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
                                        {{ client.fullname }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-span-1">
                                <h3 class="text-base font-medium text-gray-900">Dirección</h3>
                                <input type="text" v-model="filter.street" name="street" id="street"
                                    class="mt-4 w-11/12 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />

                                <h3 class="text-base font-medium text-gray-900 pt-6">Empresa</h3>
                                <select id="company" name="company" v-model="filter.company"
                                    class=" mt-4 w-11/12  block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm ">
                                    <option value="">Seleccione una empresa</option>
                                    <option v-for="empresa in this.empresas" :key="empresa.id" :value="empresa.id">
                                        {{ empresa.razon_social }}</option>
                                </select>
                            </div>

                            <div class="col-span-1">
                                <h3 class="text-base font-medium text-gray-900">Estado</h3>
                                <select v-model="filter.status" id="status" name="status"
                                    class="mt-4 w-11/12 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="" selected>Selecciones un Estado</option>
                                    <option v-for="s in status" :key="s.order_status" :value="s.order_status">{{
                                            s.order_status
                                    }}</option>
                                </select>

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
                        <a class="float-right inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-500 hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 cursor-pointer"
                            @click.prevent="getOrders">Filtrar</a>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div v-if="loading">
                <Icons name="loading" class="w-8 mx-auto" />
            </div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                 <div v-if="showToast" class="rounded-md bg-green-50 p-4 mb-5  ">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <CheckCircleIcon class="h-5 w-5 text-green-400" aria-hidden="true" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ this.message }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button type="button" @click="showToast = false"
                                    class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                                    <span class="sr-only">Dismiss</span>
                                    <XIcon class="h-5 w-5" aria-hidden="true" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
                    <hr>
                    <div class="flex justify-between mx-5 my-3 px-2 items-center">
                        <div>
                            Mostrando: {{ this.orders.from }} a {{ this.orders.to }} - Entradas encontradas:
                            {{ this.orders.total }}
                        </div>

                        <div class="flex flex-wrap -mb-1">
                            <template v-for="link in orders.links">
                                <div v-if="link.url === null"
                                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded-md"
                                    v-html="link.label"> </div>
                                <div v-else
                                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border border-gray-300 rounded-md hover:bg-blue-500 hover:text-white cursor-pointer"
                                    :class="{ 'bg-blue-500': link.active }, { 'text-white': link.active }"
                                    @click="getOrdersPaginate(link.url)" v-html="link.label"> </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
<script>

import { defineComponent } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue';
import JetButton from '@/Jetstream/Button.vue';
import { PencilIcon, XIcon, CheckCircleIcon } from '@heroicons/vue/solid';
import Icons from '@/Layouts/Components/Icons.vue';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import Toast from '@/Layouts/Components/Toast.vue';

export default defineComponent({
    props: {
        drivers: Object,
        clients: Object,
        empresas: Object,
        status: Object,
        toast: Object
    },
    components: {
        AppLayout,
        JetButton,
        PencilIcon,
        Icons,
        Datepicker,
        Toast,
        XIcon,
        CheckCircleIcon
    },
    data() {
        return {
            orders: "",
            filter: {
                company: "",
                street: "",
                date: null,
                client: "",
                status: "",
                driver: ""
            },
            showFilter: false,
            loading: false,
            length: 15,
            toastMessage: "",
            labelType: "info",
            message: "",
            showToast: false,
        }
    },
    methods: {
        async getOrders() {
            this.loading = true
            this.orders = ""
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

            if (this.filter.status) {
                filter += `&status=${this.filter.status}`
            }

            if (this.filter.driver) {
                filter += `&driver=${this.filter.driver}`
            }

            const get = `${route('orders.list')}?${filter}`

            const response = await fetch(get, { method: 'GET' })
            this.orders = await response.json()
            this.loading = false
        },
        clearMessage() {
            this.toastMessage = ""
        },
        clearFilter() {
            this.filter.company = "",
                this.filter.street = "",
                this.filter.date = null,
                this.filter.client = "",
                this.filter.status = "",
                this.filter.driver = ""
            this.getOrders()
        },
        async getOrdersPaginate(link) {

            var get = `${link}`;
            const response = await fetch(get, { method: 'GET' })

            this.orders = await response.json()
            console.log(this.orders)

        },
    },
    created() {
        this.getOrders()
    },
    mounted() {
        if (this.toast) {
            if (this.toast['status'] == 200) {
                this.message = this.toast['message']
                this.showToast = true
            } else {
                this.labelType = "danger"
                this.toastMessage = this.toast['message']
            }
        }
    }
})
</script>
