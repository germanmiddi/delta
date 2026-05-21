<template>

    <GoogleMap
        ref="mapRef"
        api-key="AIzaSyC2ZgKApfK_YBbSnZE6NGGacXCnYqF3zNw"
        style="width: 100%; height: 600px"
        :center="center"
        :zoom="12"
        :options="mapOptions">
        <Marker
            v-for="(marker, index) in markerOptions"
            :key="index"
            :options="marker">
            <InfoWindow>
                <div class="p-2" style="min-width: 200px;">
                    <div class="font-bold text-base mb-2">{{ marker.infoData?.id || marker.title }}</div>
                    <div class="text-sm mb-1"><strong>Dirección:</strong> {{ marker.infoData?.address || '-' }}</div>
                    <div class="text-sm mb-1"><strong>Tipo:</strong> <span :style="'color:' + marker.infoData?.color">{{ marker.infoData?.type || '-' }}</span></div>
                    <div class="text-sm"><strong>Estado:</strong> {{ marker.infoData?.status || '-' }}</div>
                </div>
            </InfoWindow>
        </Marker>
    </GoogleMap>
</template>

<script>

import { defineComponent } from 'vue'
import { GoogleMap, Marker, InfoWindow } from "vue3-google-map";
import { OfficeBuildingIcon, EyeOffIcon } from '@heroicons/vue/solid'
import Icons from '@/Layouts/Components/Icons.vue'

export default defineComponent({
    props: {
        form_map: Object
    },
    components: {
        GoogleMap,
        Marker,
        InfoWindow,
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
            mapOptions: {
                // Opciones del mapa de Google
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
        getTypeColor(type) {
            switch(type) {
                case 'ENVIO':
                    return 'blue';
                case 'CAMBIO':
                    return '#d97706'; // yellow-600
                case 'RETIRO':
                    return 'red';
                default:
                    return 'gray';
            }
        }
    },
    watch: {
        form_map: {
            handler: function (newForm_map) {
                this.markerOptions = []
                this.markerOptions.push({
                    "title": 'Delta - Sede Central',
                    "position": {
                        "lat": -34.5347259,
                        "lng": -58.5097959
                    },
                    "icon": {
                        "url": this.iconDelta,
                    },
                    "infoData": {
                        "id": "Delta",
                        "address": "Sede Central",
                        "type": "-",
                        "status": "-",
                        "color": "gray"
                    }
                })
                Array.from(newForm_map).forEach((value, i) => {
                    let markerColor = "gray";
                    switch (value.service.type.type) {
                        case "ENVIO":
                            markerColor = "blue"
                            break;
                        case "CAMBIO":
                            markerColor = "yellow"
                            break;
                        case "RETIRO":
                            markerColor = "red"
                            break;
                        default:
                            markerColor = "gray"
                            break;
                    }
                    if (value.client.address && value.client.address.google_latitude && value.client.address.google_longitude) {
                        // Crear una copia nueva del icono para cada marcador
                        const markerIcon = {
                            path: this.svgMarker.path,
                            fillColor: markerColor,
                            fillOpacity: this.svgMarker.fillOpacity,
                            strokeWeight: this.svgMarker.strokeWeight,
                            rotation: this.svgMarker.rotation,
                            scale: 1.5,
                            anchor: new google.maps.Point(15, 30),
                        };
                        this.markerOptions.push({
                            "icon": markerIcon,
                            "position": {
                                "lat": value.client.address.google_latitude,
                                "lng": value.client.address.google_longitude
                            },
                            "title": "Pedido #" + value.order.id,
                            "infoData": {
                                "id": "Pedido #" + value.order.id,
                                "address": value.client.address.google_address || 'Sin dirección',
                                "type": value.service.type.type,
                                "status": value.order_status.status,
                                "color": this.getTypeColor(value.service.type.type)
                            }
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
