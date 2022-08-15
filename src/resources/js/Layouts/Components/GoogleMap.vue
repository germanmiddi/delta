<template>
    <GoogleMap 
        :key="markerOptions.title"
        api-key="AIzaSyC2ZgKApfK_YBbSnZE6NGGacXCnYqF3zNw" 
        style="width: 100%; height: 300px" 
        :center="markerOptions.position" 
        :zoom="16">
        <Marker :options="markerOptions" />
    </GoogleMap>
</template>

<script>

import { defineComponent } from 'vue'
import { GoogleMap, Marker } from "vue3-google-map";

export default defineComponent({
    props: {
        latitude: Object,
        longitude: Object,
        street: Object
    },
    components: {
        GoogleMap,
        Marker
    },
    data() {
        return {
            markerOptions: { 
                position: 
                    { 
                        lat: this.latitude, 
                        lng: this.longitude
                    }, 
                label: 'C', 
                title: this.street 
            }
        }
    },
    /* watch:{
        longitude:{
            handler:function(newLongitude){
                this.marker.position.lng = newLongitude
            },
        },
        street:{
            handler:function(newStreet){
                this.$forceUpdate();
                this.marker.title = newStreet
            },
        },
        latitude:{
            handler:function(newLatitude){
                this.marker.position.lat = newLatitude
            },
        },

    } */
    /* setup() {

        const center = { lat: 40.689247, lng: -74.044502 }
        const markerOptions = { position: center, label: 'C', title: 'Ubicación del Cliente' }

        return {
            center,
            markerOptions
        }
    } */
});

</script>