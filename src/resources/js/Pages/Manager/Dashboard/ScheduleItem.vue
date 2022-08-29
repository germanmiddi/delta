<template>
	<div class="px-4 py-4 sm:px-6">
		<div class="grid grid-cols-8 gap-2">
			<div class="col-span-1">
				<p class="text-green-900 text-5xl font-medium text-black-600 truncate">
					<b>{{  this.form.service.order_id  }}</b>
				</p>
			</div>
			<div class="col-span-5">
				<p class="text-xl font-medium text-black-600 truncate">
					<b>{{  this.form.client.address.google_address  }}</b>
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
								<a href="#" @click="form.action = 2"
									:class="[(active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm'), (btnEntregado ? '' : 'pointer-events-none text-gray-400')]">Pasar
									a Entregado</a>
								</MenuItem>
								<MenuItem v-slot="{ active }">
								<a href="#"
									:class="[(active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm'), (btnRetirado ? '' : 'pointer-events-none text-gray-400')]">Pasar
									a Retirado</a>
								</MenuItem>
							</div>
							<div class="py-1">
								<MenuItem v-slot="{ active }">
								<a href="#"
									:class="[(active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm'), (btnCambio ? '' : 'pointer-events-none text-gray-400')]">Crear
									Cambio</a>
								</MenuItem>
								<MenuItem v-slot="{ active }">
								<a href="#"
									:class="[(active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm'), (btnRetiro ? '' : 'pointer-events-none text-gray-400')]">Crear
									Retiro</a>
								</MenuItem>
							</div>
							<div class="py-1">
								<MenuItem v-slot="{ active }">
								<a href="#"
									:class="[(active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm'), (btnCancelar ? '' : 'pointer-events-none text-gray-400')]">Cancelado</a>
								</MenuItem>
							</div>
						</MenuItems>
					</transition>
				</Menu>

			</div>

			<!-- SECCION DE DATOS...  -->

			<div class="col-start-2 col-span-2">
				<p class="flex items-center text-sm text-gray-500 py-1">
					<OfficeBuildingIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
					{{  form.client.company.razon_social  }}
				</p>
				<p class="flex items-center text-sm text-gray-500 py-1">
					<UserIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
					{{  form.client.fullname  }}
				</p>
				<p class="flex items-center text-sm text-gray-500 py-1">
					<PhoneIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
					{{  form.client.cellphone  }}
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
						class="px-2 inline-flex text-sm leading-5 font-semibold rounded-lg bg-slate-200 text-slate-800">
						{{  this.form.order_status.status  }}</p>

					<p v-else-if="form.order_status.status == 'ENTREGADO' || form.order_status.status == 'EN ENVIO' || form.order_status.status == 'EN RETIRO'"
						class="px-2 inline-flex text-sm leading-5 font-semibold rounded-lg bg-green-200 text-green-800">
						{{  this.form.order_status.status  }}</p>

					<p v-else
						class="px-2 inline-flex text-sm leading-5 font-semibold rounded-lg bg-red-200 text-red-800">
						{{  this.form.order_status.status  }}</p>

					<p><b>Total:</b> $ {{  this.form.order_total_price.toFixed(2)  }}</p>
					<p><b>Forma de Pago:</b> Efectivo</p>

					<p v-if="form.service.driver"><b>Cobranza:</b> {{ this.form.service.driver.fullname }}</p>
					<p v-else><b>Cobranza:</b> - </p>
				</div>
			</div>
		</div>
	</div>
</template>

<script>

import { defineComponent, ref } from 'vue'
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

import { UserIcon, CalendarIcon, LocationMarkerIcon, TruckIcon, PhoneIcon, OfficeBuildingIcon, ChevronDownIcon, SaveIcon } from '@heroicons/vue/solid'

import Icons from '@/Layouts/Components/Icons.vue'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import moment from 'moment'

export default {

	props: {
		order: Object,
		drivers: Object
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
		Datepicker,
		Icons,
		Menu,
		MenuButton,
		MenuItem,
		MenuItems,
		ChevronDownIcon,
		moment
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
			btnCancelar: false
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

		return {
			format,
			startTime
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
                form : this.form,
            }).then(response => {
				console.log(response)
                if (response.status == 200) {
					this.$emit('refresh')
                } else {
                    console.log('Error');
                }
            })
		},
		updateBtnOptions() {
			switch (this.form.order_status.id) {
				case 1: // AGENDADO
					this.btnEditar = true
					this.btnCancelar = true
					break;
				case 3: //EN ENVIO -> CON CHOFER
					if(this.form.service.status_id == 1) {
						
						this.btnEditar = true
						this.btnEntregado = true
						this.btnCancelar = true
					} else if (this.form.service.status_id == 2){
						this.btnCambio = true
						this.btnRetiro = true
					}
				break; 
				case 4: //ENTREGADO

					break;
				default:
					break;
			}
		}
	},
	created() {
		this.form = this.order
		this.formatHora()
		this.updateBtnOptions()
	}

}
</script>