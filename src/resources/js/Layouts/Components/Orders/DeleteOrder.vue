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

									<div class="sm:col-span-8 lg:col-span-8 flex">
										<TrashIcon class="h-7 w-7 text-red-500" aria-hidden="true" /> 
										<h1 class="text-2xl font-bold text-gray-900">- ¿Desea Eliminar el pedido N° {{idOrder}}?</h1>
									</div>
									<div class="sm:col-span-8 lg:col-span-8 flex mt-2">
										<KeyIcon class="h-6 w-6 text-gray-500" aria-hidden="true" /> 
										<h3 class="text-md font-bold text-gray-900"> - Código de confirmación: {{ codeConfirm }}</h3>
									</div>

									<div class="sm:col-span-8 lg:col-span-8 flex mt-2">
										<input type="text" name="code" id="code" placeholder="Ingrese el código de confirmación" v-model="code"
										class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
										
									</div>
									
									<div class="sm:col-span-8 lg:col-span-8 flex mt-2">
										<h3 class="text-xs font-bold text-red-900"> {{this.msgConfirm}}</h3>
									</div>
 									<div class="sm:col-span-8 lg:col-span-8 flex">
										<section aria-labelledby="options-heading" class="mt-10">
											<div class="grid grid-cols-4 gap-2 content-justify">
												<button @click="closeModal"
													class="px-6 py-2 mt-4 text-black-800 border border-red-500 rounded hover:bg-red-100 hover:text-red-500">
													No
												</button>
												<button class="px-6 py-2 mt-4 text-red-100 bg-red-500 rounded  hover:bg-red-700"
													@click="UpdateOrder(false)">
													Si, eliminar PEDIDO
												</button>
												<button class="px-6 py-2 mt-4 text-red-100 bg-red-500 rounded  hover:bg-red-700"
													@click="UpdateOrder(true)">
													Si, eliminar PEDIDO y CLIENTE
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

import { KeyIcon, XIcon, UserIcon, CalendarIcon, LocationMarkerIcon, TruckIcon, PhoneIcon, OfficeBuildingIcon, ChevronDownIcon, SaveIcon, ChevronUpIcon, SearchIcon, MapIcon, ClipboardListIcon, PlusCircleIcon, TrashIcon} from '@heroicons/vue/solid'

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
		idOrder: String
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
		TrashIcon,
		ChevronUpIcon, SearchIcon, MapIcon, ClipboardListIcon, PlusCircleIcon,KeyIcon,
		VueGoogleAutocomplete
	},
	data() {
		return {
			data: {},
			codeConfirm: '',
			isConfirmed: false,
			code: '',
			msgConfirm: ''
		}
	},
	methods: {
		UpdateOrder(deleteUser) {
			this.confirmAction();
			if(this.isConfirmed){
				this.data.idOrder = this.idOrder
				this.data.deleteUser = deleteUser
				this.$emit('responseDeleteOrder', this.data)
			}else{
				this.msgConfirm = 'ERROR al momento de validar el código'
			}
		},
		closeModal(){
			this.$emit('viewDeleteOrder', false)
		},
		generateConfirmationCode() {
			this.codeConfirm = Math.floor(1000 + Math.random() * 9000).toString();
			this.isConfirmed = false;
		},
		confirmAction() {
			this.isConfirmed = this.code === this.codeConfirm;
		},
	},
	created() {
		this.generateConfirmationCode()
	},

}
</script>