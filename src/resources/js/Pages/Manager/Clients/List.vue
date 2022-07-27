<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Clientes
            </h2>
             <a class="btn-blue" :href="route('clients.create')">
                    Nuevo Cliente
            </a>  
        </template>
        
        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <table class="w-full whitespace-nowrap"> 
                        <tr class="text-left font-bold bg-blue-500 text-white">
                            <th class="px-6 py-4 text-center">ID</th>
                            <th class="px-6 py-4 text-center">Nombre y Apellido</th>
                            <th class="px-6 py-4 text-center">Dirección</th>
                            <th class="px-6 py-4 text-center">Teléfono</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                        <tr v-for="client in clients.data" :key="client.id" class="hover:bg-gray-100 focus-within:bg-gray-100 text-sm ">
                            <td class="border-t px-6 py-4 text-center">
                                {{client.id}}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                {{client.fullname}}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                {{client.cellphone}}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                 <!-- <button type="button" class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <PlusSmIcon class="h-5 w-5" aria-hidden="true" />
                                </button> -->
                                 <button type="button" class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-blue-300 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <PencilIcon class="h-5 w-5" aria-hidden="true" />
                                </button>
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
    import { PencilIcon, PlusSmIcon } from '@heroicons/vue/solid'


    export default defineComponent({
        components: {
            AppLayout,      
            JetButton,
            PencilIcon,
            PlusSmIcon
        },
        data(){
            return{
                clients: ""
            }
        },
        methods:{
            async getClients(){

                const get = `${route('clients.list')}` 

                const response = await fetch(get, {method:'GET'})
                this.clients = await response.json() 

            }
        },
        created(){
            this.getClients()
        }
    })
</script>
