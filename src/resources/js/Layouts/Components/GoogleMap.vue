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
        form_map: Object
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
                        lat: this.form_map['latitude'], 
                        lng: this.form_map['longitude']
                    }, 
                label: 'C', 
                title: this.form_map['route']
            }
        }
    },
    watch:{
        form_map:{
            handler:function(newForm_map){
                this.markerOptions.title = newForm_map['route']
                this.markerOptions.position.lat = newForm_map['latitude']
                this.markerOptions.position.lng = newForm_map['longitude']
            },
        }
    }
});

</script>