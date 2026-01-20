<template>
	<div class="min-h-full">

		<Toast :toast="this.toastMessage" :type="this.labelType" @clear="clearMessage"></Toast>
		<main class="py-10" v-show="view">
			<div class="mt-8 max-w-3xl mx-auto  gap-6 sm:px-6 lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-3">
				<div class="space-y-6 lg:col-start-1 lg:col-span-2">
					<div class="bg-white shadow overflow-hidden sm:rounded-md">
						<!-- Leyenda de colores -->
						<div class="flex items-center justify-center space-x-6 py-3 bg-gray-50 border-b">
							<div class="flex items-center">
								<span class="w-4 h-4 rounded-full bg-blue-500 mr-2"></span>
								<span class="text-sm text-gray-700">Envío</span>
							</div>
							<div class="flex items-center">
								<span class="w-4 h-4 rounded-full bg-yellow-400 mr-2"></span>
								<span class="text-sm text-gray-700">Cambio</span>
							</div>
							<div class="flex items-center">
								<span class="w-4 h-4 rounded-full bg-red-500 mr-2"></span>
								<span class="text-sm text-gray-700">Retiro</span>
							</div>
						</div>
						<GoogleMapCluster :form_map="form_google">
						</GoogleMapCluster>
					</div>
				</div>
			</div>
		</main>
	</div>
</template>


<script>

import { defineComponent, ref } from 'vue'
import GoogleMapCluster from '../../../Layouts/Components/GoogleMapCluster.vue'

export default {

	props: {
		view: Boolean,
	},

	components: {
		GoogleMapCluster,
	},
	data() {
		return {
			form_google: "",
		}
	},
	setup() {
	},
	methods: {
		getFilterMap(orders) {
			this.form_google = orders
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
	}
}
</script>
