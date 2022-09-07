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
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="mt-10 sm:mt-0">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
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
                            <form action="#" method="POST">
                                <div class="shadow overflow-hidden sm:rounded-md">
                                    <div class="px-4 py-5 bg-white sm:p-6">
                                        <div class="grid grid-cols-6 gap-6">

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="date" class="block text-sm font-medium text-gray-700">Fecha
                                                    Inicio:</label>

                                                <Datepicker id="date" class="w-full mt-1" v-model="form.date"
                                                    :enableTimePicker="false" :monthChangeOnScroll="false"
                                                    :minDate="new Date(new Date().setDate(new Date().getDate() - 1))"
                                                    autoApply :format="format"></Datepicker>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="time" class="block text-sm font-medium text-gray-700">Hora
                                                    Inicio:</label>

                                                <Datepicker id="time" class="w-full mt-1" v-model="form.time"
                                                    :startTime="startTime" timePicker>
                                                </Datepicker>
                                            </div>

                                            <div class="col-span-6 py-2">
                                                <div class="border-t border-gray-200"></div>
                                            </div>

                                            <div class="col-span-6">
                                                <label for="driver"
                                                    class="block text-sm font-medium text-gray-700">Seleccione un
                                                    chofer:</label>
                                                <select v-model="form.driver" id="driver" name="driver"
                                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option disabled value="">Selecciones un Chofer</option>
                                                    <option v-for="driver in drivers" :key="driver.id"
                                                        :value="driver.id">
                                                        {{ driver.fullname }}</option>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:block" aria-hidden="true">
                    <div class="py-5">
                        <div class="border-t border-gray-200"></div>
                    </div>
                </div>

                <div class="mt-10 sm:mt-0">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
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
                            <form action="#" method="POST">
                                <div class="shadow overflow-hidden sm:rounded-md">
                                    <div class="px-4 py-5 bg-white sm:p-6">
                                        <div class="grid grid-cols-6 gap-6">
                                            <div class="col-span-4">
                                                <label for="client"
                                                    class="block text-sm font-medium text-gray-700">Seleccione un
                                                    cliente existente:</label>
                                                <select id="client" name="client" v-model="form.client_id"
                                                    :disabled="newClient ? '' : disabled"
                                                    :class="newClient ? 'bg-gray-50' : ''"
                                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                    @change="setClient(form.client_id)">
                                                    <option disabled value="">Seleccione un cliente</option>
                                                    <option v-for="client in this.clients" :key="client.id"
                                                        :value="client.id">
                                                        {{ client.fullname }} - {{ client.google_address }}</option>
                                                </select>
                                            </div>
                                            <div class="col-span-2">
                                                <button type="button"
                                                    class="px-6 py-2 w-full mt-6 text-blue-100 bg-blue-600 rounded"
                                                    @click="this.newClient = !this.newClient">
                                                    {{ this.btnTextNewClient }}
                                                </button>
                                            </div>

                                            <div class="col-span-6 py-3">
                                                <div class="border-t border-gray-200"></div>
                                            </div>

                                            <div class="col-span-6 sm:col-span-4">
                                                <label for="fullname"
                                                    class="block text-sm font-medium text-gray-700">Nombre y
                                                    Apellido</label>
                                                <input type="text" name="fullname" id="fullname" v-model="form.fullname"
                                                    :disabled="!newClient ? '' : disabled"
                                                    :class="!newClient ? 'bg-gray-50' : ''"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                            </div>

                                            <div class="col-span-6 sm:col-span-2">
                                                <label for="client_type"
                                                    class="block text-sm font-medium text-gray-700">Tipo
                                                    Cliente</label>
                                                <select id="client_type" name="client_type" v-model="form.client_type" :disabled="!newClient ? '' : disabled"
                                                    :class="!newClient ? 'bg-gray-50' : ''"
                                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="">Seleccione un tipo de Cliente</option>
                                                    <option value="1" :bind:select="form.client_type == 1">Particular</option>
                                                    <option value="2" :bind:select="form.client_type == 2">Empresa</option>
                                                </select>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="company"
                                                    class="block text-sm font-medium text-gray-700">Empresa</label>
                                                <select id="company" name="company" v-model="form.company_id"
                                                    :disabled="!newClient ? '' : disabled"
                                                    :class="!newClient ? 'bg-gray-50' : ''"
                                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option disabled value="">Seleccione una empresa</option>
                                                    <option v-for="empresa in this.empresas" :key="empresa.id"
                                                        :value="empresa.id"
                                                        :bind:select="empresa.id == form.company_id">{{
                                                                empresa.razon_social
                                                        }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="cellphone"
                                                    class="block text-sm font-medium text-gray-700">Celular</label>
                                                <input type="text" name="cellphone" id="cellphone"
                                                    v-model="form.cellphone" :disabled="!newClient ? '' : disabled"
                                                    :class="!newClient ? 'bg-gray-50' : ''"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="phone"
                                                    class="block text-sm font-medium text-gray-700">Teléfono</label>
                                                <input type="text" name="phone" id="phone" v-model="form.phone"
                                                    :disabled="!newClient ? '' : disabled"
                                                    :class="!newClient ? 'bg-gray-50' : ''"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="email"
                                                    class="block text-sm font-medium text-gray-700">Email</label>
                                                <input type="text" name="email" id="email" v-model="form.email"
                                                    :disabled="!newClient ? '' : disabled"
                                                    :class="!newClient ? 'bg-gray-50' : ''"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="dni"
                                                    class="block text-sm font-medium text-gray-700">DNI</label>
                                                <input type="text" name="dni" id="dni" v-model="form.dni"
                                                    :disabled="!newClient ? '' : disabled"
                                                    :class="!newClient ? 'bg-gray-50' : ''"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="price"
                                                    class="block text-sm font-medium text-gray-700">Monto</label>
                                                <input type="text" name="price" id="price" v-model="form.price"
                                                    :disabled="!newClient ? '' : disabled"
                                                    :class="!newClient ? 'bg-gray-50' : ''"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                            </div>

                                            <div class="col-span-6 py-3">
                                                <div class="border-t border-gray-200"></div>
                                            </div>

                                            <div class="col-span-6">
                                                <label for="google_area1"
                                                    class="block text-sm font-medium text-gray-700">Dirección</label>
                                                <vue-google-autocomplete ref="address" id="map" v-if="newClient"
                                                    classname="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                    placeholder="Ingrese la dirección"
                                                    v-on:placechanged="getAddressData">
                                                </vue-google-autocomplete>
                                                <input type="text" name="price" id="price" v-model="form.google_address" v-else
                                                    :disabled="!newClient ? '' : disabled"
                                                    :class="!newClient ? 'bg-gray-50' : ''"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="dni"
                                                    class="block text-sm font-medium text-gray-700">Ciudad</label>
                                                <input type="text" name="dni" id="dni" v-model="form.google_area1"
                                                    :disabled="!newClient ? '' : disabled"
                                                    :class="!newClient ? 'bg-gray-50' : ''"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="price"
                                                    class="block text-sm font-medium text-gray-700">Codigo
                                                    Postal</label>
                                                <input type="text" name="price" id="price" v-model="form.google_postal_code"
                                                    :disabled="!newClient ? '' : disabled"
                                                    :class="!newClient ? 'bg-gray-50' : ''"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                            </div>

                                            <div class="col-span-6 sm:col-span-6">
                                                <GoogleMap v-if="this.showMap" :form_map="form_google">
                                                </GoogleMap>
                                            </div>

                                            <div class="col-span-6 sm:col-span-6">
                                                <label for="notesAdrc"
                                                    class="block text-sm font-medium text-gray-700">Notas</label>
                                                <textarea type="text" rows=5 name="notesAdrc" id="notesAdrc"
                                                    v-model="form.address_note" :disabled="!newClient ? '' : disabled"
                                                    :class="!newClient ? 'bg-gray-50' : ''"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"> </textarea>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </form>
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
import GoogleMap from '../../../Layouts/Components/GoogleMap.vue'
import VueGoogleAutocomplete from "vue-google-autocomplete"


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
        GoogleMap,
        VueGoogleAutocomplete,
    },

    data() {
        return {
            form: {},
            // drivers:"",
            client: "",
            showMap: false,
            form_google: "",
            data: [],
            newClient: false,
            btnTextNewClient: 'Nuevo Cliente',
            address: "",

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

            //console.log(client)
            this.form.fullname = client.fullname
            this.form.client_type = client.client_type
            this.form.company_id = client.company_id
            this.form.cellphone = client.cellphone
            this.form.phone = client.phone
            this.form.email = client.email
            this.form.dni = client.dni
            this.form.price = client.price
            this.form.google_address = client.google_address
            this.form.google_area1 = client.google_area1
            this.form.google_postal_code = client.google_postal_code
            this.form.google_latitude = client.google_latitude
            this.form.google_longitude = client.google_longitude
            this.form.address_note = client.notes
            if (client.google_latitude && client.google_longitude && client.google_address) {
                this.data['latitude'] = client.google_latitude
                this.data['longitude'] = client.google_longitude
                this.data['route'] = client.google_address
                this.form_google = this.data
                this.showMap = true
            } else {
                this.showMap = false
            }

        },
        getAddressData: function (addressData, placeResultData, id) {
            console.log(addressData)
            this.form.google_address = placeResultData['formatted_address']
            this.form.google_area1 = addressData['administrative_area_level_1']
            this.form.google_postal_code = addressData['postal_code']
            this.form.google_latitude = addressData['latitude']
            this.form.google_longitude = addressData['longitude']

            this.form_google = addressData

            this.showMap = true
        },
        cleanNewUser(){
			this.form.fullname = ''
            this.form.client_type = ''
            this.form.company_id =  ''
            this.form.cellphone =  ''
            this.form.phone =  ''
            this.form.email =  ''
            this.form.dni =  ''
            this.form.price =  ''
            this.form.google_address =  ''
            this.form.google_area1 =  ''
            this.form.google_postal_code =  ''
            this.form.google_latitude =  ''
            this.form.google_longitude =  ''
            this.form.address_note =  ''
            this.showMap = false
		},

    },
    watch: {
        newClient: function (value) {
            if (value) {
                this.form.newClient = true
                this.form.client_id = null
                this.btnTextNewClient = 'Cancelar N. Cliente'
            } else {
                this.form.newClient = false
                this.form.client_id = null
                this.btnTextNewClient = 'Nuevo Cliente'
            }
            this.cleanNewUser()
        }
    },
    created() {
        this.form.newClient = false
    }
})
</script>
