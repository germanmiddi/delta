<template>
  <div class="relative">
    <label v-if="label" :for="inputId" class="block text-sm font-medium text-gray-700">{{ label }}</label>
    <div class="relative mt-1">
      <input
        :id="inputId"
        v-model="searchQuery"
        type="text"
        :placeholder="placeholder"
        :disabled="disabled"
        :class="[
          'block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm',
          disabled ? 'bg-gray-50 cursor-not-allowed' : 'bg-white'
        ]"
        @input="handleInput"
        @focus="handleFocus"
        @blur="handleBlur"
        @keydown="handleKeydown"
        autocomplete="off"
      />

      <!-- Debug info -->
      <div v-if="showDropdown" class="text-xs text-gray-500 mt-1">
        Clientes: {{ clients.length }}, Filtrados: {{ filteredClients.length }}
      </div>

      <!-- Dropdown -->
      <div
        v-if="showDropdown && filteredClients.length > 0"
        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
      >
        <div
          v-for="(client, index) in filteredClients"
          :key="client.id"
          :class="[
            'px-3 py-2 cursor-pointer text-sm',
            index === selectedIndex ? 'bg-indigo-100 text-indigo-900' : 'hover:bg-gray-100'
          ]"
          @mousedown.prevent="selectClient(client)"
          @mouseenter="selectedIndex = index"
        >
          <div class="font-medium">{{ client.fullname }}</div>
        </div>
      </div>

      <!-- No results -->
      <div
        v-if="showDropdown && searchQuery.length > 0 && filteredClients.length === 0"
        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg p-3 text-sm text-gray-500"
      >
        No se encontraron clientes
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ClientAutocomplete',
  props: {
    clients: {
      type: Array,
      default: () => []
    },
    modelValue: {
      type: [String, Number],
      default: ''
    },
    label: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: 'Buscar cliente...'
    },
    disabled: {
      type: Boolean,
      default: false
    },
    inputId: {
      type: String,
      default: 'client-autocomplete'
    }
  },
  emits: ['update:modelValue', 'client-selected'],
  data() {
    return {
      searchQuery: '',
      showDropdown: false,
      selectedIndex: -1,
      selectedClient: null
    }
  },
  computed: {
    filteredClients() {
      if (!this.clients || this.clients.length === 0) {
        return [];
      }

      if (!this.searchQuery || this.searchQuery.length < 1) {
        return this.clients.slice(0, 50); // Mostrar primeros 50 si no hay búsqueda
      }

      const query = this.searchQuery.toLowerCase();
      return this.clients
        .filter(client => {
          const searchText = client.search_text || (client.fullname ? client.fullname.toLowerCase() : '');
          return searchText.includes(query);
        })
        .slice(0, 20); // Limitar a 20 resultados
    }
  },
  watch: {
    modelValue: {
      immediate: true,
      handler(newValue) {
        if (newValue && this.clients.length > 0) {
          const client = this.clients.find(c => c.id == newValue);
          if (client) {
            this.selectedClient = client;
            this.searchQuery = client.fullname;
          }
        } else {
          this.searchQuery = '';
          this.selectedClient = null;
        }
      }
    },
    clients: {
      handler() {
        // Actualizar cliente seleccionado cuando cambian los clientes
        if (this.modelValue && this.clients.length > 0) {
          const client = this.clients.find(c => c.id == this.modelValue);
          if (client) {
            this.selectedClient = client;
            this.searchQuery = client.fullname;
          }
        }
      }
    }
  },
  methods: {
    handleFocus() {
      this.showDropdown = true;
      this.selectedIndex = -1;
    },
    handleInput() {
      this.showDropdown = true;
      this.selectedIndex = -1;

      // Si se borra el texto, limpiar selección
      if (!this.searchQuery) {
        this.clearSelection();
      }
    },
    handleBlur() {
      // Delay para permitir click en opciones
      setTimeout(() => {
        this.showDropdown = false;
        this.selectedIndex = -1;
      }, 150);
    },
    handleKeydown(event) {
      if (!this.showDropdown) return;

      switch (event.key) {
        case 'ArrowDown':
          event.preventDefault();
          this.selectedIndex = Math.min(this.selectedIndex + 1, this.filteredClients.length - 1);
          break;
        case 'ArrowUp':
          event.preventDefault();
          this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
          break;
        case 'Enter':
          event.preventDefault();
          if (this.selectedIndex >= 0 && this.filteredClients[this.selectedIndex]) {
            this.selectClient(this.filteredClients[this.selectedIndex]);
          }
          break;
        case 'Escape':
          this.showDropdown = false;
          this.selectedIndex = -1;
          break;
      }
    },
    selectClient(client) {
      this.selectedClient = client;
      this.searchQuery = client.fullname;
      this.showDropdown = false;
      this.selectedIndex = -1;

      this.$emit('update:modelValue', client.id);
      this.$emit('client-selected', client);
    },
    clearSelection() {
      this.selectedClient = null;
      this.searchQuery = '';
      this.$emit('update:modelValue', '');
      this.$emit('client-selected', null);
    }
  }
}
</script>
