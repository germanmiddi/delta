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
        <Toast :toast="this.toastMessage" :type="this.labelType" @clear="clearMessage"></Toast>

        <div>
            <div class="max-w-7xl mx-auto py-4 sm:px-6 lg:px-8">
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

                <div class="shadow sm:rounded-md sm:overflow-hidden mb-4">
                    <div class="bg-white py-4 px-4 ">
                        <div class="flex items-center justify-between flex-wrap sm:flex-nowrap ">
                            <div class="flex items-center">
                                <label class="text-sm font-medium text-gray-700 mr-2">Buscar: </label>
                                <input type="text" class="text-sm border-gray-300 rounded-md" v-model="search" @input="getClients">
                            </div>
                            <div class="flex-shrink-0">
                                <label class="text-sm font-medium text-gray-700 mr-2 ml-4" for="">Ver: </label>
                                <select class="text-sm border-gray-300 rounded-md" v-model="length"
                                    @change="getClients">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                    <table class="w-full whitespace-nowrap">
                        <thead>
                            <tr class="text-left font-bold bg-blue-500 text-white">
                                <th class="px-6 py-4 text-center">ID</th>
                                <th class="px-6 py-4 text-left">Nombre y Apellido</th>
                                <th class="px-6 py-4 text-center">Dirección</th>
                            <th class="px-6 py-4 text-center">Teléfono</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="client in clients.data" :key="client.id"
                                class="hover:bg-gray-100 focus-within:bg-gray-100 text-sm ">
                                <td class="border-t px-6 py-4 text-center">
                                {{ client.id }}
                            </td>
                            <td class="border-t px-6 py-4 text-left max-w-sm ">
                                <label v-if="client.fullname.length <= 50" class="">{{ client.fullname.substr(0,50) }}</label>
                                <div v-else class="relative group inline-block">
                                    <label class="hover:underline">{{ client.fullname.substr(0,50) }}</label>
                                    <div class="opacity-0 invisible group-hover:opacity-100 group-hover:visible absolute mt-2 bg-blue-100
                                     text-black p-1 rounded-sm z-10 shadow">
                                        <p>{{ client.fullname }}</p>
                                    </div>
                                </div>
                            </td>

                            <td v-if="client.address && client.address[0] && client.address[0].google_address" class="border-t px-6 py-4 text-center">
                                <!-- {{ client.address[0].google_address.substr(0,35) }}... -->
                                <label v-if="client.address[0].google_address.length <= 35" class="">{{ client.address[0].google_address.substr(0,35) }}</label>
                                <div v-else class="relative group inline-block">
                                    <label class="hover:underline">{{ client.address[0].google_address.substr(0,35) }}</label>
                                    <div class="-left-52 opacity-0 invisible group-hover:opacity-100 group-hover:visible absolute mt-2 bg-blue-100
                                     text-black p-1 rounded-sm z-10 shadow">
                                        <p>{{ client.address[0].google_address }}</p>
                                    </div>
                                </div>
                            </td>
                            <td v-else class="border-t px-6 py-4 text-center">
                                -
                            </td>

                            <td class="border-t px-6 py-4 text-center">
                                {{ client.cellphone }}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                <!-- <button type="button" class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <PlusSmIcon class="h-5 w-5" aria-hidden="true" />
                                </button> -->
                                <a type="button" :href="route('clients.edit', client.id)"
                                    class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-blue-300 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <PencilIcon class="h-5 w-5" aria-hidden="true" />
                                </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="flex justify-between mx-5 my-3 px-2 items-center text-sm">
                        <div>
                            Mostrando: {{ this.clients.from }} a {{ this.clients.to }} - Entradas encontradas:
                            {{ this.clients.total }}
                        </div>

                        <div class="flex flex-wrap -mb-1">
                            <template v-for="link in clients.links" :key="link.id">
                                <div v-if="link.url === null"
                                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded-md"
                                    v-html="link.label"> </div>
                                <div v-else
                                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border border-gray-300 rounded-md hover:bg-blue-500 hover:text-white cursor-pointer"
                                    :class="{ 'bg-blue-500': link.active }, { 'text-white': link.active }"
                                    @click="getClientsPaginate(link.url)" v-html="link.label"> </div>
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
import { PencilIcon, PlusSmIcon, XIcon, CheckCircleIcon } from '@heroicons/vue/solid';
import Toast from '@/Layouts/Components/Toast.vue';



export default defineComponent({
    props: {
        toast: Object
    },
    components: {
        AppLayout,
        JetButton,
        PencilIcon,
        PlusSmIcon,
        Toast,
        XIcon,
        CheckCircleIcon
    },
    data() {
        return {
            clients: "",
            toastMessage: "",
            labelType: "info",
            message: "",
            showToast: false,
            length: 20,
            search: ""
        }
    },
    methods: {
        async getClients() {

            const get = `${route('clients.list')}`
            const params = new URLSearchParams({
                length: this.length,
                search: this.search
            })
            const response = await fetch(get + '?' + params.toString(), { method: 'GET' })
            this.clients = await response.json()

        },
        async getClientsPaginate(link) {

            var get = `${link}`;
            const response = await fetch(get, { method: 'GET' })

            this.clients = await response.json()
        },

        clearMessage() {
            this.toastMessage = ""
        },
    },
    created() {
        this.getClients()
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
