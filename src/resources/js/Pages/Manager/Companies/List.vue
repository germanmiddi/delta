<template>
    <AppLayout title="Empresas">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Empresas
            </h2>
            <a class="btn-blue" href="#" @click.prevent="vaciarCompanie(), editing = false, open = true">
                Nueva Empresa
            </a>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

                <div v-if="showToast" class="rounded-md bg-green-50 p-4 mb-5  ">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <CheckCircleIcon class="h-5 w-5 text-green-400" aria-hidden="true" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">Empresa creada con éxito!</p>
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
                            <th class="px-6 py-4 text-center">Razón Social</th>
                            <th class="px-6 py-4 text-center">CUIT</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                        <tr v-for="company in companies.data" :key="company.id"
                            class="hover:bg-gray-100 focus-within:bg-gray-100 text-sm ">
                            <td class="border-t px-6 py-4 text-center">
                                {{ company.id }}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                {{ company.razon_social }}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                {{ company.cuit }}
                            </td>
                            <td class="border-t px-6 py-4 text-center">
                                <button class="inline-flex items-center p-1 border border-transparent rounded-full shadow-sm text-white bg-blue-300 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" @click="
                                            idCompanie = company.id,
                                            formRazonsocial = company.razon_social,
                                            formCuit = company.cuit,
                                            formBillingtype = company.billing_type,
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
                        <TransitionChild as="template"
                            enter="transform transition ease-in-out duration-500 sm:duration-700"
                            enter-from="translate-x-full" enter-to="translate-x-0"
                            leave="transform transition ease-in-out duration-500 sm:duration-700"
                            leave-from="translate-x-0" leave-to="translate-x-full">
                            <div class="w-screen max-w-md">
                                <form class="h-full divide-y divide-gray-200 flex flex-col bg-white shadow-xl">
                                    <div class="flex-1 h-0 overflow-y-auto">
                                        <div class="py-6 px-4 bg-blue-500 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <DialogTitle v-if="editing == false" class="text-lg font-medium text-white"> Nueva Empresa</DialogTitle>
                                                <DialogTitle v-else class="text-lg font-medium text-white"> Editar Empresa</DialogTitle>
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
                                                        <label for="razonsocial"
                                                            class="block text-sm font-medium text-gray-900">Razón
                                                            Social</label>
                                                        <div class="mt-1">
                                                            <input type="text" v-model="formRazonsocial"
                                                                name="razonsocial" id="razonsocial"
                                                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" />
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="cuit"
                                                            class="block text-sm font-medium text-gray-900">CUIT</label>
                                                        <div class="mt-1">
                                                            <input type="text" name="cuit" v-model="formCuit" id="cuit"
                                                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" />
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="billing_type"
                                                            class="block text-sm font-medium text-gray-900">Tipo de
                                                            Factura</label>
                                                        <div class="mt-1">
                                                            <input type="text" name="billing_type"
                                                                v-model="formBillingtype" id="billing_type"
                                                                class="block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" />
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
        PencilIcon
    },
    data() {
        return {
            open: false,
            editing: false,
            showToast: false,
            showForm: false,
            formRazonsocial: "",
            formCuit: "",
            formBillingtype: "",
            companies: "",
            idCompanie: ""
        }
    },
    methods: {
        async getCompanies() {
            const get = `${route('companies.list')}`

            const response = await fetch(get, { method: 'GET' })
            this.companies = await response.json()

        },
        submit() {
            let rt = '';
            if (this.editing) {
                rt = route('companies.edit');
            } else {
                rt = route('companies.store');
            }
            axios.post(rt, {
                id: this.idCompanie,
                razon_social: this.formRazonsocial,
                cuit: this.formCuit,
                billing_type: this.formBillingtype,
            }).then(response => {
                this.open = false
                this.showToast = true
                this.getCompanies()// this.$inertia.get(this.route('drivers.list'), this.params, {replace:true, preserveState:true})
                // console.log(response)
            })
            this.vaciarCompanie();
        },
        vaciarCompanie() {

            this.editing = false,
                this.idCompanie = '',
                this.formRazonsocial = '',
                this.formCuit = '',
                this.formBillingtype = ''
        }
    },
    created() {
        this.getCompanies()
    }
})
</script>
