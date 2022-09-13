<template>
	<div class="min-h-full">

		<Toast :toast="this.toastMessage" :type="this.labelType" @clear="clearMessage"></Toast>
		<main class="py-10" v-show="view">
			<!-- Page header -->
			<div
				class="max-w-3xl mx-auto px-4 sm:px-6 md:flex md:items-center md:justify-between md:space-x-5 lg:max-w-7xl lg:px-8">
				<div class="flex items-center space-x-5">
					<h1 class="text-2xl font-bold text-gray-900">Mapa de Pedidos</h1>
				</div>

			</div>

			<div class="mt-8 max-w-3xl mx-auto  gap-6 sm:px-6 lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-3">
				<div class="space-y-6 lg:col-start-1 lg:col-span-2">
					<div class="bg-white shadow overflow-hidden sm:rounded-md">
						<GoogleMapCluster v-if="this.showMap" :form_map="form_google">
						</GoogleMapCluster>
					</div>
				</div>
			</div>
		</main>
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
		<main class="py-10" v-show="!view">
			<!-- Page header -->
			<div
				class="max-w-3xl mx-auto px-4 sm:px-6 md:flex md:items-center md:justify-between md:space-x-5 lg:max-w-7xl lg:px-8">

				<div class="flex items-center space-x-5">
					<h1 class="text-2xl font-bold text-gray-900">Proximos pedidos</h1>
				</div>

			</div>

			<div
				class="mt-8 max-w-3xl mx-auto grid grid-cols-1 gap-6 sm:px-6 lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-3">
				<div class="space-y-6 lg:col-start-1 lg:col-span-2">
					<div v-for="order in orders" :key="order.id" class="bg-white shadow sm:rounded-md gap-2">
						<ScheduleItem :order="order" :drivers="this.drivers" @refresh="refreshOrders"></ScheduleItem>
					</div>
				</div>

				<section aria-labelledby="timeline-title" class="lg:col-start-3 lg:col-span-1">
					<div class="bg-white px-3 py-5  sm:rounded-lg">
						<Datepicker id="filterDate" class="w-full mt-1" v-model="filterDate" inline autoApply
							:enableTimePicker="false" :monthChangeOnScroll="false"></Datepicker>

						<div class="mt-6 flex flex-col justify-stretch">
							<a type="button" @click="open = true"
								class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Nuevo
								Pedido</a>
						</div>
					</div>
				</section>
			</div>
		</main>
	</div>

	<TransitionRoot as="template" :show="open">
		<Dialog as="div" class="relative z-10" >
			<TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
				leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
				<div class="fixed inset-0 hidden bg-gray-500 bg-opacity-75 transition-opacity md:block" />
			</TransitionChild>

			<div class="fixed inset-0 z-10 overflow-y-auto ">
				<div class="flex items-stretch justify-center text-center md:items-center md:px-2 lg:px-4 ">
					<TransitionChild as="template" enter="ease-out duration-300"
						enter-from="opacity-0 translate-y-4 md:translate-y-0 md:scale-95"
						enter-to="opacity-100 translate-y-0 md:scale-100" leave="ease-in duration-200"
						leave-from="opacity-100 translate-y-0 md:scale-100"
						leave-to="opacity-0 translate-y-4 md:translate-y-0 md:scale-95">
						<DialogPanel
							class="flex w-full transform text-left text-base transition md:my-8 md:max-w-2xl md:px-4 lg:max-w-4xl ">
							<div
								class="relative flex w-full items-center overflow-hidden bg-white px-4 pt-14 pb-8 shadow-2xl sm:px-6 sm:pt-8 md:p-6 lg:p-8  rounded-md">
								<button type="button"
									class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 sm:top-8 sm:right-6 md:top-6 md:right-6 lg:top-8 lg:right-8"
									@click="open = false">
									<span class="sr-only">Close</span>
									<XIcon class="h-6 w-6 " aria-hidden="true" />
								</button>

								<div
									class="grid w-full grid-cols-1 items-start gap-y-8 gap-x-2 sm:grid-cols-8 lg:gap-2">

									<div class="sm:col-span-8 lg:col-span-8">
										<h1 class="text-2xl font-bold text-gray-900">Nuevo Pedido</h1>
									</div>
									<div class="sm:col-span-8 lg:col-span-8">
										<section aria-labelledby="options-heading" class="mt-10">
											<div class="grid grid-cols-4 gap-2">
												<div class="col-span-2">
													<label for="time"
														class="block text-sm font-medium text-ray-700">Fecha
														Inicio:</label>
													<Datepicker id="date" class="w-full mt-1" v-model="form.date"
														:enableTimePicker="false" :monthChangeOnScroll="false"
														:minDate="new Date(new Date().setDate(new Date().getDate() - 1))"
														autoApply :format="format">
													</Datepicker>

													<label for="time"
														class="block text-sm font-medium text-gray-700">Cliente:</label>
													<select v-model="form.client_id" id="client" name="client" :disabled="newClient ? '' : disabled" :class="newClient ? 'bg-gray-50' : ''"
														class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm "
														@change="setClient(form.client_id)">
														<option value="" selected>Selecciones un Cliente</option>
														<option v-for="client in clients" :key="client.id"
															:value="client.id">
															{{ client.fullname }}
														</option>
													</select>
												</div>

												<div class="col-span-2">
													<label for="time"
														class="block text-sm font-medium text-gray-700">Hora
														Inicio:</label>
													<Datepicker id="time" class="w-full mt-1" v-model="form.time"
														:startTime="startTime" timePicker>
													</Datepicker>
													<label for="time"
														class="block text-sm font-medium text-gray-700">Chofer:</label>
													<select v-model="form.driver" id="driver" name="driver" 
														class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm ">
														<option value="" selected>Selecciones un Chofer</option>
														<option v-for="driver in drivers" :key="driver.id"
															:value="driver.id">{{
																	driver.fullname
															}}</option>
													</select>

												</div>
												<div class="col-span-4">
													<label for="address" v-if="form.google_address"
														class="block text-sm font-medium text-ray-700">
														<b>Dirección: </b>{{ this.form.google_address}}</label>
													<label for="price" v-if="form.price"
														class="block text-sm font-medium text-ray-700">
														<b>Monto:</b> $ {{ form.price.toFixed(2) }}</label>
												</div>

												<div class="col-span-4 py-3" v-if="newClient">
													<div class="border-t border-gray-200"></div>
												</div>

												<div class="col-span-1" v-if="newClient">
													<label for="time"
														class="block text-sm font-medium text-gray-700">Nombre:</label>
													<input type="text" name="price" id="price" v-model="form.fullname_new"
														class="mt-1 w-full focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md " />

												</div>
												<div class="col-span-2" v-if="newClient">
													<label for="time"
														class="block text-sm font-medium text-gray-700">Dirección:</label>
													<vue-google-autocomplete ref="address" id="map"
														classname="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
														placeholder="Ingrese la dirección"
														v-on:placechanged="getAddressData">
													</vue-google-autocomplete>
												</div>
												<div class="col-span-1" v-if="newClient">
													<label for="price_new"
														class="block text-sm font-medium text-gray-700">Monto:</label>
													<input type="number" name="price_new" id="price" v-model="form.price_new"
														class="mt-1 w-full focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md " />

												</div>
												<div class="col-span-4">
													<label for="address" v-if="form.google_address_new"
														class="block text-sm font-medium text-ray-700">
														<b>Dirección: </b>{{ this.form.google_address_new }}</label>
												</div>
											</div>
											<div class="grid grid-cols-4 gap-2 content-start">
												<button @click="open = false"
													class="px-6 py-2 mt-4 text-blue-800 border border-blue-600 rounded">
													Cancelar
												</button>
												<button class="px-6 py-2 mt-4 text-blue-100 bg-blue-600 rounded"
													@click="open = false, createOrder()">
													Guardar
												</button>

												<button
													class="px-6 py-2 mt-4 text-blue-100 bg-blue-600 rounded col-start-4"
													@click="newClient = !this.newClient">
													{{btnTextNewClient}}
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

import { defineComponent, ref } from 'vue'
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import GoogleMapCluster from '../../../Layouts/Components/GoogleMapCluster.vue'
import ScheduleItem from '../../Manager/Dashboard/ScheduleItem.vue'
import { UserIcon, CalendarIcon, LocationMarkerIcon, TruckIcon, XIcon, CheckCircleIcon, ChevronDownIcon } from '@heroicons/vue/solid'
import Toast from '@/Layouts/Components/Toast.vue';
import VueGoogleAutocomplete from "vue-google-autocomplete"

import {
	Menu, MenuButton, MenuItem, MenuItems, Dialog,
	DialogPanel,
	RadioGroup,
	RadioGroupLabel,
	RadioGroupOption,
	TransitionChild,
	TransitionRoot,
} from '@headlessui/vue'

export default {

	props: {
		view: Boolean,
	},

	components: {
		Datepicker,
		TruckIcon,
		CalendarIcon,
		LocationMarkerIcon,
		UserIcon,
		XIcon,
		CheckCircleIcon,
		GoogleMapCluster,
		ScheduleItem,
		Toast,
		Menu,
		MenuButton,
		MenuItem,
		MenuItems,
		ChevronDownIcon,
		Dialog,
		DialogPanel,
		RadioGroup,
		RadioGroupLabel,
		RadioGroupOption,
		TransitionChild,
		TransitionRoot,
		ChevronDownIcon,
		VueGoogleAutocomplete
	},
	data() {
		return {
			filterDate: new Date(),
			orders: "",
			drivers: "",
			clients: "",
			form_google: "",
			form: {},
			data: [],
			showMap: false,
			toastMessage: "",
			labelType: "info",
			showToast: false,
			message: "",
			newClient: false,
			btnTextNewClient: "Nuevo Cliente"
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
		getAddressData: function (addressData, placeResultData, id) {
            this.form.google_address_new = placeResultData['formatted_address']
            this.form.google_area1_new = addressData['administrative_area_level_1']
            this.form.google_postal_code_new = addressData['postal_code']
            this.form.google_latitude_new = addressData['latitude']
            this.form.google_longitude_new = addressData['longitude']
        },
		clearMessage() {
			this.toastMessage = ""
		},
		async getOrders() {
			this.orders = ''
			const filter = `date=${this.filterDate.toISOString()}`
			const get = `${route('orders.listdashboard')}?${filter}`
			const response = await fetch(get, { method: 'GET' })
			const r = await response.json()
			this.orders = r.data
		},
		async getOrdersMap() {

			const get = `${route('orders.listdashboardmap')}`
			const response = await fetch(get, { method: 'GET' })
			const r = await response.json()
			this.form_google = r.data
			this.showMap = true
		},

		async getDrivers() {
			const get = `${route('drivers.list')}`
			const response = await fetch(get, { method: 'GET' })
			const r = await response.json()
			this.drivers = r.data
		},

		async getClients() {
			const get = `${route('clients.list')}`
			const response = await fetch(get, { method: 'GET' })
			const r = await response.json()
			this.clients = r.data
		},
		refreshOrders(payload) {
			if (payload[0] == 200) {
				this.getOrders();
				this.message = payload[1]
				this.showToast = true
			} else {
				this.labelType = "danger"
				this.toastMessage = payload[1]
			}
		},
		setClient(id) {
			let client = this.clients.find((c) => { return c.id == id })

			this.form.price = client.price
			this.form.google_address = client.address[0].google_address
		},
		createOrder() {
			let rt = route('orders.storedashboard');

			axios.post(rt, {
				form: this.form,
			}).then(response => {
				console.log(response)
				if (response.status == 200) {
					this.form = {};
					this.getOrders();
					this.message = response.data.message
					this.showToast = true
					this.newClient = false
				} else {
					this.labelType = "danger"
					this.toastMessage = response.data.message
				}
			})
		},
		cleanNewUser(){
			this.form.google_address_new = ''
			this.form.google_area1_new = ''
			this.form.google_postal_code_new = ''
			this.form.google_latitude_new = ''
			this.form.google_longitude_new = ''
			this.form.fullname_new = ''
		},
	},
	watch: {
		filterDate: function () {
			this.getOrders()
		},
		newClient: function (value) {
			if(value){
				this.btnTextNewClient = 'Cancelar N. Cliente'
				this.cleanNewUser()
				this.form.newClient = true
				
			}else{
				this.btnTextNewClient = 'Nuevo Cliente'
				this.cleanNewUser()
				this.form.newClient = false
			}
		}
	},
	created() {
		this.getOrders(),
			this.getOrdersMap(),
			this.getDrivers(),
			this.getClients(),
			this.form.newClient = false
	}
}
</script>