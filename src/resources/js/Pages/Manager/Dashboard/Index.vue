<template>
    <AppLayout title="Dashboard">
        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 card">
                <ul class="top-filter">
                    <li class="top-filter-item" :class="filter.status == 'TODOS' ? 'top-filter-item-active' : ''"
                        @click="filter.status = 'TODOS', this.orders_view = this.filter_todos">Todos <span
                            class="top-filter-number">{{ this.filter_todos.length }}</span></li>
                    <li class="top-filter-item"
                        :class="filter.status == 'PROGRAMADOS' ? 'top-filter-item-active' : ''"
                        @click="filter.status = 'PROGRAMADOS', this.orders_view = this.filter_programados"
                        >Programados
                        <span class="top-filter-number">
                            {{ this.filter_programados.length }}</span>
                    </li>
                    <li class="top-filter-item" :class="filter.status == 'ENVIADOS' ? 'top-filter-item-active' : ''"
                        @click="filter.status = 'ENVIADOS', this.orders_view = this.filter_enviados">Enviados <span
                            class="top-filter-number">{{ this.filter_enviados.length }}</span></li>
                    <li class="top-filter-item"
                        :class="filter.status == 'RETIRO_PENDIENTE' ? 'top-filter-item-active' : ''"
                        @click="filter.status = 'RETIRO_PENDIENTE', this.orders_view = this.filter_retiros_pendientes">
                        Enviados(Retiro Pendiente) <span class="top-filter-number">{{
                                this.filter_retiros_pendientes.length
                        }}</span></li>
                    <li class="top-filter-item" :class="filter.status == 'RETIRO' ? 'top-filter-item-active' : ''"
                        @click="filter.status = 'RETIRO', this.orders_view = this.filter_retiros">Retiros <span
                            class="top-filter-number">{{ this.filter_retiros.length }}</span></li>
                    <li class="top-filter-item" :class="filter.status == 'FINALIZADO' ? 'top-filter-item-active' : ''"
                        @click="filter.status = 'FINALIZADO', this.orders_view = this.filter_finalizados">Finalizados
                        <span class="top-filter-number">{{ this.filter_finalizados.length }}</span>
                    </li>
                    <li class="top-filter-item" :class="filter.status == 'CANCELADO' ? 'top-filter-item-active' : ''"
                        @click="filter.status = 'CANCELADO', this.orders_view = this.filter_cancelados">Cancelados
                        <span class="top-filter-number">{{ this.filter_cancelados.length }}</span>
                    </li>
                </ul>
            </div>

            <div class="max-w-7xl mx-auto mt-4">
                <div v-if="showToast" class="rounded-md bg-green-50 p-4 mb-5"
                    :class="labelType == 'info' ? 'bg-green-50' : 'bg-red-50'">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <CheckCircleIcon v-if="labelType == 'info'" class="h-5 w-5 text-green-400"
                                aria-hidden="true" />
                            <XCircleIcon v-else class="h-5 w-5 text-red-400" aria-hidden="true" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium "
                                :class="labelType == 'info' ? 'text-green-800' : 'text-red-800'">{{ this.message }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button type="button" @click="showToast = false"
                                    class="inline-flex  rounded-md p-1.5  focus:outline-none focus:ring-2 focus:ring-offset-2 "
                                    :class="labelType == 'info' ? 'bg-green-50 text-green-500 hover:bg-green-100 focus:ring-offset-green-50 focus:ring-green-600' : 'bg-red-50 text-red-500 hover:bg-red-100 focus:ring-offset-red-50 focus:ring-red-600'">
                                    <span class="sr-only">Dismiss</span>
                                    <XIcon class="h-5 w-5" aria-hidden="true" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 card py-10 my-5">
                <div class="card-header">
                    <div class="card-title">
                        {{ filter.status }}: {{ this.totalOrders }}
                    </div>
                    <div class="card-buttons flex items-stretch">

                        <button class="button-w-icon ml-2" @click="showNewOrder = true">Nuevo Pedido &nbsp;
                            <PlusCircleIcon class="w-5" />
                        </button>

                        <button class="button-w-icon ml-2" @click="showMap()">{{ btnTextMap }} &nbsp;
                            <MapIcon v-if="!showFilter" class="w-5" />
                            <ClipboardListIcon v-else class="w-5" />
                        </button>

                        <button class="button-w-icon ml-2" @click="filterBtn = !filterBtn">Filtros
                            <ChevronDownIcon v-if="!filterBtn" class="w-5" />
                            <ChevronUpIcon v-else class="w-5" />
                        </button>

                    </div>

                </div>

                <div v-if="filterBtn" class="bg-white border border-gray-200 rounded-xl shadow-sm mt-2.5">

                    <div class="px-5 py-4 grid grid-cols-12 gap-x-4 gap-y-4">

                        <!-- FILA 1: Fecha y Dirección -->
                        <div class="col-span-12 md:col-span-6">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1.5">Fecha</label>
                            <Datepicker
                                class="block w-full text-sm border-gray-200 rounded-lg shadow-none"
                                v-model="filter.date" range multiCalendars
                                :closeOnAutoApply="true" :enableTimePicker="false" :format="customFormat"
                            />
                        </div>
                        <div class="col-span-12 md:col-span-6">
                            <label for="street" class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1.5">Dirección</label>
                            <input v-model="filter.street" type="text" name="street" id="street" autocomplete="off"
                                placeholder="Buscar por dirección…"
                                class="block w-full py-2 px-3 text-sm border border-gray-200 rounded-lg bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent focus:bg-white transition" />
                        </div>

                        <!-- FILA 2: Cliente, Chofer y Ver -->
                        <div class="col-span-12 md:col-span-6">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1.5">Cliente</label>
                            <ClientAutocomplete
                                v-model="filter.client"
                                :clients="clients"
                                label=""
                                placeholder="Buscar cliente…"
                                input-id="filter-client"
                            />
                        </div>
                        <div class="col-span-8 md:col-span-4">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1.5">Chofer</label>
                            <div class="relative">
                                <input
                                    v-model="driverSearch"
                                    type="text"
                                    placeholder="Buscar chofer…"
                                    autocomplete="off"
                                    class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white"
                                    @focus="showDriverDropdown = true"
                                    @blur="hideDriverDropdown()"
                                />
                                <div v-if="showDriverDropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-auto">
                                    <div
                                        @mousedown.prevent="filter.driver = ''; driverSearch = ''; showDriverDropdown = false"
                                        class="px-3 py-2 cursor-pointer text-sm text-gray-500 hover:bg-gray-100">
                                        Todos los choferes
                                    </div>
                                    <div
                                        v-for="driver in filteredDrivers"
                                        :key="driver.id"
                                        @mousedown.prevent="filter.driver = driver.id; driverSearch = driver.fullname; showDriverDropdown = false"
                                        class="px-3 py-2 cursor-pointer text-sm hover:bg-gray-100">
                                        {{ driver.fullname }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1.5">Ver</label>
                            <select v-model="pageLength"
                                class="block w-full py-2 px-2 text-sm border border-gray-200 bg-gray-50 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent focus:bg-white transition text-center">
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="200">200</option>
                                <option value="999">Todos</option>
                            </select>
                        </div>

                        <!-- FILA 3: Tipo y Limpiar -->
                        <div class="col-span-12 flex items-center justify-between border-t border-gray-100 pt-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 mr-1">Tipo</span>
                                <button
                                    @click.prevent="filter.typeEnvio = !filter.typeEnvio"
                                    :class="filter.typeEnvio
                                        ? 'bg-blue-500 text-white border-blue-500 shadow-sm'
                                        : 'bg-white text-blue-400 border-blue-200 hover:border-blue-400'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border transition-all duration-150">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="filter.typeEnvio ? 'bg-white' : 'bg-blue-400'"></span>
                                    Envío
                                </button>
                                <button
                                    @click.prevent="filter.typeCambio = !filter.typeCambio"
                                    :class="filter.typeCambio
                                        ? 'bg-yellow-400 text-white border-yellow-400 shadow-sm'
                                        : 'bg-white text-yellow-500 border-yellow-200 hover:border-yellow-400'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border transition-all duration-150">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="filter.typeCambio ? 'bg-white' : 'bg-yellow-400'"></span>
                                    Cambio
                                </button>
                                <button
                                    @click.prevent="filter.typeRetiro = !filter.typeRetiro"
                                    :class="filter.typeRetiro
                                        ? 'bg-red-500 text-white border-red-500 shadow-sm'
                                        : 'bg-white text-red-400 border-red-200 hover:border-red-400'"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border transition-all duration-150">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="filter.typeRetiro ? 'bg-white' : 'bg-red-400'"></span>
                                    Retiro
                                </button>
                            </div>
                            <button @click.prevent="clearFilter"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Limpiar filtro
                            </button>
                        </div>

                    </div>
                </div>

                <div class="card-body">
                    <div class="bg-white sm:rounded-lg border border-gray-200 overflow-x-auto">
                        <schedule :view="showFilter" ref="componenteSchedule"/>
                        <table class="table w-full whitespace-nowrap" v-if="!showFilter">

                            <thead>
                                <tr class="table-header text-left">
                                    <th class="pl-2">ID</th>
                                    <th>Fecha</th>
                                    <th>Dirección</th>
                                    <th class="text-center">Cliente</th>
                                    <th class="text-center">Empresa</th>
                                    <th class="text-center">Chofer</th>
                                <th>Tipo</th>
                                <th>Estado</th>

                                <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-row text-left" v-for="order in this.orders_view" :key="order.order.id">
                                    <td class="pl-2">{{ order.order.id }}</td>
                                    <td>
                                        {{ order.service.date ? formatDate(order.service.date) : '' }}
                                        {{ order.service.time ? ' - ' + formatHora(order.service.time) : '' }}
                                    </td>
                                    <td>{{ order.client.address && order.client.address.google_address ? order.client.address.google_address.split(",", 2).toString() : ''}}</td>
                                    <td class="text-center right p-2">{{ order.client.id }}</td>
                                    <td class="text-center right p-2">{{ order.client.company ? order.client.company.id : '-' }}</td>
                                    <td class="text-center right p-2">{{ order.service.driver ? order.service.driver.id : '-' }}</td>
                                    <td>
                                        <span :class="{
                                            'text-blue-600 font-semibold': order.service.type.type === 'ENVIO',
                                            'text-red-600 font-semibold': order.service.type.type === 'RETIRO',
                                            'text-yellow-500 font-semibold': order.service.type.type === 'CAMBIO'
                                        }">{{ order.service.type.type }}</span>
                                    </td>
                                    <td>{{ order.order_status.status }}</td>
                                    <td>
                                        <Icons name="money" class="w-6 h-6"
                                               :class="order.order.payment ? 'text-green-600' : [order.order_status.status == 'RETIRADO' ? 'text-red-500' : 'text-gray-300']"/>

                                    </td>


                                    <td>
                                        <div class="flex items-center justify-end pr-3">
                                            <a v-if="order.client.cellphone" :href="`https://wa.me/${order.client.cellphone}`" target="_blank" class="bg-green-600 text-white py-1 px-2 rounded-md hover:bg-green-400 flex items-center cursor-pointer">
                                                <Icons name="whatsapp" class="w-4 h-4 text-white" />
                                                <span class="ml-1">Whatsapp</span>
                                            </a>

                                            <Menu as="div" class="ml-2 inline-node relative">
                                                <div>
                                                    <MenuButton class="">
                                                        <DotsVerticalIcon class="w-4 text-gray-700" aria-hidden="true"/>
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
                                                            <!-- <a href="#" @click="fnEnviarUpdateOrder(order, 1, 'Actualización de Pedido'), showUpdateOrder = true"
                                                                class="text-gray-900 block px-4 py-2 text-sm pointer-events hover:bg-gray-100"
                                                                :class="order.order_status.status == 'PROGRAMADO' ? '' : 'pointer-events-none text-gray-400'"
                                                                >Editar</a> -->
                                                            <MenuItem v-slot="{ active }">
                                                            <a href="#" @click="fnEnviarUpdateOrder(order, 1, 'Actualización de Servicio'), showUpdateOrder = true"
                                                                class="text-gray-900 block px-4 py-2 text-sm pointer-events hover:bg-gray-100">Editar</a>

                                                            </MenuItem>
                                                        </div>
                                                        <div class="py-1">
                                                            <MenuItem v-slot="{ active }">
                                                            <a href="#"
                                                                @click="fnEnviarUpdateOrder(order, 2, 'Generación de Cambio'), showUpdateOrder = true"
                                                                class="text-gray-900 block px-4 py-2 text-sm pointer-events hover:bg-gray-100"
                                                                :class="(order.service.status.status == 'FINALIZADO'
                                                                        && order.order_status.status != 'PROGRAMADO'
                                                                        && (order.service.type.type == 'ENVIO' || order.service.type.type == 'CAMBIO'))
                                                                        ? ''
                                                                        : 'pointer-events-none text-gray-400'"
                                                                >Generar Cambio</a>
                                                            </MenuItem>
                                                            <MenuItem v-slot="{ active }">
                                                            <a href="#"
                                                                @click="fnEnviarUpdateOrder(order, 3, 'Generación de Retiro'), showUpdateOrder = true"
                                                                class="text-gray-900 block px-4 py-2 text-sm pointer-events hover:bg-gray-100"
                                                                :class="(order.service.status.status == 'FINALIZADO'
                                                                        && order.order_status.status != 'PROGRAMADO'
                                                                        && (order.service.type.type == 'ENVIO' || order.service.type.type == 'CAMBIO'))
                                                                        ? ''
                                                                        : 'pointer-events-none text-gray-400'"
                                                                >Generar Retiro</a>
                                                            </MenuItem>
                                                        </div>
                                                        <div class="py-1">

                                                            <MenuItem v-slot="{ active }">
                                                            <a href="#" @click="fnEnviarUpdateOrder(order, 5,'¿Desea registrar el pago del pedido N° '+order.order.id+'?'), showUpdateOrder = true "
                                                                class="text-gray-900 block px-4 py-2 text-sm pointer-events hover:bg-gray-100"
                                                                :class="!order.order.payment ? '' : 'pointer-events-none text-gray-400'"
                                                                >Registrar Pago</a>
                                                            </MenuItem>

                                                            <MenuItem v-slot="{ active }">
                                                            <a href="#" @click="fnEnviarUpdateOrder(order, 4, '¿Desea cancelar el pedido N° '+order.order.id+'?'), showUpdateOrder = true"
                                                                class="text-gray-900 block px-4 py-2 text-sm pointer-events hover:bg-gray-100"
                                                                :class="order.order_status.status != 'RETIRADO'
                                                                        && order.order_status.status != 'CANCELADO' ? '' : 'pointer-events-none text-gray-400'"
                                                                >Cancelar</a>
                                                            </MenuItem>

                                                        </div>
                                                        <div class="py-1">

                                                            <MenuItem v-slot="{ active }">
                                                            <a href="#" @click="this.deleteIdOrder = order.order.id, showDeleteOrder = true"
                                                                class="text-gray-900 block px-4 py-2 text-sm pointer-events hover:bg-gray-100"
                                                                >Eliminar Pedido</a>
                                                            </MenuItem>
                                                        </div>
                                                    </MenuItems>
                                                </transition>
                                            </Menu>

                                            <button class="ml-2">
                                                <ChevronRightIcon class="w-4 text-gray-700"
                                                    @click="this.form = order,open = true, detailsOrder(order.order.id)" />
                                            </button>
                                        </div>
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

                                                        <div class="space-y-2 pt-2 pb-5">
                                                            <div>
                                                                <label for="time"
                                                                    class="block text-xl font-medium text-gray-700 "><b>Pedido N°: {{ form.order.id }}</b></label>
                                                            </div>
                                                            <hr>
                                                            <div class="flex text-sm text-gray-700">
                                                                <label class="text-bold w-24 font-bold">Cliente:</label>
                                                                <span>{{ form.client.fullname }}</span>
                                                            </div>

                                                            <div class="flex items-center">
                                                                <label for="cellphone" class="block text-sm font-medium text-gray-700 w-24"><b>Whatsapp:</b></label>
                                                                <input v-model="form.client.cellphone" class="border border-gray-300 ml-2 text-sm w-3/5 rounded-md pl-2 py-1" />
                                                                <button @click.prevent="update_client" class="bg-blue-500 text-white ml-2 py-1 px-2 rounded-md text-sm hover:bg-blue-600">Guardar</button>
                                                                 <!-- {{ form.client.cellphone }} -->
                                                            </div>

                                                            <div class="flex text-sm text-gray-700">
                                                                <label class="text-bold w-24 font-bold">Empresa:</label>
                                                                <span>{{  form.client.company ? form.client.company.razon_social : '-'  }}</span>
                                                            </div>

                                                            <div class="flex text-sm text-gray-700">
                                                                <label class="text-bold w-24 font-bold">Chofer:</label>
                                                                <span>{{ form.service.driver ? form.service.driver.fullname : '-' }}</span>
                                                            </div>

                                                            <div class="flex text-sm text-gray-700">
                                                                <label class="text-bold w-24 font-bold">Fecha Inicio:</label>
                                                                <span>{{ formatDate(form.service.date) }}</span>
                                                            </div>

                                                            <div class="flex text-sm text-gray-700">
                                                                <label class="text-bold w-24 font-bold">Hora Inicio:</label>
                                                                <span>{{ formatHora(form.service.time) }}</span>
                                                            </div>

                                                            <div class="flex text-sm text-gray-700">
                                                                <label class="text-bold w-24 font-bold">Estado:</label>
                                                                <span>{{ form.service.type.type }} - {{ form.order_status.status }}</span>
                                                            </div>

                                                            <div class="flex text-sm text-gray-700">
                                                                <label class="text-bold w-24 font-bold">Dirección:</label>
                                                                <span class="w-80">{{ form.client.address && form.client.address.google_address ? form.client.address.google_address : 'Sin dirección' }}</span>
                                                            </div>

                                                            <div class="flex text-sm text-gray-700">
                                                                <label class="text-bold w-24 font-bold">Monto Total:</label>
                                                                <span>$ {{ form.order.total_price.toFixed(2) }}</span>
                                                            </div>

                                                            <div class="flex text-sm text-gray-700">
                                                                <label class="text-bold w-24 font-bold">Pago:</label>
                                                                <span v-if="form.order.payment">PAGADO</span>
                                                                <span v-else>PENDIENTE</span>

                                                            </div>

                                                            <div class="flex text-sm text-gray-700">
                                                                <label class="text-bold w-24 font-bold">Forma Pago:</label>
                                                                <span>{{ form.order.payment_type}}</span>
                                                            </div>

                                                            <div class="flex text-sm text-gray-700">
                                                                <label class="text-bold w-24 font-bold">Cobrador:</label>
                                                                <span>{{ form.order.collector }}</span>
                                                            </div>

                                                            <div>
                                                                <label for="comentario"
                                                                    class="block text-sm font-medium text-gray-700">
                                                                    <b>Notas: </b></label>
                                                                    <textarea type="text" rows=5 name="notes" id="notes"
                                                                    v-model="form.order.notes"
                                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"> </textarea>
                                                            </div>
                                                            <hr>
                                                            <div>
                                                                <label for="time"
                                                                    class="block text-xl font-medium text-gray-800">Historial</label>
                                                            </div>
                                                            <div v-for="s in list_services" class="bg-gray-50 p-2">
                                                                <label for="time"
                                                                    class="block text-sm font-medium text-gray-700">
                                                                    <b>Fecha: </b>{{ formatDate(s.date) }} {{ formatHora(s.time) }}</label>

                                                                <label v-if="s.driver" for="time"
                                                                    class="block text-sm font-medium text-gray-700">
                                                                    <b>Chofer: </b>{{s.driver.fullname }}</label>

                                                                <label v-else for="time"
                                                                    class="block text-sm font-medium text-gray-700">
                                                                    <b>Chofer: </b>Sin conductor</label>

                                                                <label for="time"
                                                                    class="block text-sm font-medium text-gray-700">
                                                                    <b>Tipo Servicio: </b>{{ s.type.type }}</label>

                                                                <label for="time"
                                                                    class="block text-sm font-medium text-gray-700">
                                                                    <b>Estado Servicio: </b>{{ s.status.status }}</label>

                                                                <label for="price"
                                                                    class="block text-sm font-medium text-gray-700">
                                                                    <b>Monto Servicio: </b>$ {{ s.price.toFixed(2) }}</label>

                                                                <hr>
                                                            </div>
                                                        </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 px-4 py-4 flex justify-end">
                                        <button type="button"
                                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            @click="open = false">Ocultar</button>
                                        <button @click.prevent="updateNote()"
                                            class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Actualizar Nota</button>
                                    </div>
                                </form>
                            </div>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>

        <NewOrder :show="showNewOrder"
                  @viewNewOrder="fnShowNewOrder"
                  @responseNewOrder="fnNewOrder('data', $event)" />

        <UpdateOrder :show="showUpdateOrder"
                     @viewUpdateOrder="fnShowUpdateOrder"
                     ref="componenteUpdateOrder"
                     @responseUpdateOrder="fnUpdateOrder('data', $event)" />

        <DeleteOrder v-if="showDeleteOrder" :show="showDeleteOrder" :idOrder="deleteIdOrder"
                     @viewDeleteOrder="fnShowDeleteOrder"
                     ref="componenteDeleteOrder"
                     @responseDeleteOrder="fnDeleteOrder" />

    </AppLayout>
</template>

<script>

import { defineComponent, ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue';
import Schedule from './Schedule.vue'
import { ChevronDownIcon,
         ChevronUpIcon,
         SearchIcon,
         XIcon,
         MapIcon,
         ClipboardListIcon,
         ChevronRightIcon,
         DotsVerticalIcon,
         PlusCircleIcon,
         CheckCircleIcon,
         XCircleIcon
        } from '@heroicons/vue/solid'
import {
         Dialog,
         DialogOverlay,
         DialogTitle,
         TransitionChild,
         TransitionRoot,
         Menu,
         MenuButton,
         MenuItem,
         MenuItems,
        } from '@headlessui/vue'
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import NewOrder from '../../Manager/Dashboard/NewOrder.vue'
import UpdateOrder from '../../Manager/Dashboard/UpdateOrder.vue'
import moment from 'moment'
import Icons from '@/Layouts/Components/Icons.vue'
import DeleteOrder from '@/Layouts/Components/Orders/DeleteOrder.vue'
import ClientAutocomplete from '@/Components/ClientAutocomplete.vue'

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
        ClientAutocomplete,
        PlusCircleIcon,
        CheckCircleIcon,
        XCircleIcon,
        Dialog,
        DialogOverlay,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        Datepicker,
        NewOrder,
        UpdateOrder,
        DeleteOrder,
        moment,
        Icons
    },

    data() {
        return {
            open: false,
            openUpdate: false,
            showNewOrder: false,
            showUpdateOrder: false,
            showDeleteOrder: false,
            deleteIdOrder: '',
            filterBtn: true,
            filter: {
                street: "",
                client: "",
                driver: "",
                status: "TODOS",
                date: [
                    new Date(new Date().getTime() - 7 * 24 * 60 * 60 * 1000 - 3600000 * 3),
                    new Date(new Date().getTime() - 3600000 * 3)
                ],
                typeEnvio: true,
                typeCambio: true,
                typeRetiro: true
            },
            showFilter: true,
            btnTextMap: '',
            clients: '',
            drivers: '',
            orders_view: '',
            orders: '',
            filter_todos: '',
            filter_programados: '',
            filter_enviados: '',
            filter_retiros_pendientes: '',
            filter_retiros: '',
            filter_finalizados: '',
            filter_cancelados: '',
            filter_cant_total: '0',
            showToast: false,
            message: '',
            labelType: 'danger',

            btnOption: false,
            btnEditar: false,
            btnEntregado: false,
            btnRetirado: false,
            btnCambio: false,
            btnRetiro: false,
            btnCancelar: false,
            titleModal: "",
            list_services: "",

            form: {},
            pageLength: 100,
            searchTimeout: null,
            totalOrders: 0,
            driverSearch: '',
            showDriverDropdown: false,

        }
    },
    computed: {
        filteredDrivers() {
            if (!this.drivers || !this.driverSearch) return this.drivers || []
            const q = this.driverSearch.toLowerCase()
            return this.drivers.filter(d => d.fullname.toLowerCase().includes(q))
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

        const open = ref(false)

        return {
            format,
            startTime,
            open
        }
    },
    methods: {
        formatHora(time) {
			//time = { hours: moment(time, 'HH:mm').format('HH:mm'), minutes: moment(time, 'HH:mm').format('mm') };
            return moment(time, 'HH:mm').format('HH:mm')
		},
        formatDate(date) {
            return moment(date, 'Y/mm/D').format('D/mm/Y')
		},
        fnShowNewOrder() {
            this.showNewOrder = false
        },
        fnShowUpdateOrder() {
            this.showUpdateOrder = false
        },
        fnShowDeleteOrder() {
            this.showDeleteOrder = false
        },
        fnNewOrder(data, $event) {
            this.showNewOrder = false
            if ($event.status == 200) {
                this.message = $event.data.message
                this.labelType = "info"
                this.showToast = true
                this.getOrders()
            } else {
                this.labelType = "danger"
                this.message = $event.data.message
                this.showToast = true
            }
        },
        fnUpdateOrder(data, $event) {
            this.showNewOrder = false
            if ($event.status == 200) {
                this.message = $event.data.message
                this.labelType = "info"
                this.showToast = true
                this.getOrders()
            } else {
                this.labelType = "danger"
                this.message = $event.data.message
                this.showToast = true
            }
        },
        fnEnviarUpdateOrder(order, action, title){
            this.$refs.componenteUpdateOrder.getOrder(order, action, title)
        },
        async detailsOrder(id){

            const get = `${route('orders.services', id)}`
            const response =  await fetch(get, { method: 'GET' })
            let list =  await response.json()
            //console.log(list)
            this.list_services = await list.data[0].services
            //this.open = true

        },
        updateNote(){
            console.log(this.form.order.id + " NOTA " +this.form.order.notes)
            let rt = route('orders.updatedashboard');
			axios.post(rt, {
				form: {
                    action: 6,
                    order_id: this.form.order.id,
                    notes: this.form.order.notes
                },
			}).then(response => {
				if (response.status == 200) {
                    this.labelType = "info"
					this.message = response.data.message
					this.showToast = true
				} else {
					this.labelType = "danger"
					this.toastMessage = response.data.message
                    this.showToast = true
				}
				this.open = false
			})
        },

        showMap() {
            this.showFilter = !this.showFilter
            if (this.showFilter) {
                this.btnTextMap = 'Ver Listado'
            } else {
                this.btnTextMap = 'Ver Mapa'
            }
        },

        hideDriverDropdown() {
            setTimeout(() => { this.showDriverDropdown = false }, 150)
        },
        clearFilter() {
            this.filter.street = ""
            this.filter.client = ""
            //this.filter.status = "TODOS"
            this.filter.driver = ""
            this.driverSearch = ""
            this.filter.date = [
                    new Date(new Date().getTime() - 7 * 24 * 60 * 60 * 1000 - 3600000 * 3),
                    new Date(new Date().getTime() - 3600000 * 3)
            ]
            this.filter.typeEnvio = true
            this.filter.typeCambio = true
            this.filter.typeRetiro = true
            this.getOrders()
        },

        async getClients() {
            // const get = `${route('clients.list')}`
            const get = `${route('clients.fulllist')}`
            const response = await fetch(get, { method: 'GET' })
            // let list = await response.json()
            this.clients = await response.json()
            // this.clients = list.map((d) => { return { id: d.id, fullname: d.fullname } })
            // this.clients = list.data.map((d) => { return { id: d.id, fullname: d.fullname } })
        },

        async getDrivers() {
            const get = `${route('drivers.list')}`
            const response = await fetch(get, { method: 'GET' })
            let list = await response.json()
            this.drivers = list.data.map((d) => { return { id: d.id, fullname: d.fullname } })
        },

        async getOrders() {

            this.orders = ''
            //this.orders_view = ''
            let filter = `date=${JSON.stringify(this.filter.date)}`
            filter += `&status=${this.filter.status}`

            if (this.filter.street.length > 0) {
                filter += `&street=${JSON.stringify(this.filter.street)}`
            }

            if (this.filter.client) {
                filter += `&client=${this.filter.client}`
            }

            if (this.filter.driver) {
                filter += `&driver=${this.filter.driver}`
            }

            const get = `${route('orders.listdashboard')}?${filter}`
            const response = await fetch(get, { method: 'GET' })
            const r = await response.json()

            //this.orders_view = r.data
            this.orders = r.data
            this.create_filter()
        },

        async create_filter() {
            this.orders = this.orders.sort(function (a, b) {
                    return a.service.id - b.service.id;
            });

            this.orders = this.orders.sort((p1, p2) => (p1.service.time < p2.service.time) ? 1 : (p1.service.time > p2.service.time) ? -1 : 0);
            this.orders = this.orders.sort((p1, p2) => (p1.service.date < p2.service.date) ? 1 : (p1.service.date > p2.service.date) ? -1 : 0);

            // Filtrar por tipo de servicio
            this.orders = this.orders.filter(order => {
                const type = order.service?.type?.type;
                if (type === 'ENVIO' && !this.filter.typeEnvio) return false;
                if (type === 'CAMBIO' && !this.filter.typeCambio) return false;
                if (type === 'RETIRO' && !this.filter.typeRetiro) return false;
                return true;
            });

            this.filter_programados = this.orders.filter(element => {
                return element.order_status.status == 'PROGRAMADO' && element.order_status.status != 'CANCELADO';
            });
            this.filter_enviados = this.orders.filter(element => {
                return element.order_status.status != 'PROGRAMADO' && element.order_status.status != 'CANCELADO' && (element.service.type.type == 'ENVIO' || element.service.type.type == 'CAMBIO');
            });
            this.filter_retiros_pendientes = this.orders.filter(element => {
                return element.order_status.status != 'PROGRAMADO' && element.order_status.status != 'CANCELADO' && (element.service.type.type == 'ENVIO' || element.service.type.type == 'CAMBIO') && element.service.status.status == 'FINALIZADO';
            });
            this.filter_retiros = this.orders.filter(element => {
                return element.order_status.status != 'PROGRAMADO' && element.order_status.status != 'CANCELADO' && element.service.type.type == 'RETIRO';
            });
            this.filter_finalizados = this.orders.filter(element => {
                return element.order_status.status == 'RETIRADO';
            });
            this.filter_cancelados = this.orders.filter(element => {
                return element.order_status.status == 'CANCELADO';
            });
            this.filter_todos = this.orders.filter(element => {
                return element.order_status.status != 'CANCELADO';
            });

            let base
            switch (this.filter.status) {
                case 'PROGRAMADOS':
                    base = this.filter_programados
                    break;
                case 'ENVIADOS':
                    base = this.filter_enviados
                    break;
                case 'RETIRO_PENDIENTE':
                    base = this.filter_retiros_pendientes
                    break;
                case 'RETIRO':
                    base = this.filter_retiros
                    break;
                case 'FINALIZADO':
                    base = this.filter_finalizados
                    break;
                case 'CANCELADO':
                    base = this.filter_cancelados
                    break;
                case 'TODOS':
                    base = this.filter_todos
                    break;
                default:
                    base = this.orders
                    break;
            }
            this.totalOrders = base.length
            this.orders_view = base.slice(0, parseInt(this.pageLength))
        },

        async update_client(){

            let post = route('clients.updatedashboard');
			axios.post(post, this.form.client)
                 .then(response => {
                    console.log(response.data)

				    if (response.status == 200) {
                        this.labelType = "info"
                        this.message = response.data.message
                        this.showToast = true
                    } else {
                        this.labelType = "danger"
                        this.toastMessage = response.data.message
                        this.showToast = true
                    }
                    // this.open = false
			})

        },
		async fnDeleteOrder(data) {
            this.showDeleteOrder = false
            const response = await axios.post(route('orders.destroy'), { data });

			if (response.status == 200) {
				this.labelType = "info"
                this.message = response.data.message
                this.showToast = true
                this.getOrders()

			} else {
				this.labelType = "danger"
                this.toastMessage = response.data.message
                this.showToast = true
			}
		},

    },
    created() {
        this.getClients(),
        this.getDrivers(),
        this.getOrders()
    },
    mounted() {
        this.showMap()
    },
    watch: {
        orders_view(){
            this.$refs.componenteSchedule.getFilterMap(this.orders_view)
        },
        'filter.date': function() {
            this.getOrders()
        },
        'filter.client': function() {
            this.getOrders()
        },
        'filter.driver': function() {
            this.getOrders()
        },
        'filter.street': function() {
            clearTimeout(this.searchTimeout)
            this.searchTimeout = setTimeout(() => {
                this.getOrders()
            }, 400)
        },
        'filter.typeEnvio': function() {
            this.getOrders()
        },
        'filter.typeCambio': function() {
            this.getOrders()
        },
        'filter.typeRetiro': function() {
            this.getOrders()
        },
        'filter.status': function() {
            this.create_filter()
        },
        'pageLength': function() {
            this.create_filter()
        },
    },
})
const showFilter = false


</script>
