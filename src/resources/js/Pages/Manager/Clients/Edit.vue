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
                                                    class="block text-sm font-medium text-gray-700">Nombre y Apellido</label>
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
                                                    class="block text-sm font-medium text-gray-700">Whatsapp / Celular</label>
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
                                                <input type="text" name="price" id="price" v-model="form.price" @keypress="this.form.modify_price = true"
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
                                                <input type="text" name="google_area1" id="google_area1"
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

    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-10 ">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 hidden bg-gray-500 bg-opacity-75 transition-opacity md:block "/>
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto ">
                <div class="flex items-stretch justify-center text-center md:items-center md:px-2 lg:px-4 ">
                    <TransitionChild as="template" enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 md:translate-y-0 md:scale-95"
                        enter-to="opacity-100 translate-y-0 md:scale-100" leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 md:scale-100"
                        leave-to="opacity-0 translate-y-4 md:translate-y-0 md:scale-95">
                        <DialogPanel
                            class="flex w-full transform text-left text-base transition md:my-8 md:max-w-2xl md:px-4 lg:max-w-2xl ">
                            <div
                                class="relative flex w-full items-center overflow-hidden bg-white px-4 pt-14 pb-8 shadow-2xl sm:px-6 sm:pt-8 md:p-6 lg:p-8 rounded-md">
                                <div
                                    class="grid w-full grid-cols-1 items-start gap-y-8 gap-x-2 sm:grid-cols-8 lg:gap-2">
                                    <div class="sm:col-span-8 lg:col-span-8 mx-auto text-center">
                                        <Icons name="exclamation" class="ml-2 w-20 h-20 text-red-500" />
                                    </div>
                                    <div class="sm:col-span-8 lg:col-span-8 mx-auto">
                                        <h1 class="text-xl font-bold text-gray-900">¿Desea actualizar los pedidos abiertos con el nuevo Precio?</h1>
                                    </div>
                                    <div class="sm:col-span-8 lg:col-span-8 mx-auto">
                                        <section aria-labelledby="options-heading" class="mt-10">
                                            <div class="grid grid-cols-4 gap-2">

                                            </div>
                                            <div class="grid grid-cols-2 gap-2 ">
                                                <button @click="open = false,form.update_price = false, updateOrder()"
                                                    class="px-6 py-2 mt-4 text-blue-800 border border-blue-600 rounded">
                                                    Cancelar
                                                </button>
                                                <button class="px-6 py-2 mt-4 text-blue-100 bg-blue-600 rounded"
                                                    @click="open = false, form.update_price = true, updateOrder()">
                                                    Si, Actualizar
                                                </button>
                                            </div>
                                        </section>
                                    </div>

                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>


<script>
import { ref, defineComponent } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Icons from '@/Layouts/Components/Icons.vue'
import Toast from '@/Layouts/Components/Toast.vue'
import GoogleMap from '../../../Layouts/Components/GoogleMap.vue'
import VueGoogleAutocomplete from "vue-google-autocomplete"
import {
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue'

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
        VueGoogleAutocomplete,
        Dialog,
        DialogPanel,
        TransitionChild,
        TransitionRoot,
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
    setup() {

        const open = ref(false)

        return {
            open
        }
    },

    methods: {
        clearMessage() {
            this.toastMessage = ""
        },
        submit() {
            if(this.form.modify_price){
                this.open = true
            }else{
                this.updateOrder()
            }
        },
        updateOrder(){
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
        }
    },
    created() {
        this.form = this.cliente
        this.form.update_price = false;
        this.form.modify_price = false;
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
    }
})
</script>
