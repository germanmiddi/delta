<template>
    <app-layout title="Dashboard">

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Clientes - Editar Cliente
            </h2>

            <button class="btn-blue" @click="submit">
                Guardar
            </button>
        </template>

        <Toast :toast="this.toastMessage" :type="this.labelType" @clear="clearMessage"></Toast>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="mt-10 sm:mt-0">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <div class="px-4 sm:px-0">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Información Personal</h3>
                                <p class="mt-1 text-sm text-gray-600">Indique los datos del solicitante.</p>
                            </div>
                        </div>
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <form action="#" method="POST">
                                <div class="shadow overflow-hidden sm:rounded-md">
                                    <div class="px-4 py-5 bg-white sm:p-6">
                                        <div class="grid grid-cols-6 gap-6">

                                            <div class="col-span-6 sm:col-span-4">
                                                <label for="fullname"
                                                    class="block text-sm font-medium text-gray-700">Nombre y
                                                    Apellido</label>
                                                <input type="text" name="fullname" id="fullname" v-model="form.fullname"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </div>

                                            <div class="col-span-6 sm:col-span-2">
                                                <label for="client_type"
                                                    class="block text-sm font-medium text-gray-700">Tipo Cliente</label>
                                                <select id="client_type" name="client_type" v-model="form.client_type"
                                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option disabled value="">Seleccione un tipo de Cliente</option>
                                                    <option value="1" v-bind:select="1 == form.client_type">Particular
                                                    </option>
                                                    <option value="2" v-bind:select="2 == form.client_type">Empresa
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="company"
                                                    class="block text-sm font-medium text-gray-700">Empresa</label>
                                                <select id="company" name="company" v-model="form.company_id"
                                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option disabled value="">Seleccione una empresa</option>
                                                    <option v-for="empresa in this.empresas" :key="empresa.id"
                                                        :value="empresa.id" :bind:select="empresa.id == form.companyid">
                                                        {{ empresa.razon_social }}</option>
                                                </select>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="cellphone"
                                                    class="block text-sm font-medium text-gray-700">Celular</label>
                                                <input type="text" name="cellphone" id="cellphone"
                                                    v-model="form.cellphone"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="phone"
                                                    class="block text-sm font-medium text-gray-700">Teléfono</label>
                                                <input type="text" name="phone" id="phone" v-model="form.phone"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="email"
                                                    class="block text-sm font-medium text-gray-700">Email</label>
                                                <input type="text" name="email" id="email" v-model="form.email"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="dni"
                                                    class="block text-sm font-medium text-gray-700">DNI</label>
                                                <input type="text" name="dni" id="dni" v-model="form.dni"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="price"
                                                    class="block text-sm font-medium text-gray-700">Monto</label>
                                                <input type="text" name="price" id="price" v-model="form.price"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </div>

                                            <div class="col-span-6 sm:col-span-6">
                                                <label for="notes"
                                                    class="block text-sm font-medium text-gray-700">Notas</label>
                                                <textarea type="text" rows=5 name="notes" id="notes"
                                                    v-model="form.notes"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"> </textarea>
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
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Dirección del Servicio</h3>
                                <p class="mt-1 text-sm text-gray-600">Indique los datos del solicitante.</p>
                            </div>
                        </div>
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <form action="#" method="POST">
                                <div class="shadow overflow-hidden sm:rounded-md">
                                    <div class="px-4 py-5 bg-white sm:p-6">
                                        <div class="grid grid-cols-6 gap-6">

                                            <!-- GOOGLE MAPS -->    

                                            <div class="col-span-6 sm:col-span-6">
                                                <label for="google_autocomple"
                                                    class="block text-sm font-medium text-gray-700">Buscador Google
                                                    Maps</label>
                                                <vue-google-autocomplete ref="address" id="map"
                                                    classname="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                    placeholder="Ingrese la dirección"
                                                    v-on:placechanged="getAddressData">
                                                </vue-google-autocomplete>
                                                <br>
                                                <label for="google_address"
                                                    class="block text-sm font-medium text-gray-700"><b>{{
                                                            form.address.google_address
                                                    }}</b></label>
                                            </div>

                                            <div class="col-span-3">
                                                <label for="google_area1"
                                                    class="block text-sm font-medium text-gray-700">Ciudad</label>
                                                <input type="text" name="price" id="price"
                                                    v-model="form.address.google_area1"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                                    disabled />
                                            </div>

                                            <div class="col-span-3">
                                                <label for="google_postal_code"
                                                    class="block text-sm font-medium text-gray-700">Codigo
                                                    Postal</label>
                                                <input type="text" name="price" id="price"
                                                    v-model="form.address.google_postal_code"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-gray-50"
                                                    disabled />
                                            </div>

                                            <div class="col-span-6 sm:col-span-6">
                                                <GoogleMap v-if="this.showMap" :form_map="form_google">
                                                </GoogleMap>
                                            </div>
                                            <div class="col-span-6 sm:col-span-6">
                                                <label for="notesAdrc"
                                                    class="block text-sm font-medium text-gray-700">Notas</label>
                                                <textarea type="text" rows=5 name="notesAdrc" id="notesAdrc"
                                                    v-model="form.address.notes"
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
import { defineComponent } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Icons from '@/Layouts/Components/Icons.vue'
import Toast from '@/Layouts/Components/Toast.vue'
import GoogleMap from '../../../Layouts/Components/GoogleMap.vue'
import VueGoogleAutocomplete from "vue-google-autocomplete"

export default defineComponent({
    props: {
        empresas: Object,
        states: Object,
        cliente: Object,
        address_client: Object,
    },

    components: {
        AppLayout,
        Icons,
        Toast,
        GoogleMap,
        VueGoogleAutocomplete
    },

    data() {
        return {
            form: {},
            form_google: "",
            data: [],
            cities: "",
            loadCity: false,
            toastMessage: "",
            labelType: "info",
            first_update_city: true,
            address: "",
            showMap: false,
        }
    },

    methods: {
        clearMessage() {
            this.toastMessage = ""
        },
        /* async getCity() {
            if (!this.form.address.zipcode || this.form.address.zipcode.length != 4) return

            if (this.first_update_city) { //Controla que no limpie la primera busqueda
                this.first_update_city = false
            } else {
                this.loadCity = true
                this.form.address.city_id = ""
                this.form.address.state_id = ""
            }

            const filter = `cp=${this.form.address.zipcode}`
            const get = `${route('clients.getCityByCp')}?${filter}`
            const response = await fetch(get, { method: 'GET' })
            this.cities = await response.json()

            if (this.cities.length == 0) {
                this.labelType = "danger"
                this.toastMessage = 'Código Postal no encontrado'
            } else {
                this.form.address.state_id = this.cities[0].state_id
            }
            this.loadCity = false

        }, */
        submit() {
            this.$inertia.post(route('clients.update'), this.form)
        },
        getAddressData: function (addressData, placeResultData, id) {
            this.form.address.google_address = placeResultData['formatted_address']
            this.form.address.google_area1 = addressData['administrative_area_level_1']
            this.form.address.google_postal_code = addressData['postal_code']
            this.form.address.google_latitude = addressData['latitude']
            this.form.address.google_longitude = addressData['longitude']

            this.form_google = addressData

            this.showMap = true
        },
    },
    created() {
        this.form = this.cliente
        this.form.address = this.address_client[0]

        if(this.form.address.google_latitude && this.form.address.google_longitude && this.form.address.google_address){
            this.data['latitude'] = this.form.address.google_latitude
            this.data['longitude'] = this.form.address.google_longitude
            this.data['route'] = this.form.address.google_address
            this.form_google = this.data
            this.showMap = true
        }
    },
    mounted() {
        this.$refs.address.focus();
    },
})
</script>
