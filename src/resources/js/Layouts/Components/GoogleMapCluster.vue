<template>

    <GoogleMap api-key="AIzaSyC2ZgKApfK_YBbSnZE6NGGacXCnYqF3zNw" style="width: 100%; height: 600px" :center="center"
        :zoom="12" :options="mapOptions">
        <Marker v-for="marker in markerOptions" :options="marker"/>
    </GoogleMap>
</template>

<script>

import { defineComponent } from 'vue'
import { GoogleMap, Marker } from "vue3-google-map";
import { OfficeBuildingIcon, EyeOffIcon } from '@heroicons/vue/solid'
import Icons from '@/Layouts/Components/Icons.vue'

export default defineComponent({
    props: {
        form_map: Object
    },
    components: {
        GoogleMap,
        Marker,
        OfficeBuildingIcon,
        Icons,
        EyeOffIcon
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
            iconDelta: "https://img.icons8.com/external-xnimrodx-lineal-color-xnimrodx/48/null/external-building-real-estate-xnimrodx-lineal-color-xnimrodx-4.png",
            svgMarker: {
                    path: "M24,9c0,4.07-3.06,7.44-7,7.94V30c0,0.55-0.45,1-1,1s-1-0.45-1-1V16.94c-3.94-0.5-7-3.87-7-7.94    c0-4.41,3.59-8,8-8S24,4.59,24,9z",
                    fillColor: "red",
                    fillOpacity: 1,
                    strokeWeight: 0,
                    rotation: 0,
                    scale: 1.5,
                    anchor: new google.maps.Point(15, 30),
                }
        }
    },
    methods: {
    },
    watch: {
        form_map: {
            handler: function (newForm_map) {
                this.markerOptions = []
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
                    switch (value.order_status.status) {
                        case "PROGRAMADO":
                            this.svgMarker.fillColor="orange"
                            break;
                        case "ENTREGADO":
                            this.svgMarker.fillColor="red"
                            break;
                        case "RETIRADO":
                            this.svgMarker.fillColor="green"
                            break;
                        case "CANCELADO":
                            this.svgMarker.fillColor="gray"
                            break;
                        default:
                            this.svgMarker.fillColor="black"
                            break;
                    }
                    if (value.client.address && value.client.address.google_latitude && value.client.address.google_longitude) {
                        this.svgMarker.fillColor = this.svgMarker.fillColor
                        this.markerOptions.push({
                            "icon": this.svgMarker,
                            "title": (value.client.address.google_address || 'Sin dirección') +" | "+value.order_status.status,
                            "position": {
                                "lat": value.client.address.google_latitude,
                                "lng": value.client.address.google_longitude
                            },
                        })
                    }
                });
            },
        }
    },
    mounted() {
    },
    created() {
    }
});


</script>
