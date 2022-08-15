<template>
    <GoogleMap  api-key="AIzaSyC2ZgKApfK_YBbSnZE6NGGacXCnYqF3zNw" style="width: 100%; height: 300px" :center="center"
        :zoom="16">
        <Marker :options="{ position: center }" />
    </GoogleMap>
</template>

<script>

import { defineComponent } from 'vue'
import { GoogleMap, Marker } from "vue3-google-map";

export default defineComponent({
    props: {
        latitude: String,
        longitude: String,
        showMap: Boolean,
    },

    components: {
        GoogleMap,
        Marker
    },
    data() {
        return {
            centro:{
                lat: -34,
                lon: -48
            } 
        }
    },
    setup() {
        const center = { lat: 5, lon: 0};

        return {
            center
        }
    }
});

</script>