<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
            <div>
                <a class="btn-blue" @click="showMap()">
                    {{ btnTextMap }}
                </a>
            </div>
        </template>

        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 card">
                <ul class="top-filter">
                    <li class="top-filter-item">Todos <span class="top-filter-number">50</span> </li>
                    <li class="top-filter-item top-filter-item-active">Enviados <span class="top-filter-number">10</span></li>
                    <li class="top-filter-item">Enviados(Retiro Pendiente) <span class="top-filter-number">10</span></li>
                    <li class="top-filter-item">Retiros <span class="top-filter-number">10</span></li>
                    <li class="top-filter-item">Finalizados <span class="top-filter-number">10</span></li>
                </ul> 
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 card py-10 my-5">
                <div class="card-header">
                    <div class="card-title">Próximos Pedidos</div> 
                    <div class="card-buttons">
                        <button class="button-w-icon">Filtros <ChevronDownIcon class="w-5" /></button>
                    </div>    
                </div>
                <div class="card-filter">
                    <div class="filter-input-group">
                        <label>Direccón: </label>
                        <input />   
                    </div>    
                    <div class="filter-input-group">
                        <!-- <label>Direccón: </label> -->
                        <input placeholder="Cliente"/>   
                    </div>    
                    <div class="filter-input-group">
                        <!-- <label>Direccón: </label> -->
                        <input />   
                    </div>    
                    <div class="filter-input-group">
                        <!-- <label>Direccón: </label> -->
                        <input />   
                    </div>
                    <div class="filter-button">
                        <button class="button"><SearchIcon class="w-5 text-gray-700" /></button>
                    </div>    

                </div>     

                <div class="card-body">
                    <div class="bg-white overflow-hidden sm:rounded-lg border border-gray-200">
                        <table class="table w-full whitespace-nowrap">
                            <tr class="table-header">
                                <th>ID</th> 
                                <th>Fecha</th> 
                                <th>Dirección</th> 
                                <th>Cliente</th> 
                                <th>Empresa</th> 
                                <th>Chofer</th> 
                                <th></th> 
                            </tr>
                            <tbody>
                                <tr class="table-row">
                                    <td>10</td>
                                    <td>03/09/2022</td>
                                    <td>Av. Maipú 2034, B1636AAN Olivos</td>
                                    <td>John Doe</td>
                                    <td>Contratista Pilar</td>
                                    <td>Martin Lugo</td>
                                    <td><button><ChevronDownIcon class="w-4 text-gray-700" /></button></td>
                                </tr>
                                <tr class="table-row">
                                    <td>11</td>
                                    <td>03/09/2022</td>
                                    <td>Pedro Goyena 2820, B1636BXR Olivos</td>
                                    <td>John Doe</td>
                                    <td>Contratista Pilar</td>
                                    <td>Martin Lugo</td>
                                    <td><button><ChevronDownIcon class="w-4 text-gray-700" /></button></td>
                                </tr>
                                <tr class="table-row">
                                    <td>12</td>
                                    <td>03/09/2022</td>
                                    <td>Diego Palma 2847, B1642FLE San Isidro</td>
                                    <td>John Doe</td>
                                    <td>Contratista Pilar</td>
                                    <td>Martin Lugo</td>
                                    <td><button><ChevronDownIcon class="w-4 text-gray-700" /></button></td>
                                </tr>
                            </tbody>  
                        </table>                    
                        <div class="">
                            <div class="table-header">

                            </div>
                            <!-- <div class="table-row"> -->
                            <div class="w-full">
                                <div class="row-main flex justify-between ">
                                    <div>12</div>
                                    <div>03/09/2022</div>
                                    <div>Diego Palma 2847, B1642FLE San Isidro</div>
                                    <div>John Doe</div>
                                    <div>Contratista Pilar</div>
                                    <div>Martin Lugo</div>
                                    <div><button><ChevronDownIcon class="w-4 text-gray-700" /></button></div>
                                </div>
                                <div class="row-detail">

                                </div>
                            </div>
                            
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <schedule :view="showFilter" />
            </div>
        </div>
    </AppLayout>
</template>

<script>

import { defineComponent } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue';
import Schedule from './Schedule.vue'
import { ChevronDownIcon, SearchIcon } from '@heroicons/vue/solid'

export default defineComponent({
    components: {
        AppLayout,
        Schedule,
        ChevronDownIcon, 
        SearchIcon
    },

    data() {
        return {
            showFilter: true,
            btnTextMap: ''
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
        }
    },
    mounted() {
        this.showMap()
    }
})
const showFilter = false


</script>