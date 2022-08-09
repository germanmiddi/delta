<template>
  <AppLayout title="Choferes">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Choferes
      </h2>
      <a class="btn-blue" href="#" @click.prevent="vaciarChofer(), editing = false, open = true">
        Nuevo Chofer
      </a>
    </template>
    <Toast :toast="this.toastMessage" :type="this.labelType" @clear="clearMessage"></Toast>
    <div>
      <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

        <div v-if="showToast" class="rounded-md bg-green-50 p-4 mb-5  ">
          <div class="flex">
            <div class="flex-shrink-0">
              <CheckCircleIcon class="h-5 w-5 text-green-400" aria-hidden="true" />
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-green-800">{{ this.message }}</p>
            </div>
            <div class="ml-auto pl-3">
              <div class="-mx-1.5 -my-1.5">
                <button type="button" @click="showToast = false"
                  class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                  <span class="sr-only">Dismiss</span>
                  <XIcon class="h-5 w-5" aria-hidden="true" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
          <table class="w-full whitespace-nowrap">
            <tr class="text-left font-bold bg-blue-500 text-white">
              <th class="px-6 py-4 text-center">ID</th>
              <th class="px-6 py-4 text-center">Nombre y Apellido</th>
              <th class="px-6 py-4 text-center">Camión</th>
              <th class="px-6 py-4 text-center">Acciones</th>
            </tr>
            <tr v-for="driver in drivers.data" :key="driver.id"
              class="hover:bg-gray-100 focus-within:bg-gray-100 text-sm ">
              <td class="border-t px-6 py-4 text-center">
                {{ driver.id }}
              </td>
              <td class="border-t px-6 py-4 text-center">
                {{ driver.fullname }}
              </td>
              <td class="border-t px-6 py-4 text-center">
                {{ driver.truck }}
              </td>
              <td class="border-t px-6 py-4 text-center">
                <button
                  class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-blue-300 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  @click="
                  idDriver = driver.id,
                  formFullname = driver.fullname,
                  formDni = driver.dni,
                  formPhone = driver.phone,
                  formCellphone = driver.cellphone,
                  formEmail = driver.email,
                  formTruck = driver.truck,
                  formNotes = driver.notes,
                  editing = true,
                  open = true">
                  <PencilIcon class="h-5 w-5" aria-hidden="true" />
                </button>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <TransitionRoot as="template" :show="open">
      <Dialog as="div" class="fixed inset-0 overflow-hidden" @close="open = false">
        <div class="absolute inset-0 overflow-hidden">
          <DialogOverlay class="absolute inset-0" />

          <div class="fixed inset-y-0 pl-16 max-w-full right-0 flex">
            <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700"
              enter-from="translate-x-full" enter-to="translate-x-0"
              leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0"
              leave-to="translate-x-full">
              <div class="w-screen max-w-md">
                <form class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl">
                  <div class="flex-1 h-0 overflow-y-auto">
                    <div class="py-6 px-4 bg-blue-500 sm:px-6">
                      <div class="flex items-center justify-between">
                        <DialogTitle v-if="editing == false" class="text-lg font-medium text-white"> Nuevo Chofer
                        </DialogTitle>

                        <DialogTitle v-else class="text-lg font-medium text-white"> Editar Chofer </DialogTitle>
                        <div class="ml-3 h-7 flex items-center">
                          <button type="button"
                            class="bg-blue-500 rounded-md text-indigo-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
                            @click="open = false">
                            <span class="sr-only">Cerrar</span>
                            <XIcon class="h-6 w-6" aria-hidden="true" />
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="flex-1 flex flex-col justify-between">
                      <div class="px-4 divide-y divide-gray-200 sm:px-6">

                        <div class="space-y-6 pt-6 pb-5">
                          <div>
                            <label for="fullname" class="block text-sm font-medium text-gray-900">Nombre y
                              Apellido</label>
                            <div class="mt-1">
                              <input type="text" v-model="formFullname" name="fullname" id="fullname"
                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" />
                            </div>
                          </div>

                          <div>
                            <label for="dni" class="block text-sm font-medium text-gray-900">Documento</label>
                            <div class="mt-1">
                              <input type="text" name="dni" v-model="formDni" id="dni"
                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" />
                            </div>
                          </div>

                          <div>
                            <label for="phone" class="block text-sm font-medium text-gray-900">Teléfono</label>
                            <div class="mt-1">
                              <input type="text" name="phone" v-model="formPhone" id="phone"
                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" />
                            </div>
                          </div>

                          <div>
                            <label for="cellphone" class="block text-sm font-medium text-gray-900">Celular</label>
                            <div class="mt-1">
                              <input type="text" name="cellphone" v-model="formCellphone" id="cellphone"
                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" />
                            </div>
                          </div>

                          <div>
                            <label for="email" class="block text-sm font-medium text-gray-900">Email</label>
                            <div class="mt-1">
                              <input type="text" name="email" id="email" v-model="formEmail"
                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" />
                            </div>
                          </div>

                          <div>
                            <label for="truck" class="block text-sm font-medium text-gray-900">Camión</label>
                            <div class="mt-1">
                              <input type="text" name="truck" id="truck" v-model="formTruck"
                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" />
                            </div>
                          </div>

                          <div>
                            <label for="notes" class="block text-sm font-medium text-gray-900">Notas</label>
                            <div class="mt-1">
                              <textarea type="text" name="notes" id="notes" rows="5" v-model="formNotes"
                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md"></textarea>
                            </div>
                          </div>

                        </div>

                      </div>
                    </div>
                  </div>
                  <div class="flex-shrink-0 px-4 py-4 flex justify-end">
                    <button type="button"
                      class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                      @click="open = false">Cancelar</button>
                    <button @click.prevent="submit"
                      class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Guardar</button>
                  </div>
                </form>
              </div>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

  </AppLayout>
</template>
<script>

import { defineComponent } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue';
import JetButton from '@/Jetstream/Button.vue';
import Toast from '@/Layouts/Components/Toast.vue'

import { Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XIcon, PencilIcon } from '@heroicons/vue/outline'
import { LinkIcon, PlusSmIcon, QuestionMarkCircleIcon, CheckCircleIcon } from '@heroicons/vue/solid'

export default defineComponent({
  components: {
    AppLayout,
    JetButton,
    Dialog,
    DialogOverlay,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
    LinkIcon,
    PlusSmIcon,
    QuestionMarkCircleIcon,
    CheckCircleIcon,
    XIcon,
    PencilIcon,
    Toast

  },
  data() {
    return {
      open: false,
      editing: false,
      showToast: false,
      showForm: false,
      formFullname: "",
      formDni: "",
      formPhone: "",
      formCellphone: "",
      formEmail: "",
      formTruck: "",
      formNotes: "",
      drivers: "",
      idDriver: "",
      message: "",
      toastMessage: "",
      labelType: "info",
    }
  },
  methods: {
    async getDrivers() {
      const get = `${route('drivers.list')}`

      const response = await fetch(get, { method: 'GET' })
      this.drivers = await response.json()

    },
    clearMessage() {
      this.toastMessage = ""
    },
    submit() {
      let rt = '';
      if (this.editing) {
        rt = route('drivers.update');
      } else {
        rt = route('drivers.store');
      }
      axios.post(rt, {
        id: this.idDriver,
        fullname: this.formFullname,
        dni: this.formDni,
        phone: this.formPhone,
        cellphone: this.formCellphone,
        email: this.formEmail,
        truck: this.formTruck,
        notes: this.formNotes,
      }).then(response => {

        if(response.status == 200){
          this.message = response.data.message
          this.showToast = true
        }else{
          this.labelType = "danger"
          this.toastMessage = response.data.message
        }
        this.open = false
        this.getDrivers()
      })
      this.vaciarChofer();
    },
    vaciarChofer() {
      this.editing = false,
        this.formFullname = '',
        this.idDriver = '',
        this.formDni = '',
        this.formPhone = '',
        this.formCellphone = '',
        this.formEmail = '',
        this.formTruck = '',
        this.formNotes = ''
    }
  },
  created() {
    this.getDrivers()
  }
})
</script>
