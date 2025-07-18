<script setup>
import AppMain from '@/Layouts/AppMain.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

const props = defineProps({
  trabajadores: Array,
  embarcaciones: Array
})

const form = useForm({
  trabajador_id: '',
  embarcacion_id: '',
  // Modificación en la fecha para que sea correcta según la zona horaria local
  fecha: new Date().toLocaleDateString('en-CA'),  // Cambié a 'en-CA' para obtener el formato YYYY-MM-DD
  tipo_asistencia: ''
})

const tipos = [
  { value: 'D', label: 'Descanso' },
  { value: 'TR', label: 'Trabajo' },
  { value: 'L', label: 'Licencia' },
  { value: 'F', label: 'Falta' }
]

// Alerta visual
const alerta = ref({
  tipo: '', mensaje: '', visible: false
})

const mostrarAlerta = (mensaje, tipo = 'success', duracion = 3000) => {
  alerta.value = { mensaje, tipo, visible: true }
  setTimeout(() => { alerta.value.visible = false }, duracion)
}

// Autocompletado
const trabajadorInput = ref('')
const filteredTrabajadores = computed(() => {
  const query = trabajadorInput.value.toLowerCase()
  return props.trabajadores.filter(t =>
    t.nombre.toLowerCase().includes(query) ||
    t.rut.toLowerCase().includes(query)
  )
})

watch(trabajadorInput, (val) => {
  const encontrado = props.trabajadores.find(t =>
    `${t.nombre} - ${t.rut}`.toLowerCase() === val.toLowerCase()
  )
  form.trabajador_id = encontrado ? encontrado.id : ''
})
</script>

<template>
  <Head title="Registrar Asistencia" />

  <AppMain>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-white py-10 px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-xl space-y-8 border border-gray-200">
        <h2 class="text-3xl font-bold text-blue-800 border-b pb-4">Registrar Asistencia</h2>

        <!-- Alerta -->
        <div v-if="alerta.visible" :class="[ 
          'p-3 rounded-md text-sm font-medium text-center',
          alerta.tipo === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
        ]">
          {{ alerta.mensaje }}
        </div>

        <!-- Formulario manual -->
        <form @submit.prevent="form.post(route('asistencia.store'), {
          onSuccess: () => mostrarAlerta('Asistencia registrada correctamente.'),
          onError: () => mostrarAlerta('Hubo un error al registrar la asistencia.', 'error')
        })" class="space-y-6">

          <!-- Trabajador con Autocompletado -->
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">Trabajador</label>
            <input
              type="text"
              v-model="trabajadorInput"
              placeholder="Buscar por nombre o RUT"
              class="form-input w-full rounded border-gray-300"
              autocomplete="off"
            />
            <ul v-if="trabajadorInput && filteredTrabajadores.length" class="absolute z-10 w-full bg-white border mt-1 rounded shadow max-h-48 overflow-auto">
              <li
                v-for="t in filteredTrabajadores"
                :key="t.id"
                class="px-3 py-2 hover:bg-blue-100 cursor-pointer"
                @click="trabajadorInput = `${t.nombre} - ${t.rut}`"
              >
                {{ t.nombre }} - {{ t.rut }}
              </li>
            </ul>
            <p v-if="form.errors.trabajador_id" class="text-red-500 text-sm mt-1">{{ form.errors.trabajador_id }}</p>
          </div>



          <!-- Fecha -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
            <input type="date" v-model="form.fecha" class="form-input w-full rounded border-gray-300" />
            <p v-if="form.errors.fecha" class="text-red-500 text-sm mt-1">{{ form.errors.fecha }}</p>
          </div>

          <!-- Tipo de asistencia -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de asistencia</label>
            <select v-model="form.tipo_asistencia" class="form-select w-full rounded border-gray-300">
              <option disabled value="">Selecciona un tipo</option>
              <option v-for="tipo in tipos" :key="tipo.value" :value="tipo.value">{{ tipo.label }}</option>
            </select>
            <p v-if="form.errors.tipo_asistencia" class="text-red-500 text-sm mt-1">{{ form.errors.tipo_asistencia }}</p>
          </div>

          <!-- Botón -->
          <div>
            <button
              type="submit"
              class="w-full py-3 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-semibold"
              :disabled="form.processing || !form.trabajador_id"
            >
              Guardar asistencia
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppMain>
</template>
