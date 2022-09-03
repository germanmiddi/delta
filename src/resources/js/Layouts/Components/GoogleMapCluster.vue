<template>
    <GoogleMap api-key="AIzaSyC2ZgKApfK_YBbSnZE6NGGacXCnYqF3zNw" style="width: 100%; height: 400px" :center="center"
        :zoom="14">

        <Marker v-for="marker in markerOptions" :options="marker" />

    </GoogleMap>
</template>

<script>

import { objectExpression } from '@babel/types';
import { defineComponent } from 'vue'
import { GoogleMap, Marker } from "vue3-google-map";

export default defineComponent({
    props: {
        form_map: Object
    },
    components: {
        GoogleMap,
        Marker
    },
    data() {
        return {
            markerOptions: [

            ],
            center:
            {
                lat: -34.55,
                lng: -58.45
            }
        }
    },
    methods: {
        renderMap() {
            Array.from(this.form_map).forEach((value, i) => {
                if (value.client.address.google_latitude && value.client.address.google_longitude) {
                    this.markerOptions.push({
                        "title": value.client.address.google_address,
                        "label": value.client.fullname.charAt(0).toUpperCase(),
                        "position": {
                            "lat": value.client.address.google_latitude,
                            "lng": value.client.address.google_longitude
                        },
                        "icon": {
                            "path": google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
                            "strokeColor": "red",
                            "scale": 6
                        },
                    })
                }

            });
        },
    },
    watch: {
        form_map: {
            handler: function (newForm_map) {
                Array.from(newForm_map).forEach((value, i) => {
                    if (value.client[0].address.google_latitude && value.client[0].address.google_longitude) {
                        this.markerOptions.push({
                            "title": value.client[0].address.google_address,
                            "label": value.client[0].fullname.charAt(0).toUpperCase(),
                            "position": {
                                "lat": value.client[0].address.google_latitude,
                                "lng": value.client[0].address.google_longitude
                            },
                            "icon": {
                                "path": google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
                                "strokeColor": "red",
                                "scale": 6
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
    }
});

</script>