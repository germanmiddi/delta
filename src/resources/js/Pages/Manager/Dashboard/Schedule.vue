<template>
	<div class="min-h-full">
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
					<div class="bg-white px-3 py-5 shadow sm:rounded-lg">
						<Datepicker id="filterDate" class="w-full mt-1" v-model="filterDate" inline autoApply
							:enableTimePicker="false" :monthChangeOnScroll="false"></Datepicker>

						<div class="mt-6 flex flex-col justify-stretch">
							<a type="button" :href="route('orders.create')"
								class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Nuevo
								Pedido</a>
						</div>
					</div>
				</section>
			</div>
		</main>
	</div>
</template>

<script>

import { defineComponent, ref } from 'vue'
import GoogleMapCluster from '../../../Layouts/Components/GoogleMapCluster.vue'
import ScheduleItem from '../../Manager/Dashboard/ScheduleItem.vue'
import { UserIcon, CalendarIcon, LocationMarkerIcon, TruckIcon, } from '@heroicons/vue/solid'
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

export default {

	props: {
		view: Boolean
	},

	components: {
		Datepicker,
		TruckIcon,
		CalendarIcon,
		LocationMarkerIcon,
		UserIcon,
		GoogleMapCluster,
		ScheduleItem
	},

	setup() {
	},

	data() {
		return {
			filterDate: new Date(),
			orders: "",
			drivers: "",
			form_google: "",
			data: [],
			showMap: false,
		}
	},
	methods: {
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
		refreshOrders() {
			this.getOrders();
		},
	},
	watch: {
		filterDate: function () {
			this.getOrders()
		},
	},
	created() {
		this.getOrders(),
		this.getOrdersMap(),
		this.getDrivers()
	}
}
</script>