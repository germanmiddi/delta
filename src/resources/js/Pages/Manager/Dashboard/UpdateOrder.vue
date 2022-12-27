<template>
	<TransitionRoot as="template">
		<Dialog as="div" class="relative z-10">
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
									@click="closeModal">
									<span class="sr-only">Close</span>
									<XIcon class="h-6 w-6 " aria-hidden="true" />
								</button>

								<div
									class="grid w-full grid-cols-1 items-start gap-y-8 gap-x-2 sm:grid-cols-8 lg:gap-2">

									<div class="sm:col-span-8 lg:col-span-8">
										<h1 class="text-2xl font-bold text-gray-900">{{modal_title}}</h1>
										
									</div>
									<div class="sm:col-span-8 lg:col-span-8">
										<section aria-labelledby="options-heading" class="mt-10" v-if="!showCancelar">
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
														class="block text-sm font-medium text-gray-700">Chofer:</label>
													<select v-model="form.driver_id" id="driver" name="driver" 
														class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm ">
														<option value="" selected>Selecciones un Chofer</option>
														<option v-for="driver in drivers" :key="driver.id"
															:value="driver.id"
															:bind:select="driver.id == form.driver_id"
															>{{
																	driver.fullname
															}}</option>
													</select>
													<label for="time"
														class="block text-sm font-medium text-gray-700">Monto($):</label>
													<input type="text" name="price" id="price" v-model="form.price_unit_new"
														class="mt-1 w-full focus:ring-indigo-500 focus:border-indigo-500 block shadow-sm sm:text-sm border-gray-300 rounded-md " />
												</div>

												<div class="col-span-2">
													<label for="time"
														class="block text-sm font-medium text-gray-700">Hora
														Inicio:</label>
													<Datepicker id="time" class="w-full mt-1" v-model="form.time"
														:startTime="startTime" timePicker>
													</Datepicker>

													<label for="time"
														class="block text-sm font-medium text-gray-700">Cliente:</label>
													<select disabled v-model="form.client_id" id="client" name="client" :disabled="newClient ? '' : disabled" :class="newClient ? 'bg-gray-50' : ''"
														class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm "
														@change="setClient(form.client_id)">
														<option value="" selected>Selecciones un Cliente</option>
														<option v-for="client in clients" :key="client.id"
															:value="client.id"
															:bind:select="client.id == form.client_id">
															{{ client.fullname }}
														</option>
													</select>
													<label for="address" v-if="form.google_address"
														class="block text-sm font-medium text-ray-700">
														<b>Dirección: </b>{{ this.form.google_address}}</label>
													<label for="price" v-if="form.unit_price"
														class="block text-sm font-medium text-ray-700">
														<b>Monto unitario:</b> $ {{ form.unit_price.toFixed(2) }}</label>
													<label for="price" v-if="form.total_price"
														class="block text-sm font-medium text-ray-700">
														<b>Monto total:</b> $ {{ form.total_price.toFixed(2) }}</label>
													<label for="price" v-if="form.order_status"
														class="block text-sm font-medium text-ray-700">
														<b>Estado:</b> {{ form.order_status }}</label>
												</div>

											</div> 
											<div class="grid grid-cols-4 gap-2 content-start">
												<button @click="closeModal"
													class="px-6 py-2 mt-4 text-blue-800 border border-blue-600 rounded">
													Cancelar
												</button>
												<button class="px-6 py-2 mt-4 text-blue-100 bg-blue-600 rounded"
													@click="UpdateOrder()">
													Guardar
												</button>
											</div>
										</section>
										<section aria-labelledby="options-heading" class="mt-10" v-else>
											<div class="grid grid-cols-4 gap-2 content-start">
												<button @click="closeModal"
													class="px-6 py-2 mt-4 text-blue-800 border border-blue-600 rounded">
													No
												</button>
												<button class="px-6 py-2 mt-4 text-blue-100 bg-blue-600 rounded"
													@click="UpdateOrder()">
													Si
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

import { XIcon, UserIcon, CalendarIcon, LocationMarkerIcon, TruckIcon, PhoneIcon, OfficeBuildingIcon, ChevronDownIcon, SaveIcon, ChevronUpIcon, SearchIcon, MapIcon, ClipboardListIcon, PlusCircleIcon } from '@heroicons/vue/solid'

import Icons from '@/Layouts/Components/Icons.vue'

import {
	Menu, MenuButton, MenuItem, MenuItems, Dialog,
	DialogPanel,
	RadioGroup,
	RadioGroupLabel,
	RadioGroupOption,
	TransitionChild,
	TransitionRoot,
} from '@headlessui/vue'
import moment from 'moment'
import VueGoogleAutocomplete from "vue-google-autocomplete"

export default {

	props: {
		
	},

	components: {
		Datepicker,
		TruckIcon,
		PhoneIcon,
		OfficeBuildingIcon,
		CalendarIcon,
		LocationMarkerIcon,
		UserIcon,
		SaveIcon,
		Icons,
		Menu,
		MenuButton,
		MenuItem,
		MenuItems,
		ChevronDownIcon,
		moment,
		Dialog,
		DialogPanel,
		RadioGroup,
		RadioGroupLabel,
		RadioGroupOption,
		TransitionChild,
		TransitionRoot,
		XIcon,
		ChevronUpIcon, SearchIcon, MapIcon, ClipboardListIcon, PlusCircleIcon,
		VueGoogleAutocomplete
	},
	data() {
		return {
			newClient: false,
			btnTextNewClient: "Nuevo Cliente",
			form_google: "",
			form: {},
			showCancelar: false,
			modal_title: 'Pedido'
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
		formatHora() {
			if(this.form.time){
				this.form.time = { hours: moment(this.form.time, 'HH:mm:ss').format('H'), minutes: moment(this.form.time, 'HH:mm:ss').format('mm') };
			}
		},
		getOrder(order, action, title = 'Pedido'){
			
			this.showCancelar = false
			
			this.modal_title = title
			this.form.order_id = order.order.id
			this.form.service_id = order.service.id
			this.form.client_id = order.client.id
			this.form.total_price = order.order.total_price
			this.form.unit_price = order.order.unit_price
			this.form.google_address = order.client.address ? order.client.address.google_address : '-'
			this.form.price_unit_new = order.order.unit_price
			this.form.order_status = order.order_status.status
			this.form.action = action

			if(action == 1){
				this.form.driver_id = order.service.driver ? order.service.driver.id : ''
				this.form.date = order.service.date ? order.service.date : ''
				this.form.time = order.service.time ? order.service.time : ''
				this.form.google_address = order.client.address ? order.client.address.google_address : '-'
				this.form.date = new Date(this.form.date + "T00:00:00.000-03:00")
				this.formatHora()
			}else if(action == 2 || action == 3){
				this.form.driver_id = ''
				this.form.date = ''
				this.form.time = ''
			}else if(action == 4){
				this.showCancelar = true
			}else if(action == 5){
				this.showCancelar = true
			}
		},
		getAddressData: function (addressData, placeResultData, id) {
            this.form.google_address_new = placeResultData['formatted_address']
            this.form.google_area1_new = addressData['administrative_area_level_1']
            this.form.google_postal_code_new = addressData['postal_code']
            this.form.google_latitude_new = addressData['latitude']
            this.form.google_longitude_new = addressData['longitude']
        },
		async getClients() {
			const get = `${route('clients.list')}`
			const response = await fetch(get, { method: 'GET' })
			const r = await response.json()
			this.clients = r.data
		},
		async getDrivers() {
			const get = `${route('drivers.list')}`
			const response = await fetch(get, { method: 'GET' })
			const r = await response.json()
			this.drivers = r.data
		},
		cleanNewUser(){
			this.form.google_address_new = ''
			this.form.google_area1_new = ''
			this.form.google_postal_code_new = ''
			this.form.google_latitude_new = ''
			this.form.google_longitude_new = ''
			this.form.fullname_new = ''
		},
		cleanUser(){
			this.form.client_id = ''
			this.form.driver = ''
			this.form.date = ''
			this.form.time = ''
			this.newClient = false
		},	
		setClient(id) {
			let client = this.clients.find((c) => { return c.id == id })

			this.form.price = client.price
			this.form.google_address = client.address[0].google_address
		},
		UpdateOrder() {
			let rt = route('orders.updatedashboard');

			axios.post(rt, {
				form: this.form,
			}).then(response => {
				if (response.status == 200) {
					this.form = {};
					this.message = response.data.message
					this.showToast = true
				} else {
					this.labelType = "danger"
					this.toastMessage = response.data.message
				}
				this.newClient = false
				this.$emit('responseUpdateOrder', response)
				this.closeModal()
			})
		},
		closeModal(){
			this.$emit('viewUpdateOrder', false)
		}

	},
	watch: {
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
		this.getDrivers(),
		this.getClients(),
		this.form.newClient = false
	}

}
</script>