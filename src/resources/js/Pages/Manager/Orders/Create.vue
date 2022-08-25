<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pedidos - <span class="font-normal text-gray-600">Crear Pedido</span>
            </h2>

            <button class="btn-blue" @click="submit">
                Guardar
            </button>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                Datos del Servicio
                            </h3>

                            <p class="mt-1 text-sm text-gray-600">
                                Indique los detalles del pedido.
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-md">

                            <div class="text-base font-semibold  text-gray-900 mb-4" aria-hidden="true">Fecha del
                                servicio</div>

                            <div class="grid grid-cols-6 gap-6">

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha
                                        Inicio:</label>

                                    <Datepicker id="fecha_inicio" class="w-full mt-1" v-model="form.fecha_inicio"
                                        :enableTimePicker="false" :monthChangeOnScroll="false"
                                        :minDate="new Date(new Date().setDate(new Date().getDate() - 1))" autoApply
                                        :format="format"></Datepicker>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Hora
                                        Inicio:</label>

                                    <Datepicker id="fecha_inicio" class="w-full mt-1" v-model="form.hora_inicio"
                                        :startTime="startTime" timePicker></Datepicker>
                                </div>

                            </div>

                            <div class="py-5">
                                <div class="border-t border-gray-300"></div>
                            </div>

                            <div class="text-base font-semibold text-gray-900 mb-4" aria-hidden="true">Chofer</div>

                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-4">
                                    <label for="driver" class="block text-sm font-medium text-gray-700">Seleccione un
                                        chofer:</label>
                                    <select v-model="form.driver" id="driver" name="driver"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option disabled value="">Selecciones un Chofer</option>
                                        <option v-for="driver in drivers" :key="driver.id" :value="driver.id">
                                            {{ driver.fullname }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="max-w-7xl mx-auto py-5 sm:px-6 lg:px-6">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                Datos del Cliente
                            </h3>

                            <p class="mt-1 text-sm text-gray-600">
                                Indique los datos del cliente.
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-md">

                            <div class="text-base font-semibold text-gray-900 mb-4" aria-hidden="true">Cliente</div>

                            <div class="grid grid-cols-6 gap-4">

                                <div class="col-span-4">
                                    <label for="client" class="block text-sm font-medium text-gray-700">Seleccione un
                                        cliente existente:</label>
                                    <select id="client" name="client" v-model="form.client_id"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        @change="setClient(form.client_id)">
                                        <option disabled value="">Seleccione un cliente</option>
                                        <option v-for="client in this.clients" :key="client.id" :value="client.id">
                                            {{ client.fullname }} - {{ client.street }} {{ client.strnum }}
                                            {{ client.city_ltxt }}</option>
                                    </select>
                                </div>

                                <!-- <div class="col-span-2 flex justify-around items-baseline space-x-1  mt-6">
                                    <button type="button" class="h-8 inline-flex items-center px-2.5 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Seleccionar</button>                                    
                                    <button type="button" class="h-8 inline-flex items-center px-2.5 py-1.5 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Crear</button>
                                </div> -->

                                <div class="col-span-4">
                                    <label for="street"
                                        class="block text-sm font-medium text-gray-700">Dirección</label>
                                    <input type="text" v-model="form.street" name="street" id="street"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                        disabled />
                                </div>

                                <div class="col-span-1">
                                    <label for="strnum" class="block text-sm font-medium text-gray-700">Número</label>
                                    <input type="text" v-model="form.strnum" name="strnum" id="strnum"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                        disabled />
                                </div>

                                <div class="col-span-1">
                                    <label for="floor" class="block text-sm font-medium text-gray-700">Piso/Dpto</label>
                                    <input type="text" v-model="form.floor" name="floor" id="floor"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                        disabled />
                                </div>

                                <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                    <label for="zipcode" class="block text-sm font-medium text-gray-700">Código
                                        Postal</label>
                                    <input type="text" name="zipcode" id="zipcode" v-model="form.zipcode"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                        disabled />
                                </div>

                                <div class="col-span-6 sm:col-span-6 lg:col-span-2">
                                    <label for="city" class="block text-sm font-medium text-gray-700">Ciudad</label>
                                    <input type="text" name="city" id="city" v-model="form.city"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                        disabled />
                                </div>

                                <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                    <label for="region"
                                        class="block text-sm font-medium text-gray-700">Provincia</label>
                                    <input type="text" name="region" id="region" v-model="form.state"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                        disabled />
                                </div>


                                <div class="col-span-6 py-3">
                                    <div class="border-t border-gray-200"></div>
                                </div>

                                <div class="col-span-6 sm:col-span-4">
                                    <label for="fullname" class="block text-sm font-medium text-gray-700">Nombre y
                                        Apellido</label>
                                    <input type="text" name="fullname" id="fullname" v-model="form.fullname"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                        disabled />
                                </div>

                                <div class="col-span-6 sm:col-span-2">
                                    <label for="client_type" class="block text-sm font-medium text-gray-700">Tipo
                                        Cliente</label>
                                    <input type="text" name="client_type" id="client_type" v-model="form.client_type"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                        disabled />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="company" class="block text-sm font-medium text-gray-700">Empresa</label>
                                    <select id="company" name="company" v-model="form.company_id"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50"
                                        disabled>
                                        <option disabled value="">Seleccione una empresa</option>
                                        <option v-for="empresa in this.empresas" :key="empresa.id" :value="empresa.id"
                                            :bind:select="empresa.id == form.company_id">{{ empresa.razon_social }}
                                        </option>
                                    </select>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="cellphone"
                                        class="block text-sm font-medium text-gray-700">Celular</label>
                                    <input type="text" name="cellphone" id="cellphone" v-model="form.cellphone"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                        disabled />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                    <input type="text" name="phone" id="phone" v-model="form.phone"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                        disabled />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="text" name="email" id="email" v-model="form.email"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                        disabled />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="dni" class="block text-sm font-medium text-gray-700">DNI</label>
                                    <input type="text" name="dni" id="dni" v-model="form.dni"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                        disabled />
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="price" class="block text-sm font-medium text-gray-700">Monto</label>
                                    <input type="text" name="price" id="price" v-model="form.price"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                        disabled />
                                </div>

                            </div>
                            
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </app-layout>
</template>

<script>

import { defineComponent, ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import Icons from '@/Layouts/Components/Icons.vue'
import Checkbox from './Checkbox.vue'


export default defineComponent({
    props: {
        drivers: Object,
        clients: Object,
        states: Object,
        empresas: Object,
    },

    components: {
        AppLayout,
        Datepicker,
        Icons,
        Checkbox,
    },

    data() {
        return {
            form: {},
            // drivers:"",
            client: "",
        }
    },

    setup() {

        const format = (date) => {
            const day = date.getDate();
            const month = date.getMonth() + 1;
            const year = date.getFullYear();

            return `${day}/${month}/${year}`;
        }

        const startTime = ref({ hours: 9, minutes: 0 });

        return {
            format,
            startTime
        }
    },

    methods: {

        async submit() {
            this.$inertia.post(route('orders.store'), this.form)
        },

        async getDrivers() {
            const get = `${route('drivers.list')}`
            const response = await fetch(get, { method: 'GET' })
            let list = await response.json()
            this.drivers = list.data.map((d) => { return { id: d.id, name: d.fullname } })
        },

        setClient(id) {

            let client = this.clients.find((c) => { return c.id == id })

            console.log(client)
            this.form.fullname = client.fullname
            this.form.client_type = client.client_type
            this.form.company_id = client.company_id
            this.form.cellphone = client.cellphone
            this.form.phone = client.phone
            this.form.email = client.email
            this.form.dni = client.dni
            this.form.price = client.price
            this.form.street = client.street
            this.form.strnum = client.strnum
            this.form.zipcode = client.zipcode
            this.form.city = client.city_ltxt
            this.form.state = client.state_ltxt

        }

    },
    created() {
        // this.getDrivers()
    }
})
</script>
