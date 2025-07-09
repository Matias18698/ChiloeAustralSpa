<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import AppMain from '@/Layouts/AppMain.vue'
import SignaturePad from '@/Components/SignaturePad.vue'

const props = defineProps({
  trabajadores: Array,
  embarcaciones: Array
})

const form = useForm({
  fecha: '',
  numero_boleta: '',
  zona: '',
  centro: '',
  embarcacion_id: '',
  trabajador_id: '',
  hora_inicial: '',
  hora_final: '',
  actividades_am: '',
  actividades_pm: '',
  firma_encargado: '',
  observaciones: '',
  total_jaulas: '',
  dimension_jaulas: '',
    // Nuevos campos para buzos
  buzo_1_id: '',
  buzo_2_id: '',
  buzo_3_id: '',
  buzo_4_id: '',
  buzo_5_id: '',
})

import { ref } from 'vue'

const signatureRef = ref(null)

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
    hora_inicial: 'La hora inicial es obligatoria.',
    hora_final: 'La hora final es obligatoria.',
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

    // Guardar firma como base64 antes de enviar
  if (signatureRef.value && !signatureRef.value.isEmpty()) {
    form.firma_encargado = signatureRef.value.saveSignature()
  } else {
    form.errors.firma_encargado = 'Debe ingresar la firma del encargado.'
    return
  }
  // Verifica errores antes de enviar
  if (Object.keys(form.errors).length > 0) return

  form.post(route('bitacora.store'), {
    onSuccess: () => {
      router.visit(route('bitacora.index'))
    }
  })
}

const clearSignature = () => {
  if (signatureRef.value) {
    signatureRef.value.clearSignature()
  }
}
</script>

<template>
  <Head title="Nueva Bitácora" />

  <AppMain>
    <div class="w-full bg-gradient-to-b from-blue-50 to-blue-100 min-h-screen p-6">
      <div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow-lg border border-blue-200">
        <h2 class="text-2xl font-bold text-blue-800 mb-8">Registrar Nueva Bitácora</h2>

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
                <option v-for="t in trabajadores" :key="t.id" :value="t.id">{{ t.nombre }} </option>
              </select>
              <p v-if="form.errors.trabajador_id" class="error">{{ form.errors.trabajador_id }}</p>
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




          </div>

          <!-- Horarios -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">Hora Inicial</label>
              <input type="time" v-model="form.hora_inicial" class="input" />
              <p v-if="form.errors.hora_inicial" class="error">{{ form.errors.hora_inicial }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Hora Final</label>
              <input type="time" v-model="form.hora_final" class="input" />
              <p v-if="form.errors.hora_final" class="error">{{ form.errors.hora_final }}</p>
            </div>
          </div>
          <!-- total jaulas -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Total Jaulas</label>
            <input 
              type="number" 
              v-model.number="form.total_jaulas" 
              class="input"
              min="0"
            />
            <p v-if="form.errors.total_jaulas" class="error">{{ form.errors.total_jaulas }}</p>
          </div>
          <!-- Dimensiones de Jaulas -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Dimensiones de Jaulas</label>
            <input 
              type="text" 
              v-model="form.dimension_jaulas" 
              class="input"
              placeholder="Ej: 10x10"
            />
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
          <!-- Observaciones-->
           <div>
            <label class="block text-sm font-medium text-gray-700">Observaciones</label>
            <textarea v-model="form.observaciones" rows="3" class="input"></textarea>
            <p v-if="form.errors.o" class="error">{{ form.errors.observaciones }}</p>
          </div>


            <!-- Firma Encargado -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Firma del Encargado de Faena <span class="text-red-500">*</span>
              </label>
              <p class="text-xs text-gray-500 mb-2">Por favor, firme en el recuadro utilizando el mouse o el dedo (en pantallas táctiles).</p>

              <!-- Aquí se coloca el signature pad real -->
              <SignaturePad ref="signatureRef" class="w-full h-48" />

              <div class="flex justify-between items-center mt-2">
                <button
                  type="button"
                  @click="clearSignature"
                  class="text-sm text-blue-600 hover:underline"
                >
                  🧹 Limpiar firma
                </button>
              </div>
            </div>



          <!-- Botón -->
          <div>
            <button 
              type="submit"
              :disabled="form.processing"
              class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-md shadow hover:bg-blue-700 transition disabled:opacity-50"
            >
              {{ form.processing ? 'Guardando...' : 'Guardar Bitácora' }}
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
