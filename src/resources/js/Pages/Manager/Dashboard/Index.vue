<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
            <div>
                <a class="btn-blue" @click="showFilter = !showFilter">
                    Ver Mapa
                </a>
            </div>
        </template>

       

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                 <schedule :view="showFilter"/>

                <!-- <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    
                </div> -->
            </div>
        </div> 
    </AppLayout>
</template>

<script>

import { defineComponent } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue';
import Schedule from './Schedule.vue'

export default defineComponent({
    components: {
        AppLayout,
        Schedule,
    },

    data() {
        return {
            showFilter: true,
        }
    }
})
const showFilter = false


</script>