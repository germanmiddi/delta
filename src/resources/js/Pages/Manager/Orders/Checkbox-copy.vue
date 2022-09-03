<template>
  <div class="w-full ">
    <Combobox v-model="selected">
      <div class="relative mt-1">
        <div
          class="relative  w-full cursor-default overflow-hidden rounded-md bg-white text-left shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm"
        >
          <ComboboxInput
            class="w-full border-gray-300 py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 rounded-md focus:ring-0 shadow-sm"
            :displayValue="(d) => d.fullname"
            @change="query = $event.target.value"
          />
          <ComboboxButton
            class="absolute inset-y-0 right-0 flex items-center pr-2"
          >
            <SelectorIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
          </ComboboxButton>
        </div>
        <TransitionRoot
          leave="transition ease-in duration-100"
          leaveFrom="opacity-100"
          leaveTo="opacity-0"
          @after-leave="query = ''"
        >
          <ComboboxOptions
            class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
          >
            <div
              v-if="filteredPeople.length === 0 && query !== ''"
              class="relative cursor-default select-none py-2 px-4 text-gray-700"
            >
              No se encuentran resultados.
            </div>

            <ComboboxOption
              v-for="person in filteredPeople"
              as="template"
              :key="person.id"
              :value="person"
              v-slot="{ selected, active }"
            >
              <li
                class="relative cursor-default select-none py-2 pl-10 pr-4"
                :class="{
                  'bg-teal-600 text-white': active,
                  'text-gray-900': !active,
                }"
              >
                <span
                  class="block truncate"
                  :class="{ 'font-medium': selected, 'font-normal': !selected }"
                >
                  {{ person.fullname }}
                </span>
                <span
                  v-if="selected"
                  class="absolute inset-y-0 left-0 flex items-center pl-3"
                  :class="{ 'text-white': active, 'text-teal-600': !active }"
                >
                  <CheckIcon class="h-5 w-5" aria-hidden="true" />
                </span>
              </li>
            </ComboboxOption>
          </ComboboxOptions>
        </TransitionRoot>
      </div>
    </Combobox>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import {
  Combobox,
  ComboboxInput,
  ComboboxButton,
  ComboboxOptions,
  ComboboxOption,
  TransitionRoot,
} from '@headlessui/vue'
import { CheckIcon, SelectorIcon } from '@heroicons/vue/solid'
import { defineComponent } from 'vue'

  export default{
    components:{
      Combobox,
      ComboboxInput,
      ComboboxButton,
      ComboboxOptions,
      ComboboxOption,
      TransitionRoot,
      CheckIcon, 
      SelectorIcon,
      ref, 
      computed,
      defineComponent
    },
    
    setup(){
        let selected = ref('')
        let query    = ref('')


        return{

          selected,
          query,
          filteredPeople: [],
        }
    },
    data(){
      return{
        drivers: ""
      }
    },
    methods:{
      async getDrivers(){
          const get = `${route('drivers.list')}` 
          const response = await fetch(get, {method:'GET'})
          let list = await response.json() 
          this.drivers = list.data
      }
    },
    computed:{
      filteredPeople(){
        if(this.query.value === ''){
          return this.drivers
        }else{
          return response =  this.drivers.filter((person) => {
                                    //console.log(person.fullname)
                                    return person.fullname.includes(this.query.value)
                                  })
        }
      }
    },
    watch:{
      "drivers": function(){
        console.log(this.query)
        

        
        // this.filteredPeople = this.query.value == 'undefined'
        // ? this.drivers
        // : this.drivers.filter((person) => {
        //     console.log(person.fullname)
        //     return person.fullname.includes(this.query.value)
        //   })
      }
    },

    created(){
      this.getDrivers()
    }
  }


</script>
