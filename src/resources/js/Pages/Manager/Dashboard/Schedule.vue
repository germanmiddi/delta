
<template>
	<div class="min-h-full">

		<main class="py-10">
			<!-- Page header -->
			<div class="max-w-3xl mx-auto px-4 sm:px-6 md:flex md:items-center md:justify-between md:space-x-5 lg:max-w-7xl lg:px-8">
				<div class="flex items-center space-x-5">
						<h1 class="text-2xl font-bold text-gray-900">Proximos pedidos</h1>
				</div>

			</div>

			<div class="mt-8 max-w-3xl mx-auto grid grid-cols-1 gap-6 sm:px-6 lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-3">
				<div class="space-y-6 lg:col-start-1 lg:col-span-2">
				<div class="bg-white shadow overflow-hidden sm:rounded-md">
						<ul role="list" class="divide-y divide-gray-200">
						<li v-for="position in positions" :key="position.id">
								<a href="#" class="block hover:bg-gray-50">
								<div class="px-4 py-4 sm:px-6">
										<div class="flex items-center justify-between">
										<p class="text-sm font-medium text-indigo-600 truncate">
												{{ position.title }}
										</p>
										<div class="ml-2 flex-shrink-0 flex">
												<p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
												{{ position.type }}
												</p>
										</div>
										</div>
										<div class="mt-2 sm:flex sm:justify-between">
										<div class="sm:flex">
												<p class="flex items-center text-sm text-gray-500">
												<UsersIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
												{{ position.department }}
												</p>
												<p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
												<LocationMarkerIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
												{{ position.location }}
												</p>
										</div>
										<div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
												<CalendarIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
												<p>
												Closing on
												{{ ' ' }}
												<time :datetime="position.closeDate">{{ position.closeDateFull }}</time>
												</p>
										</div>
										</div>
								</div>
								</a>
						</li>
						</ul>
				</div>          
				</div>

				<section aria-labelledby="timeline-title" class="lg:col-start-3 lg:col-span-1">
					<div class="bg-white px-3 py-5 shadow sm:rounded-lg">
						<Datepicker id="filterDate" class="w-full mt-1" v-model="filterDate" 
									inline autoApply
									:enableTimePicker="false"
									:monthChangeOnScroll="false" 
									:format="format"></Datepicker>
						
						<div class="mt-6 flex flex-col justify-stretch">
							<button type="button" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Nuevo Pedido</button>
						</div>
					</div>
				</section>
			</div>
		</main>
	</div>
</template>

<script>

	import Datepicker from '@vuepic/vue-datepicker';
	import '@vuepic/vue-datepicker/dist/main.css'

	import {HomeIcon,
			PaperClipIcon,
			QuestionMarkCircleIcon,
			SearchIcon,
			ThumbUpIcon,
			UserIcon,
			ChevronLeftIcon,
			ChevronRightIcon} from '@heroicons/vue/solid'

	import { BellIcon, MenuIcon, XIcon } from '@heroicons/vue/outline'
	import { CalendarIcon, LocationMarkerIcon, UsersIcon } from '@heroicons/vue/solid'


	const positions = [
		{
			id: 1,
			title: 'Back End Developer',
			type: 'Full-time',
			location: 'Remote',
			department: 'Engineering',
			closeDate: '2020-01-07',
			closeDateFull: 'January 7, 2020',
		},
		{
			id: 2,
			title: 'Front End Developer',
			type: 'Full-time',
			location: 'Remote',
			department: 'Engineering',
			closeDate: '2020-01-07',
			closeDateFull: 'January 7, 2020',
		},
		{
			id: 3,
			title: 'User Interface Designer',
			type: 'Full-time',
			location: 'Remote',
			department: 'Design',
			closeDate: '2020-01-14',
			closeDateFull: 'January 14, 2020',
		},
	]

	export default {

		components: {
			Datepicker,
			BellIcon,
			HomeIcon,
			MenuIcon,
			PaperClipIcon,
			QuestionMarkCircleIcon,
			SearchIcon,
			XIcon,
			CalendarIcon,
			LocationMarkerIcon,
			UsersIcon,    
					ChevronLeftIcon,
			ChevronRightIcon,
		},

		setup() {
			const format = (date) => {
				const day = date.getDate();
				const month = date.getMonth() + 1;
				const year = date.getFullYear();

				return `${day}/${month}/${year}`;
			}
			return {
				positions,
				format,
			}
		},
		
		data(){
			return{
				filterDate:"",
				orders: ""
			}
		},

		methods:{
			
			async getOrders(){
				const filter = `date=${this.filterDate}`
				const get = `${route('orders.listdashboard')}?${filter}`
				
				const response = await fetch(get, {method:'GET'})
				this.orders = await response.json() 
			}

		},
		watch:{
			filterDate:function(){
				this.getOrders()
			}
		},
		created(){
			this.getOrders()
		
		}

	}
</script>