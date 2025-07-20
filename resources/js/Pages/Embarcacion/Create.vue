<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppMain from '@/Layouts/AppMain.vue';

// Formulario para crear embarcación
const form = useForm({
  nombre: '',
  tipo: '',
  patente: '',
  capacidad: '',
  estado: 'activo',
  latitud: null,
  longitud: null,
  imei: '' 
});

// Enviar formulario
const submit = () => {
  // Enviar datos incluyendo latitud y longitud si están disponibles
  form.post(route('embarcacion.store'), {
    onSuccess: () => {
      console.log('✅ Embarcación creada correctamente.');
    },
    onError: (errors) => {
      console.error('❌ Error al crear embarcación:', errors);
    }
  });
};
</script>

<template>
  <Head title="Crear Embarcación" />

  <AppMain>
    <div class="w-full bg-gradient-to-b from-blue-50 to-blue-100 min-h-screen p-6">
      <div class="mb-6">
        <h2 class="text-3xl font-bold text-blue-800 flex items-center">
          <span class="mr-2"></span> Crear Nueva Embarcación
        </h2>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6 border border-blue-200">
        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <h3 class="text-lg font-semibold text-blue-800 border-b pb-2 mb-4">Información de la Embarcación</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="nombre" class="block text-sm font-medium text-black">Nombre</label>
                <input type="text" id="nombre" v-model="form.nombre" required class="mt-1 block w-full rounded-md border-gray-300 bg-white text-black" />
              </div>
              <div>
                <label for="tipo" class="block text-sm font-medium text-black">Tipo</label>
                <input type="text" id="tipo" v-model="form.tipo" required class="mt-1 block w-full rounded-md border-gray-300 bg-white text-black" />
              </div>
              <div>
                <label for="patente" class="block text-sm font-medium text-black">Patente</label>
                <input type="text" id="patente" v-model="form.patente" required class="mt-1 block w-full rounded-md border-gray-300 bg-white text-black" />
              </div>
              <div>
                <label for="capacidad" class="block text-sm font-medium text-black">Capacidad</label>
                <input type="number" id="capacidad" v-model="form.capacidad" required class="mt-1 block w-full rounded-md border-gray-300 bg-white text-black" />
              </div>
              <div>
                <label for="imei" class="block text-sm font-medium text-black">IMEI</label>
                <input type="text" id="imei" v-model="form.imei" required class="mt-1 block w-full rounded-md border-gray-300 bg-white text-black" />
              </div>
              <div>
                <label for="estado" class="block text-sm font-medium text-black">Estado</label>
                <select id="estado" v-model="form.estado" class="mt-1 block w-full rounded-md border-gray-300 bg-white text-black">
                  <option value="activo">Activo</option>
                  <option value="inactivo">Inactivo</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Botón submit -->
          <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-900">
              <span>Crear Embarcación</span>
            </button> 
          </div>
        </form>
      </div>
    </div>
  </AppMain>
</template>
