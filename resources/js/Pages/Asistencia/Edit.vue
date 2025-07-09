<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import AppMain from '@/Layouts/AppMain.vue'

const props = defineProps({
  asistencia: Object,
  embarcaciones: Array,
})

// Inicializa el formulario con los datos de la asistencia
const form = useForm({
  fecha: props.asistencia.fecha,
  trabajador_id: props.asistencia.trabajador_id,
  tipo_asistencia: props.asistencia.tipo_asistencia,
  embarcacion_id: props.asistencia.embarcacion_id,
})

const submit = () => {
  form.put(route('asistencias.update', props.asistencia.id), {
    onSuccess: () => {
      router.visit(route('asistencias.index'))
    }
  })
}
</script>

<template>
  <Head title="Editar Asistencia" />

  <AppMain>
    <div class="w-full bg-gradient-to-b from-blue-50 to-blue-100 min-h-screen p-6">
      <div class="max-w-xl mx-auto bg-white p-6 rounded-2xl shadow-lg border border-blue-200">
        <h2 class="text-2xl font-bold text-blue-800 mb-6">📝 Editar Asistencia</h2>

        <form @submit.prevent="submit" class="space-y-6">

          <!-- Fecha -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Fecha</label>
            <input type="date" v-model="form.fecha" class="input" />
            <p v-if="form.errors.fecha" class="error">{{ form.errors.fecha }}</p>
          </div>

          <!-- Embarcación -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Embarcación</label>
            <select v-model="form.embarcacion_id" class="input">
              <option disabled value="">Selecciona una embarcación</option>
              <option v-for="e in embarcaciones" :key="e.id" :value="e.id">{{ e.nombre }}</option>
            </select>
            <p v-if="form.errors.embarcacion_id" class="error">{{ form.errors.embarcacion_id }}</p>
          </div>

          <!-- Botón -->
          <div class="pt-4">
            <button 
              type="submit"
              :disabled="form.processing || !form.trabajador_id || !form.embarcacion_id"
              class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-md shadow hover:bg-blue-700 transition disabled:opacity-50"
            >
              {{ form.processing ? 'Actualizando...' : 'Actualizar Asistencia' }}
            </button>
            <p v-if="!form.trabajador_id || !form.embarcacion_id" class="text-center text-sm text-gray-500 mt-2">
              Escanea un código QR válido y selecciona una embarcación para continuar.
            </p>
          </div>
        </form>
      </div>
    </div>
  </AppMain>
</template>

<style scoped>
.input {
  @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-white text-black;
}
.error {
  @apply text-red-500 text-sm mt-1;
}
</style>
