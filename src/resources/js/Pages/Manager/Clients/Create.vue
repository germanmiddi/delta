<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Clientes - Crear Cliente
            </h2>

            <button class="btn-blue">
                Guardar
            </button>              
        </template>

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
                                    <label for="fullname" class="block text-sm font-medium text-gray-700">Nombre y Apellido</label>
                                    <input type="text" name="fullname" id="fullname" v-model="form.fullname" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-2">
                                    <label for="client_type" class="block text-sm font-medium text-gray-700">Tipo Cliente</label>
                                    <input type="text" name="client_type" id="client_type" v-model="form.client_type" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="company" class="block text-sm font-medium text-gray-700">Empresa</label>
                                    <select id="company" name="company" v-model="form.company_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option>United States</option>
                                    <option>Canada</option>
                                    <option>Mexico</option>
                                    </select>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="cellphone" class="block text-sm font-medium text-gray-700">Celular</label>
                                    <input type="text" name="cellphone" id="cellphone" v-model="form.cellphone" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                    <input type="text" name="phone" id="phone" v-model="form.phone" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="text" name="email" id="email" v-model="form.email" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="dni" class="block text-sm font-medium text-gray-700">DNI</label>
                                    <input type="text" name="dni" id="dni" v-model="form.dni" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-6">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notas</label>
                                    <textarea type="text" rows=5 name="notes" id="notes" v-model="form.notes" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"> </textarea>
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

                                <div class="col-span-4">
                                    <label for="street" class="block text-sm font-medium text-gray-700">Dirección</label>
                                    <input type="text" v-model="form.street" name="street" id="street" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-1">
                                    <label for="strnum" class="block text-sm font-medium text-gray-700">Número</label>
                                    <input type="text" v-model="form.strnum" name="strnum" id="strnum" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-1">
                                    <label for="floor" class="block text-sm font-medium text-gray-700">Piso/Dpto</label>
                                    <input type="text" v-model="form.floor" name="floor" id="floor" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>


                                <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                    <label for="zipcode" class="block text-sm font-medium text-gray-700">Código Postal</label>
                                    <input type="text" name="zipcode" id="zipcode" v-model="form.zipcode" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-6 lg:col-span-2">
                                    <label for="city" class="block text-sm font-medium text-gray-700">Ciudad</label>
                                    <input type="text" name="city" id="city" v-model="form.city_id" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                    <label for="region" class="block text-sm font-medium text-gray-700">Provincia</label>
                                    <input type="text" name="region" id="region" v-model="form.state_id" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-6">
                                    <label for="notesAdrc" class="block text-sm font-medium text-gray-700">Notas</label>
                                    <textarea type="text" rows=5 name="notesAdrc" id="notesAdrc" v-model="form.notesAdrc" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"> </textarea>
                                </div>

                                </div>
                            </div>

                            </div>
                        </form>
                        </div>
                    </div>
                </div>

                <!-- <div class="md:grid md:grid-cols-3 md:gap-6">


                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                             <h3 class="text-lg font-medium leading-6 text-gray-900">
                                Datos del Cliente
                            </h3>

                            <p class="mt-1 text-sm text-gray-600">
                                Complete la información del cliente.
                            </p>
                        </div>
                    </div>        

                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-md">
                            <div class="grid grid-cols-6 gap-6">

                                <div class="col-span-6 sm:col-span-3">
                                    <jet-label for="fecha_salida" value="Fecha salida" />
                                    <Datepicker class="w-full" v-model="form.fecha_salida" 
                                                :enableTimePicker="false"
                                                :monthChangeOnScroll="false" 
                                                autoApply
                                                :minDate="new Date(new Date().setDate(new Date().getDate()-1))"></Datepicker>
                                </div>  

                                <div class="col-span-6 sm:col-span-3">
                                    <jet-label for="fecha_llegada" value="Fecha llegada" />
                                    <Datepicker class="w-full" v-model="form.fecha_llegada" 
                                                :enableTimePicker="false"
                                                :monthChangeOnScroll="false" 
                                                autoApply
                                                :minDate="form.fecha_salida">
                                    </Datepicker>
                                </div>  

                                <div class="col-span-6 sm:col-span-3">
                                    <jet-label for="pais_origen" value="Pais origen" />
                                    <select v-model="form.pais_origen" id="pais_origen" name="pais_origen" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option disabled value="">Seleccione el Pais</option>
                                        <option v-for="country in countries_list" :key="country.id" :value="country.iso_country">{{country.description}}</option>
                                    </select>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <jet-label for="pais_destino" value="Pais destino" />
                                    <select v-model="form.pais_destino" id="pais_destino" name="pais_destino" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option disabled value="">Seleccione el Pais</option>
                                        <option v-for="country in countries_list" :key="country.id" :value="country.iso_country">{{country.description}}</option>
                                    </select>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <jet-label for="id_plan" value="Plan" />
                                    <select v-model="form.id_plan" id="id_plan" name="id_plan" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option disabled value="">Seleccione el Plan</option>
                                        <option v-for="plan in plan_list" :key="plan.id" :value="plan.id">{{plan.titulo}}</option>
                                    </select>
                                </div>

                                <div class="col-span-6 sm:col-span-1">
                                    <jet-label for="costo" value="Total" />
                                    <jet-input v-model="form.costo" id="costo" type="text" class="mt-1 block w-full"  />
                                </div>

                                <div class="col-span-6 sm:col-span-1">
                                    <jet-label for="moneda" value="moneda" />
                                    <jet-input v-model="form.moneda" id="moneda" type="text" class="mt-1 block w-full" />
                                </div>

                                <div class="col-span-6 sm:col-span-1">
                                    <jet-label for="tasa_cambio" value="tasa_cambio" />
                                    <jet-input v-model="form.tasa_cambio" id="tasa_cambio" type="text" class="mt-1 block w-full"  />
                                </div>


                            </div>

                        </div>

                    </div>

                    

                </div> -->
            </div>
        </div>            

    </app-layout>
</template>


<script>
    import { defineComponent } from 'vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetActionMessage from '@/Jetstream/ActionMessage.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    // import Datepicker from 'vue3-date-time-picker'
    // import 'vue3-date-time-picker/dist/main.css'
    import { ref } from 'vue'
    import Icons from '@/Layouts/Components/Icons.vue'    


    export default defineComponent({
        props:{

        },

        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
            AppLayout,
            // Datepicker,
            Icons
        },

        data() {
            return {
                form: {},
            }
        },

        methods:{
            submit(){

            },

        },
    })
</script>
