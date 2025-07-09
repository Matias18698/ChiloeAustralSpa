<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppMain from '@/Layouts/AppMain.vue';

const props = defineProps({
    embarcaciones: Array,
});

const form = useForm({
    nombre: '',
    apellido: '', 
    avatar: null,
    rut: '',
    fecha_nacimiento: '',
    estado_civil: '',
    nacionalidad: '',
    direccion: '',
    comuna: '',
    email: '',
    telefono: '',
    afp: '',
    cargo: '',
    tamaño_ropa: '',
    tipo_contrato: '',
    sueldo_real: '',
    sueldo_liquidacion: '',
    embarcacion_id: null, // ahora opcional
});

const submit = () => {
      // Si no se seleccionó embarcación, eliminamos el campo del form
  if (!form.embarcacion_id) {
    delete form.embarcacion_id
  }
  form.post(route('trabajador.store'), {
    onSuccess: () => {
      form.reset();
    },
    onError: (errors) => {
      alert('No se pudo crear el trabajador. Por favor revisa los errores en el formulario.');
      console.log(errors);
    }
  });
};

const onSelectAvatar = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.avatar = file;
    }
};
</script>

<template>
  <Head title="Crear trabajador" />

  <AppMain>
    <div class="w-full bg-gradient-to-b from-blue-50 to-blue-100 min-h-screen p-6">
      <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-blue-800 flex items-center">
          Crear Trabajador
        </h2>
      </div>

      <div class="overflow-hidden bg-white rounded-xl shadow-lg p-6 border border-blue-200">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Datos Personales -->
          <div>
            <h3 class="text-lg font-semibold text-blue-800 border-b pb-2 mb-4">Datos Personales</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="nombre" class="block text-sm font-medium text-black">Nombre</label>
                <input type="text" id="nombre" v-model="form.nombre" required class="input" />
              </div>
              <div>
                <label for="apellido" class="block text-sm font-medium text-black">Apellido</label>
                <input type="text" id="apellido" v-model="form.apellido" required class="input" />
              </div>
              <div>
                <label for="rut" class="block text-sm font-medium text-black">RUT</label>
                <input type="text" id="rut" v-model="form.rut" required class="input" :class="{'border-red-500': form.errors.rut}" />
                <p v-if="form.errors.rut" class="text-red-600 text-sm mt-1">{{ form.errors.rut }}</p>
              </div>
              <div>
                <label for="fecha_nacimiento" class="block text-sm font-medium text-black">Fecha de Nacimiento</label>
                <input type="date" id="fecha_nacimiento" v-model="form.fecha_nacimiento" required class="input" />
              </div>
              <div>
                <label for="estado_civil" class="block text-sm font-medium text-black">Estado Civil</label>
                <select id="estado_civil" v-model="form.estado_civil" required class="input">
                  <option value="">Seleccione</option>
                  <option value="Soltero/a">Soltero/a</option>
                  <option value="Casado/a">Casado/a</option>
                  <option value="Divorciado/a">Divorciado/a</option>
                  <option value="Viudo/a">Viudo/a</option>
                </select>
              </div>
              <div>
                <label for="nacionalidad" class="block text-sm font-medium text-black">Nacionalidad</label>
                <input type="text" id="nacionalidad" v-model="form.nacionalidad" required class="input" />
              </div>
              <div>
                <label for="avatar" class="block text-sm font-medium text-black">Avatar</label>
                <input type="file" id="avatar" @change="onSelectAvatar" class="input" accept="image/*" />
              </div>
              <div>
                <label for="embarcacion_id" class="block text-sm font-medium text-black">Embarcación</label>
                <select id="embarcacion_id" v-model="form.embarcacion_id" class="input">
                  <option value="">Sin asignar</option>
                  <option v-for="embarcacion in embarcaciones" :key="embarcacion.id" :value="embarcacion.id">
                    {{ embarcacion.nombre }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Contacto -->
          <div>
            <h3 class="text-lg font-semibold text-blue-800 border-b pb-2 mb-4">Contacto</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="direccion" class="block text-sm font-medium text-black">Domicilio</label>
                <input type="text" id="direccion" v-model="form.direccion" required class="input" />
              </div>
              <div>
                <label for="comuna" class="block text-sm font-medium text-black">Comuna</label>
                <input type="text" id="comuna" v-model="form.comuna" required class="input" />
              </div>
              <div>
                <label for="email" class="block text-sm font-medium text-black">Email</label>
                <input type="email" id="email" v-model="form.email" required class="input" :class="{'border-red-500': form.errors.email}" />
                <p v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</p>
              </div>
              <div>
                <label for="telefono" class="block text-sm font-medium text-black">Número de Contacto</label>
                <input type="text" id="telefono" v-model="form.telefono" required class="input" placeholder="+569..." />
              </div>
            </div>
          </div>

          <!-- Información Laboral -->
          <div>
            <h3 class="text-lg font-semibold text-blue-800 border-b pb-2 mb-4">Información Laboral</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="afp" class="block text-sm font-medium text-black">AFP</label>
                <input type="text" id="afp" v-model="form.afp" required class="input" />
              </div>
              <div>
                <label for="cargo" class="block text-sm font-medium text-black">Cargo</label>
                <input type="text" id="cargo" v-model="form.cargo" required class="input" />
              </div>
              <div>
                <label for="tamaño_ropa" class="block text-sm font-medium text-black">Talla de Ropa</label>
                <input type="text" id="tamaño_ropa" v-model="form.tamaño_ropa" required class="input" />
              </div>
              <div>
                <label for="tipo_contrato" class="block text-sm font-medium text-black">Tipo de Contrato</label>
                <input type="text" id="tipo_contrato" v-model="form.tipo_contrato" required class="input" />
              </div>
              <div>
                <label for="sueldo_real" class="block text-sm font-medium text-black">Sueldo Real</label>
                <input type="number" id="sueldo_real" v-model="form.sueldo_real" required class="input" />
              </div>
              <div>
                <label for="sueldo_liquidacion" class="block text-sm font-medium text-black">Sueldo por Liquidación</label>
                <input type="number" id="sueldo_liquidacion" v-model="form.sueldo_liquidacion" required class="input" />
              </div>
            </div>
          </div>

          <!-- Botón -->
          <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition" :disabled="form.processing">
              Crear trabajador
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppMain>
</template>

<style scoped>
.input {
  @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black;
}
</style>
