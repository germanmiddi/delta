<template>
    <AppLayout title="Dashboard">
        <!-- <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
            <div>
                <a class="btn-blue" @click="showMap()">
                    {{ btnTextMap }}
                </a>
            </div>
        </template> -->

        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 card">
                <ul class="top-filter">
                    <li class="top-filter-item" :class="filter.status == 'TODOS' ? 'top-filter-item-active' : ''"
                        @click="filter.status = 'TODOS', getOrders()">Todos <span class="top-filter-number">50</span></li>
                    <li class="top-filter-item" :class="filter.status == 'AGENDADOS' ? 'top-filter-item-active' : ''"
                        @click="filter.status = 'AGENDADOS', getOrders()">Agendados <span class="top-filter-number">50</span></li>
                    <li class="top-filter-item" :class="filter.status == 'ENVIADOS' ? 'top-filter-item-active' : ''"
                        @click="filter.status = 'ENVIADOS', getOrders()">Enviados <span class="top-filter-number">10</span></li>
                    <li class="top-filter-item"
                        :class="filter.status == 'RETIRO_PENDIENTE' ? 'top-filter-item-active' : ''"
                        @click="filter.status = 'RETIRO_PENDIENTE', getOrders()">Enviados(Retiro Pendiente) <span
                            class="top-filter-number">10</span></li>
                    <li class="top-filter-item" :class="filter.status == 'RETIRO' ? 'top-filter-item-active' : ''"
                        @click="filter.status = 'RETIRO', getOrders()">Retiros <span class="top-filter-number">5</span></li>
                    <li class="top-filter-item" :class="filter.status == 'FINALIZADO' ? 'top-filter-item-active' : ''"
                        @click="filter.status = 'FINALIZADO', getOrders()">Finalizados <span class="top-filter-number">5</span></li>
                </ul>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 card py-10 my-5">
                <div class="card-header">
                    <div class="card-title">Próximos Pedidos</div>
                    <div class="card-buttons flex items-stretch">

                        <button class="button-w-icon ml-2" @click="filterBtn = !filterBtn">Filtros
                            <ChevronDownIcon v-if="!filterBtn" class="w-5" />
                            <ChevronUpIcon v-else class="w-5" />
                        </button>

                        <button class="button-w-icon ml-2" @click="showMap()">{{ btnTextMap }} &nbsp;
                            <MapIcon v-if="!showFilter" class="w-5" />
                            <ClipboardListIcon v-else class="w-5" />
                        </button>

                    </div>

                </div>
                <div class="card-filter" v-if="filterBtn">
                    <div class="filter-input-group">
                        <!-- <label>Direccón: </label> -->
                        <input type="text" name="street" id="street" placeholder="Dirección" v-model="filter.street"
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    </div>
                    <div class="filter-input-group">
                        <select id="client" name="client" v-model="filter.client"
                            class="border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Seleccione un cliente</option>
                            <option v-for="client in this.clients" :value="client.id">
                                {{ client.fullname }}
                            </option>
                        </select>
                    </div>
                    <div class="filter-input-group">
                        <!-- <label>Direccón: </label> -->
                        <input disabled />
                    </div>
                    <div class="filter-input-group">
                        <!-- <label>Direccón: </label> -->
                        <input disabled />
                    </div>
                    <div class="flex items-stretch">
                        <div class="filter-button ml-2">
                            <button class="button" @click.prevent="getOrders">
                                <SearchIcon class="w-5 text-gray-700" />
                            </button>
                        </div>
                        <div class="filter-button ml-2">
                            <button class="button" @click.prevent="clearFilter">
                                <XIcon class="w-5 text-gray-700" />
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="bg-white sm:rounded-lg border border-gray-200">
                        <schedule :view="showFilter" />
                        <table class="table w-full whitespace-nowrap" v-if="!showFilter">
                            <tr class="table-header">
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Dirección</th>
                                <th>Cliente</th>
                                <th>Empresa</th>
                                <th>Chofer</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                            <tbody>
                                <tr class="table-row" v-for="order in this.orders">
                                    <td>{{ order.order.id }}</td>
                                    <td>
                                        {{ order.service.date ? order.service.date : '' }}
                                        {{ order.service.time ? ' - ' + order.service.time : '' }}
                                    </td>
                                    <td>{{ order.client.address.google_address }}</td>
                                    <td>{{ order.client.fullname }}</td>
                                    <td>{{ order.client.company ? order.client.company.razon_social : '-' }}</td>
                                    <td>{{ order.service.driver ? order.service.driver.fullname : '-' }}</td>
                                    <td>{{ order.order_status.status }}</td>
                                    <td class=" flex items-stretch">
                                        
                                        <Menu as="div" class="inline-node relative">
                                            <div>
                                                <MenuButton class="">
                                                    <DotsVerticalIcon class="w-4 text-gray-700" aria-hidden="true" />
                                                </MenuButton>
                                            </div>
                                            <transition enter-active-class="transition ease-out duration-100"
                                                enter-from-class="transform opacity-0 scale-95"
                                                enter-to-class="transform opacity-100 scale-100"
                                                leave-active-class="transition ease-in duration-75"
                                                leave-from-class="transform opacity-100 scale-100"
                                                leave-to-class="transform opacity-0 scale-95">
                                                <MenuItems
                                                    class="origin-top-left absolute z-50 right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none">
                                                    <div class="py-1">
                                                        <MenuItem v-slot="{ active }">
                                                        <a href="#" @click="btnOption = !btnOption, form.action = 1"
                                                            class="text-gray-900 block px-4 py-2 text-sm pointer-events hover:bg-gray-100"
                                                            >Editar</a>
                                                        </MenuItem>
                                                    </div>
                                                    <div class="py-1">
                                                        <MenuItem v-slot="{ active }">
                                                        <a href="#" @click="form.action = 2, updateOrder()"
                                                            class="text-gray-900 block px-4 py-2 text-sm pointer-events hover:bg-gray-100"
                                                            >Pasar
                                                            a Entregado</a>
                                                        </MenuItem>
                                                        <MenuItem v-slot="{ active }">
                                                        <a href="#" @click="form.action = 3, updateOrder()"
                                                            class="text-gray-900 block px-4 py-2 text-sm pointer-events hover:bg-gray-100"
                                                            >Pasar
                                                            a Retirado</a>
                                                        </MenuItem>
                                                    </div>
                                                    <div class="py-1">
                                                        <MenuItem v-slot="{ active }">
                                                        <a href="#"
                                                            @click="titleModal = 'Generar Cambio', form.action = 4, open = true"
                                                            class="text-gray-900 block px-4 py-2 text-sm pointer-events hover:bg-gray-100"
                                                            >Generar
                                                            Cambio</a>
                                                        </MenuItem>
                                                        <MenuItem v-slot="{ active }">
                                                        <a href="#"
                                                            @click="titleModal = 'Generar Retiro', form.action = 5, open = true"
                                                            class="text-gray-900 block px-4 py-2 text-sm pointer-events hover:bg-gray-100"
                                                            >Generar
                                                            Retiro</a>
                                                        </MenuItem>
                                                    </div>
                                                    <div class="py-1">
                                                        <MenuItem v-slot="{ active }">
                                                        <a href="#" @click="form.action = 6, updateOrder()"
                                                        class="text-gray-900 block px-4 py-2 text-sm pointer-events hover:bg-gray-100">Cancelar</a>
                                                        </MenuItem>
                                                    </div>
                                                </MenuItems>
                                            </transition>
                                        </Menu>
                                        <button class="ml-2">
                                            <ChevronRightIcon class="w-4 text-gray-700" @click="open = true" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <TransitionRoot as="template" :show="open">
            <Dialog as="div" class="fixed inset-0 overflow-hidden" @close="open = false">
                <div class="absolute inset-0 overflow-hidden">
                    <DialogOverlay class="absolute inset-0" />

                    <div class="fixed inset-y-0 pl-16 max-w-full right-0 flex">
                        <TransitionChild as="template"
                            enter="transform transition ease-in-out duration-500 sm:duration-700"
                            enter-from="translate-x-full" enter-to="translate-x-0"
                            leave="transform transition ease-in-out duration-500 sm:duration-700"
                            leave-from="translate-x-0" leave-to="translate-x-full">
                            <div class="w-screen max-w-md">
                                <form class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl">
                                    <div class="flex-1 h-0 overflow-y-auto">
                                        <div class="py-6 px-4 bg-blue-500 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <DialogTitle class="text-lg font-medium text-white"> Detalle del Pedido
                                                </DialogTitle>

                                                <div class="ml-3 h-7 flex items-center">
                                                    <button type="button"
                                                        class="bg-blue-500 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                                                        @click="open = false">
                                                        <span class="sr-only">Cerrar</span>
                                                        <XIcon class="h-6 w-6" aria-hidden="true" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-1 flex flex-col justify-between">
                                            <div class="px-4 divide-y divide-gray-200 sm:px-6">

                                                <div class="space-y-6 pt-6 pb-5">
                                                    <div>
                                                        Detalle
                                                        <!-- <label for="fullname" class="block text-sm font-medium text-gray-900">Orden</label>
                            <div class="mt-1">
                              <input type="text" name="fullname" id="fullname"
                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" />
                            </div> -->
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 px-4 py-4 flex justify-end">
                                        <button type="button"
                                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            @click="open = false">Cancelar</button>
                                        <button @click.prevent="submit"
                                            class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
        <!-- <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <schedule :view="showFilter" />
            </div>
        </div> -->
    </AppLayout>
</template>

<script>

import { defineComponent } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue';
import Schedule from './Schedule.vue'
import { ChevronDownIcon, ChevronUpIcon, SearchIcon, XIcon, MapIcon, ClipboardListIcon, ChevronRightIcon, DotsVerticalIcon } from '@heroicons/vue/solid'
import {
    Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot, Menu,
    MenuButton,
    MenuItem,
    MenuItems,
} from '@headlessui/vue'

export default defineComponent({
    props: {

    },
    components: {
        AppLayout,
        Schedule,
        ChevronDownIcon,
        ChevronUpIcon,
        SearchIcon,
        XIcon,
        MapIcon,
        ClipboardListIcon,
        ChevronRightIcon,
        DotsVerticalIcon,
        Dialog,
        DialogOverlay,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
        Menu, MenuButton, MenuItem, MenuItems,
    },

    data() {
        return {
            open: false,
            filterBtn: true,
            filterDate: new Date(),
            filter: {
                street: "",
                client: "",
                status: "TODOS"
            },
            showFilter: true,
            btnTextMap: '',
            clients: '',
            orders: '',
        }
    },
    methods: {
        showMap() {
            this.showFilter = !this.showFilter
            if (this.showFilter) {
                this.btnTextMap = 'Ver Pedidos'
            } else {
                this.btnTextMap = 'Ver Mapa'
            }
        },
        clearFilter() {
            this.filter.street = ""
            this.filter.client = ""
            this.filter.status = ""
            this.getOrders()
        },
        async getClients() {
            const get = `${route('clients.list')}`
            const response = await fetch(get, { method: 'GET' })
            let list = await response.json()
            this.clients = list.data.map((d) => { return { id: d.id, fullname: d.fullname } })
        },
        async getOrders() {
            this.orders = ''
            let filter = `date=${this.filterDate.toISOString()}`
            filter += `&status=${this.filter.status}`

            if (this.filter.street.length > 0) {
                filter += `&street=${JSON.stringify(this.filter.street)}`
            }

            if (this.filter.client) {
                filter += `&client=${this.filter.client}`
            }
            const get = `${route('orders.listdashboard')}?${filter}`
            const response = await fetch(get, { method: 'GET' })
            const r = await response.json()

            this.orders = r.data
        },

    },
    created() {
        this.getClients(),
            this.getOrders()
    },
    mounted() {
        this.showMap()
    }
})
const showFilter = false


</script>