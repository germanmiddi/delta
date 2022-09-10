<template>

    <GoogleMap api-key="AIzaSyC2ZgKApfK_YBbSnZE6NGGacXCnYqF3zNw" style="width: 100%; height: 600px" :center="center"
        :zoom="14">

        <Marker v-for="marker in markerOptions" :options="marker" />

    </GoogleMap>
</template>

<script>

import { objectExpression } from '@babel/types';
import { defineComponent } from 'vue'
import { GoogleMap, Marker } from "vue3-google-map";
import { OfficeBuildingIcon } from '@heroicons/vue/solid'
import Icons from '@/Layouts/Components/Icons.vue'

export default defineComponent({
    props: {
        form_map: Object
    },
    components: {
        GoogleMap,
        Marker,
        OfficeBuildingIcon,
        Icons
    },
    data() {
        return {
            markerOptions: [

            ],
            center:
            {
                lat: -34.5347259,
                lng: -58.5097959
            },
            iconClient: "https://cdn0.iconfinder.com/data/icons/typicons-2/24/flag-32.png",
            iconDelta: "https://cdn3.iconfinder.com/data/icons/kicon-business/24/office-32.png",


        }
    },
    methods: {
        renderMap() {
            this.markerOptions.push({
                "title": 'Delta',
                "position": {
                    "lat": -34.5347259,
                    "lng": -58.5097959
                },
                "icon": {
                    "url": this.iconDelta,
                },
            })
            Array.from(this.form_map).forEach((value, i) => {
                if (value.client.address.google_latitude && value.client.address.google_longitude) {
                    this.markerOptions.push({
                        "title": value.client.address.google_address,
                        //"label": value.client.fullname.charAt(0).toUpperCase(),
                        "position": {
                            "lat": value.client.address.google_latitude,
                            "lng": value.client.address.google_longitude
                        },
                        "icon": {
                            "url": this.iconClient,
                        },
                    })
                }

            });
        },
    },
    watch: {
        form_map: {
            handler: function (newForm_map) {
                this.markerOptions.push({
                    "title": 'Delta',
                    "position": {
                        "lat": -34.5347259,
                        "lng": -58.5097959
                    },
                    "icon": {
                        "url": this.iconDelta,
                    },
                })
                Array.from(newForm_map).forEach((value, i) => {
                    if (value.client[0].address.google_latitude && value.client[0].address.google_longitude) {
                        this.markerOptions.push({
                            "title": value.client[0].address.google_address,
                            //"label": value.client[0].fullname.charAt(0).toUpperCase(),
                            "position": {
                                "lat": value.client[0].address.google_latitude,
                                "lng": value.client[0].address.google_longitude
                            },
                            "icon": {
                                "path": this.iconClient,
                            },
                        })
                    }
                });
            },
        }
    },
    mounted() {
        this.renderMap()
        //console.log(this.markerOptions)
    },
    created() {

    }
});
// CENTER: <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
// <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
// </svg>

// CLIENT: <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
// <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" />
// </svg>



</script>
