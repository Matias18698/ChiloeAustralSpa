<script setup>
import AppMain from '@/Layouts/AppMain.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import QrScanner from 'qr-scanner'

// Props
const props = defineProps({
  trabajadores: Array,
})

// Fecha actual (zona horaria Chile)
const chileDate = new Date().toLocaleDateString('en-CA', { timeZone: 'America/Santiago' })

// Formulario Inertia
const form = useForm({
  trabajador_id: '',
  fecha: chileDate,
  tipo_asistencia: 'TR'
})

// Refs y estados
const video = ref(null)
const scanner = ref(null)
const cameras = ref([])
const selectedCameraId = ref('')
const trabajadorSeleccionado = ref(null)
const trabajadorNombre = ref('')
const showScanBox = ref(true)
const isSubmitting = ref(false)
const alerta = ref({ visible: false, mensaje: '', tipo: 'success' })

// Función para normalizar RUT
const normalizeRut = rut => rut.replace(/\./g, '').replace('-', '').toLowerCase()

// Mostrar alerta
const mostrarAlerta = (mensaje, tipo = 'success') => {
  alerta.value = { mensaje, tipo, visible: true }
  setTimeout(() => (alerta.value.visible = false), 10000)
}

// Escáner detecta un resultado
const setResult = ({ data }) => {
  console.log('QR escaneado:', data)

  // Extrae el RUT del QR 
  const match = data.match(/RUN=([^\s&]*)/)
  const rutExtraido = match ? match[1] : data.trim()
  const rut = normalizeRut(rutExtraido)

  const trabajador = props.trabajadores.find(t => normalizeRut(t.rut) === rut)

  if (trabajador) {
    form.trabajador_id = trabajador.id
    trabajadorSeleccionado.value = trabajador
    trabajadorNombre.value = trabajador.nombre
    mostrarAlerta(`Trabajador ${trabajador.nombre} ${trabajador.apellido} escaneado con éxito ✅`)
    showScanBox.value = false
    stopScanner()
  } else {
    mostrarAlerta(`❌ RUT ${rut} no encontrado`, 'error')
  }
}

// Iniciar escáner
const startScanner = async () => {
  try {
    if (!scanner.value) {
      scanner.value = new QrScanner(video.value, setResult, {
        highlightScanRegion: true,
        highlightCodeOutline: true
      })
      await changeCamera()
    }
    await scanner.value.start()
  } catch (error) {
    console.error('Error al iniciar el escáner:', error)
    mostrarAlerta('Error al acceder a la cámara', 'error')
  }
}

// Detener escáner
const stopScanner = () => {
  scanner.value?.stop()
}

// Cambiar cámara
const changeCamera = async () => {
  try {
    await scanner.value?.setCamera(selectedCameraId.value)
  } catch (error) {
    console.error('Error al cambiar la cámara:', error)
  }
}

const reiniciarEscaneo = async () => {
  form.trabajador_id = ''
  trabajadorSeleccionado.value = null
  trabajadorNombre.value = ''
  showScanBox.value = true
  alerta.value.visible = false

  // Destruir el escáner anterior si existe
  if (scanner.value) {
    scanner.value.destroy()
    scanner.value = null
  }

  // Pausa breve para asegurar que se libere el recurso
  await new Promise(resolve => setTimeout(resolve, 100))

  // Crear nuevo escáner
  scanner.value = new QrScanner(video.value, setResult, {
    highlightScanRegion: true,
    highlightCodeOutline: true
  })

  await changeCamera()
  await startScanner()
}


// Guardar asistencia
const guardarAsistencia = () => {
  if (!form.trabajador_id) {
    mostrarAlerta('⚠️ Escanea un trabajador ', 'error')
    return
  }


  // Registro individual
  isSubmitting.value = true
  form.post(route('asistencia.store'), {
    onSuccess: () => {
      mostrarAlerta('✅ Asistencia registrada correctamente')
      reiniciarEscaneo()
    },
    onError: () => mostrarAlerta('❌ Error al registrar asistencia', 'error'),
    onFinish: () => (isSubmitting.value = false)
  })
}

// Montar componente y escáner
onMounted(async () => {
  QrScanner.WORKER_PATH = '/qr-scanner-worker.min.js'

  scanner.value = new QrScanner(video.value, setResult, {
    highlightScanRegion: true,
    highlightCodeOutline: true
  })

  cameras.value = await QrScanner.listCameras(true)
  selectedCameraId.value = cameras.value[0]?.id

  await changeCamera()
  await startScanner()
})

// Limpiar al salir
onBeforeUnmount(() => scanner.value?.destroy())
</script>
<template>
  <Head title="Registrar Asistencia" />
  <AppMain>
    <div class="max-w-xl mx-auto p-6 bg-white shadow rounded mt-10 space-y-6">
      <h1 class="text-xl font-bold">Registrar Asistencia</h1>

      <!-- Alerta -->
      <div v-if="alerta.visible" :class="[
        'p-3 rounded',
        alerta.tipo === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
      ]">
        {{ alerta.mensaje }}
      </div>

      <!-- Escáner QR -->
      <div v-if="showScanBox" class="space-y-4">
        <video ref="video" class="w-full rounded border" />
        <select v-model="selectedCameraId" @change="changeCamera" class="w-full border rounded px-2 py-1">
          <option v-for="cam in cameras" :key="cam.id" :value="cam.id">{{ cam.label }}</option>
        </select>
      </div>

      <!-- Formulario -->
      <form @submit.prevent="guardarAsistencia" class="space-y-4">

        <!-- Reiniciar escaneo -->
        <div v-if="!showScanBox" class="text-sm text-center">
          <button type="button" @click="reiniciarEscaneo" class="text-blue-600 underline">
            Reiniciar escaneo
          </button>
        </div>

        <!-- Botón de guardar -->
        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-2 rounded font-semibold disabled:bg-gray-300"
          :disabled="isSubmitting || !form.trabajador_id" 
        >
          {{ isSubmitting ? 'Guardando...' : 'Guardar Asistencia' }}
        </button>
      </form>
    </div>
  </AppMain>
</template>
