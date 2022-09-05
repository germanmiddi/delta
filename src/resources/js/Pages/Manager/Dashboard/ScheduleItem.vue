<template>
	<div class="px-4 py-4 sm:px-6">
		<div class="grid grid-cols-8 gap-2">
			<div class="col-span-1">
				<p class="text-green-900 text-5xl font-medium text-black-600 truncate">
					<b>{{ this.form.service.order_id }}</b>
				</p>
			</div>
			<div class="col-span-5">
				<p v-if="this.form.client.address.google_address" class="text-xl font-medium text-black-600 truncate">
					<b>{{ this.form.client.address.google_address }}</b>
				</p>
				<p v-else class="text-xl font-medium text-black-600 truncate">
					<b>--</b>
				</p>
			</div>
			<div class="col-span-2 px-5">
				<div v-show="btnOption">
					<a class="btn-blue h-7" @click="updateOrder">
						<SaveIcon class="mr-2 h-5 w-5" />
						Guardar
					</a>
				</div>

				<Menu as="div" class="inline-node relative" v-show="!btnOption">
					<div>
						<MenuButton class="btn-blue h-7">
							Opciones
							<ChevronDownIcon class="mr-1 ml-2 h-5 w-5" aria-hidden="true" />
						</MenuButton>
					</div>
					<transition enter-active-class="transition ease-out duration-100"
						enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
						leave-active-class="transition ease-in duration-75"
						leave-from-class="transform opacity-100 scale-100"
						leave-to-class="transform opacity-0 scale-95">
						<MenuItems
							class="origin-top-left absolute z-50 right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none">
							<div class="py-1">
								<MenuItem v-slot="{ active }">
								<a href="#" @click="btnOption = !btnOption, form.action = 1"
									:class="[(active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm'), (btnEditar ? '' : 'pointer-events-none text-gray-400')]">Editar</a>
								</MenuItem>
							</div>
							<div class="py-1">
								<MenuItem v-slot="{ active }">
								<a href="#" @click="form.action = 2, updateOrder()"
									:class="[(active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm'), (btnEntregado ? '' : 'pointer-events-none text-gray-400')]">Pasar
									a Entregado</a>
								</MenuItem>
								<MenuItem v-slot="{ active }">
								<a href="#" @click="form.action = 3, updateOrder()"
									:class="[(active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm'), (btnRetirado ? '' : 'pointer-events-none text-gray-400')]">Pasar
									a Retirado</a>
								</MenuItem>
							</div>
							<div class="py-1">
								<MenuItem v-slot="{ active }">
								<a href="#" @click="titleModal = 'Generar Cambio', form.action = 4, open = true"
									:class="[(active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm'), (btnCambio ? '' : 'pointer-events-none text-gray-400')]">Crear
									Cambio</a>
								</MenuItem>
								<MenuItem v-slot="{ active }">
								<a href="#" @click="titleModal = 'Generar Retiro', form.action = 5, open = true"
									:class="[(active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm'), (btnRetiro ? '' : 'pointer-events-none text-gray-400')]">Crear
									Retiro</a>
								</MenuItem>
							</div>
							<div class="py-1">
								<MenuItem v-slot="{ active }">
								<a href="#" @click="form.action = 6, updateOrder()"
									:class="[(active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm'), (btnCancelar ? '' : 'pointer-events-none text-gray-400')]">Cancelado</a>
								</MenuItem>
							</div>
						</MenuItems>
					</transition>
				</Menu>

			</div>

			<!-- SECCION DE DATOS...  -->

			<div class="col-start-2 col-span-2">
				<p v-if="form.client.company" class="flex items-center text-sm text-gray-500 py-1">
					<OfficeBuildingIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
					{{ form.client.company.razon_social }}
				</p>
				<p v-else class="flex items-center text-sm text-gray-500 py-1">
					<OfficeBuildingIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
					-
				</p>


				<p class="flex items-center text-sm text-gray-500 py-1">
					<UserIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
					{{ form.client.fullname }}
				</p>
				<p v-if="form.client.cellphone" class="flex items-center text-sm text-gray-500 py-1">
					<PhoneIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
					{{ form.client.cellphone }}
				</p>
				<p v-else class="flex items-center text-sm text-gray-500 py-1">
					<PhoneIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
					-
				</p>
			</div>
			<div class="col-span-3 ">
				<Datepicker id="date" class="w-full mt-1" v-model="form.service.date" :enableTimePicker="false"
					:monthChangeOnScroll="false" :minDate="new Date(new Date().setDate(new Date().getDate() - 1))"
					autoApply :format="format" :disabled="!btnOption ? '' : disabled"></Datepicker>

				<Datepicker id="time" class="w-full mt-1" :startTime="startTime" timePicker v-model="form.service.time"
					:disabled="!btnOption ? '' : disabled">
				</Datepicker>

				<select v-model="form.service.driver_id" id="driver" name="driver"
					:disabled="!btnOption ? '' : disabled" :class="[btnOption ? '' : 'bg-gray-100']"
					class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm ">
					<option disabled value="" selected>Selecciones un Chofer</option>
					<option v-for="driver in drivers" :key="driver.id" :value="driver.id"
						:bind:select="driver.id == form.service.driver_id">{{
								driver.fullname
						}}</option>
				</select>
			</div>
			<div class="col-span-2 px-5">
				<div class=" text-xs text-slate-400">
					<p v-if="form.order_status.status == 'AGENDADO' || form.order_status.status == 'ENTREGADO'"
						class="px-2 mt-2 inline-flex text-sm leading-5 font-semibold rounded-lg bg-slate-200 text-slate-800">
						{{ this.form.order_status.status }}</p>

					<p v-else-if="form.order_status.status == 'ENTREGADO' || form.order_status.status == 'EN ENVIO' || form.order_status.status == 'EN RETIRO'"
						class="px-2 mt-2 inline-flex text-sm leading-5 font-semibold rounded-lg bg-green-200 text-green-800">
						{{ this.form.order_status.status }}</p>

					<p v-else
						class="px-2 mt-2 inline-flex text-sm leading-5 font-semibold rounded-lg bg-red-200 text-red-800">
						{{ this.form.order_status.status }}</p>




					<p v-if="form.order_total_price || form.order_total_price == 0" class="mt-2"><b>Total:</b> $ {{
							this.form.order_total_price.toFixed(2)
					}} </p>
					<p v-else class="mt-2"><b>Total:</b> $ -</p>

					<p><b>Forma de Pago:</b> Efectivo</p>

					<p v-if="form.service.driver"><b>Cobranza:</b> {{ this.form.service.driver.fullname }}</p>
					<p v-else><b>Cobranza:</b> - </p>

					<p v-if="form.service.type.type == 'ENVIO' || form.service.type.type == 'CAMBIO'"
						class="px-2 mt-2 inline-flex text-sm leading-5 font-semibold rounded-lg bg-green-200 text-green-800">
						{{ this.form.service.type.type }}</p>

					<p v-else
						class="px-2 mt-2 inline-flex text-sm leading-5 font-semibold rounded-lg bg-red-200 text-red-800">
						{{ this.form.service.type.type }}</p>
				</div>
			</div>
		</div>
	</div>

	<TransitionRoot as="template" :show="open">
		<Dialog as="div" class="relative z-10" @close="open = false">
			<TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
				leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
				<div class="fixed inset-0 hidden bg-gray-500 bg-opacity-75 transition-opacity md:block" />
			</TransitionChild>

			<div class="fixed inset-0 z-10 overflow-y-auto rounded-md">
				<div class="flex items-stretch justify-center text-center md:items-center md:px-2 lg:px-4 rounded">
					<TransitionChild as="template" enter="ease-out duration-300"
						enter-from="opacity-0 translate-y-4 md:translate-y-0 md:scale-95"
						enter-to="opacity-100 translate-y-0 md:scale-100" leave="ease-in duration-200"
						leave-from="opacity-100 translate-y-0 md:scale-100"
						leave-to="opacity-0 translate-y-4 md:translate-y-0 md:scale-95">
						<DialogPanel
							class="flex w-full transform text-left text-base transition md:my-8 md:max-w-2xl md:px-4 lg:max-w-4xl rounded">
							<div
								class="relative flex w-full items-center overflow-hidden bg-white px-4 pt-14 pb-8 shadow-2xl sm:px-6 sm:pt-8 md:p-6 lg:p-8">
								<button type="button"
									class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 sm:top-8 sm:right-6 md:top-6 md:right-6 lg:top-8 lg:right-8"
									@click="open = false">
									<span class="sr-only">Close</span>
									<XIcon class="h-6 w-6 " aria-hidden="true" />
								</button>

								<div
									class="grid w-full grid-cols-1 items-start gap-y-8 gap-x-2 sm:grid-cols-8 lg:gap-2">

									<div class="sm:col-span-8 lg:col-span-8">
										<h1 class="text-2xl font-bold text-gray-900">{{ this.titleModal }}</h1>
									</div>
									<div class="sm:col-span-8 lg:col-span-8">
										<section aria-labelledby="options-heading" class="mt-10">
											<label for="time" class="block text-sm font-medium text-gray-700">Fecha
												Inicio:</label>
											<Datepicker id="date" class="w-full mt-1" v-model="form.service.date_new"
												:enableTimePicker="false" :monthChangeOnScroll="false"
												:minDate="new Date(new Date().setDate(new Date().getDate() - 1))"
												autoApply :format="format"></Datepicker>

											<label for="time" class="block text-sm font-medium text-gray-700">Hora
												Inicio:</label>
											<Datepicker id="time" class="w-full mt-1" v-model="form.service.time_new"
												:startTime="startTime" timePicker>
											</Datepicker>
											<label for="time" class="block text-sm font-medium text-gray-700">Hora
												Chofer:</label>
											<select v-model="form.service.driver_id_new" id="driver" name="driver"
												class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm ">
												<option value="" selected>Selecciones un Chofer</option>
												<option v-for="driver in drivers" :key="driver.id" :value="driver.id">{{
														driver.fullname
												}}</option>
											</select>
											<button @click="open = false"
												class="px-6 py-2 mt-4 text-blue-800 border border-blue-600 rounded">
												Cancelar
											</button>
											<button class="px-6 py-2 ml-2 text-blue-100 bg-blue-600 rounded"
												@click="open = false, updateOrder()">
												Guardar
											</button>

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

import { XIcon, UserIcon, CalendarIcon, LocationMarkerIcon, TruckIcon, PhoneIcon, OfficeBuildingIcon, ChevronDownIcon, SaveIcon } from '@heroicons/vue/solid'

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

export default {

	props: {
		order: Object
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
		XIcon
	},
	data() {
		return {
			orders: "",
			form: {},
			btnOption: false,
			btnEditar: false,
			btnEntregado: false,
			btnRetirado: false,
			btnCambio: false,
			btnRetiro: false,
			btnCancelar: false,
			titleModal: ""
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
			this.form.service.time = { hours: moment(this.form.service.time, 'HH:mm:ss').format('H'), minutes: moment(this.form.service.time, 'HH:mm:ss').format('mm') };
		},
		updateOrder() {
			this.btnOption = false

			let rt = route('orders.updatedashboard');

			axios.post(rt, {
				form: this.form,
			}).then(response => {
				this.$emit('refresh', [response.status, response.data.message])
			})
		},
		updateBtnOptions() {
			switch (this.form.order_status.id) {
				case 1: // AGENDADO
					this.btnEditar = true
					this.btnCancelar = true
					break;
				case 3: //EN ENVIO -> CON CHOFER
					if (this.form.service.status_id == 1) {
						this.btnEditar = true
						this.btnEntregado = true
						this.btnCancelar = true
					} else if (this.form.service.status_id == 2) {
						this.btnCambio = true
						this.btnRetiro = true
					}
					break;
				case 4: //ENTREGADO
					if (this.form.service.status_id == 3) {
						this.btnCambio = true
						this.btnRetiro = true
					} else if (this.form.service.status_id == 1) {
						this.btnEditar = true
						this.btnCancelar = true
						if (this.form.service.driver_id) {
							if (this.form.service.type_id == 2) {
								this.btnEntregado = true
							} else {
								this.btnRetirado = true
							}
						}
					}
					break;
				default:
					break;
			}
		}
	},
	created() {
		this.form = this.order
		this.form.service.date = new Date(this.order.service.date + "T00:00:00.000-03:00")
		if (this.form.service.time) {
			this.formatHora();
		}
		this.updateBtnOptions()
	}

}
</script>