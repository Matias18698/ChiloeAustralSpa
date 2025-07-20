<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import AppMain from '@/Layouts/AppMain.vue'
import { ref } from 'vue'

const props = defineProps({
  bitacora: Object,
  trabajadores: Array,
  embarcaciones: Array
})

const form = useForm({
  fecha: props.bitacora.fecha ?? '',
  numero_boleta: props.bitacora.numero_boleta ?? '',
  zona: props.bitacora.zona ?? '',
  centro: props.bitacora.centro ?? '',
  embarcacion_id: props.bitacora.embarcacion_id ?? '',
  trabajador_id: props.bitacora.trabajador_id ?? '',
  hora_inicial: props.bitacora.hora_inicial ?? '',
  hora_final: props.bitacora.hora_final ?? '',
  actividades_am: props.bitacora.actividades_am ?? '',
  actividades_pm: props.bitacora.actividades_pm ?? '',
  firma_encargado: props.bitacora.firma_encargado ?? '',
  observaciones: props.bitacora.observaciones ?? '',
  total_jaulas: props.bitacora.total_jaulas ?? '',
  dimension_jaulas: props.bitacora.dimension_jaulas ?? '',
  buzo_1_id: props.bitacora.buzo_1_id ?? '',
  buzo_2_id: props.bitacora.buzo_2_id ?? '',
  buzo_3_id: props.bitacora.buzo_3_id ?? '',
  buzo_4_id: props.bitacora.buzo_4_id ?? '',
  buzo_5_id: props.bitacora.buzo_5_id ?? '',
})

const horaInicialOriginal = ref(props.bitacora.hora_inicial)
const horaFinalOriginal = ref(props.bitacora.hora_final)

const submit = () => {
  form.clearErrors()

  // Validación requerida
  const requiredFields = {
    fecha: 'La fecha es obligatoria.',
    numero_boleta: 'El número de boleta es obligatorio.',
    zona: 'La zona es obligatoria.',
    centro: 'El centro es obligatorio.',
    embarcacion_id: 'Debe seleccionar una embarcación.',
    trabajador_id: 'Debe seleccionar un encargado de faena.',
    actividades_am: 'Debe ingresar las actividades AM.',
    actividades_pm: 'Debe ingresar las actividades PM.',
    total_jaulas: 'Debe ingresar el total de jaulas.',
    dimension_jaulas: 'Debe indicar las dimensiones de las jaulas.'
  }

  for (const [field, message] of Object.entries(requiredFields)) {
    if (!form[field]) {
      form.errors[field] = message
    }
  }


  // Verifica errores antes de enviar
  if (Object.keys(form.errors).length > 0) return

  form.post(route('bitacora.update', props.bitacora.id), {
    onSuccess: () => {
      router.visit(route('bitacora.index'))
    }
  })
}
</script>

<template>
  <Head title="Editar Bitácora" />

  <AppMain>
    <div class="w-full bg-gradient-to-b from-blue-50 to-blue-100 min-h-screen p-6">
      <div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow-lg border border-blue-200">
        <h2 class="text-2xl font-bold text-blue-800 mb-8">Editar Bitácora</h2>

        <form @submit.prevent="submit" class="space-y-6">
          <!-- Información General -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">Fecha</label>
              <input type="date" v-model="form.fecha" class="input" />
              <p v-if="form.errors.fecha" class="error">{{ form.errors.fecha }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Nº Boleta</label>
              <input type="text" v-model="form.numero_boleta" class="input" />
              <p v-if="form.errors.numero_boleta" class="error">{{ form.errors.numero_boleta }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Zona</label>
              <input type="text" v-model="form.zona" class="input" />
              <p v-if="form.errors.zona" class="error">{{ form.errors.zona }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Centro</label>
              <input type="text" v-model="form.centro" class="input" />
              <p v-if="form.errors.centro" class="error">{{ form.errors.centro }}</p>
            </div>
          </div>

          <!-- Asignaciones -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">Embarcación</label>
              <select v-model="form.embarcacion_id" class="input">
                <option disabled value="">Selecciona una embarcación</option>
                <option v-for="e in embarcaciones" :key="e.id" :value="e.id">{{ e.nombre }}</option>
              </select>
              <p v-if="form.errors.embarcacion_id" class="error">{{ form.errors.embarcacion_id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Encargado de Faena</label>
              <select v-model="form.trabajador_id" class="input">
                <option disabled value="">Selecciona un trabajador</option>
                <option v-for="t in trabajadores" :key="t.id" :value="t.id">{{ t.nombre }}</option>
              </select>
              <p v-if="form.errors.trabajador_id" class="error">{{ form.errors.trabajador_id }}</p>
            </div>
          </div>
          <!-- buzos -->
              <div class="col-span-full">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 w-full">
                  <div v-for="i in 5" :key="i">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Buzo {{ i }}
                    </label>
                    <select
                      v-model="form[`buzo_${i}_id`]"
                      class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                      <option disabled value="">Selecciona un buzo</option>
                        <option
                        v-for="t in trabajadores.filter(t => t.cargo && t.cargo.toLowerCase() === 'buzo')"
                        :key="t.id"
                        :value="t.id"
                      >
                        {{ t.nombre }}
                      </option>
                    </select>
                    <p v-if="form.errors[`buzo_${i}_id`]" class="text-sm text-red-600 mt-1">
                      {{ form.errors[`buzo_${i}_id`] }}
                    </p>
                  </div>
                </div>
              </div>
          <!-- Horarios -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">Hora Inicial</label>
              <input type="time" v-model="form.hora_inicial" class="input" />
              <p v-if="form.errors.hora_inicial" class="error">Debe ingresar nuevamente la hora inicial.</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Hora Final</label>
              <input type="time" v-model="form.hora_final" class="input" />
              <p v-if="form.errors.hora_final" class="error">Debe ingresar nuevamente la hora final.</p>
            </div>
          </div>

          <!-- Jaulas -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Total Jaulas</label>
            <input type="number" v-model.number="form.total_jaulas" class="input" min="0" />
            <p v-if="form.errors.total_jaulas" class="error">{{ form.errors.total_jaulas }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Dimensiones de Jaulas</label>
            <input type="text" v-model="form.dimension_jaulas" class="input" placeholder="Ej: 10x10" />
            <p v-if="form.errors.dimension_jaulas" class="error">{{ form.errors.dimension_jaulas }}</p>
          </div>

          <!-- Actividades -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Actividades AM</label>
            <textarea v-model="form.actividades_am" rows="3" class="input"></textarea>
            <p v-if="form.errors.actividades_am" class="error">{{ form.errors.actividades_am }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Actividades PM</label>
            <textarea v-model="form.actividades_pm" rows="3" class="input"></textarea>
            <p v-if="form.errors.actividades_pm" class="error">{{ form.errors.actividades_pm }}</p>
          </div>

          <!-- Observaciones -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Observaciones</label>
            <textarea v-model="form.observaciones" rows="3" class="input"></textarea>
            <p v-if="form.errors.observaciones" class="error">{{ form.errors.observaciones }}</p>
          </div>

          <!-- Firma del encargado (solo visual) -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Firma del Encargado</label>
              <div class="h-20 flex items-end justify-left mb-1 relative">
                <img
                  v-if="bitacora.firma_encargado"
                  :src="`/storage/${bitacora.firma_encargado}`"
                  alt="Firma"
                  class="border rounded-md max-w-xs object-contain"
                />
              </div>
          </div>
          <!-- Botón -->
          <div class="pt-4">
            <button 
              type="submit"
              :disabled="form.processing"
              class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-md shadow hover:bg-blue-700 transition disabled:opacity-50"
            >
              {{ form.processing ? 'Actualizando...' : 'Actualizar Bitácora' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppMain>
</template>

<style scoped>
.input {
  @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500;
}
.error {
  @apply text-red-500 text-sm mt-1;
}
</style>
