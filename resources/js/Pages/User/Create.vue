<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import AppMain from '@/Layouts/AppMain.vue'
import { ref } from 'vue'

const props = defineProps({
  roles: Array,
  
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: '', // opcional
})

const submit = () => {
  form.post(route('usuarios.store'), {
    onSuccess: () => router.visit(route('usuarios.index')),
  })
}
</script>

<template>
  <Head title="Crear Usuario" />

  <AppMain>
    <div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow mt-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">👤 Crear Nuevo Usuario</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700">Nombre</label>
          <input v-model="form.name" type="text" class="input" />
          <p v-if="form.errors.name" class="error">{{ form.errors.name }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input v-model="form.email" type="email" class="input" />
          <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Contraseña</label>
          <input v-model="form.password" type="password" class="input" />
          <p v-if="form.errors.password" class="error">{{ form.errors.password }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
          <input v-model="form.password_confirmation" type="password" class="input" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Rol</label>
          <select v-model="form.role" class="input">
            <option value="">Seleccionar Rol</option>
            <option v-for="role in roles" :key="role" :value="role">
              {{ role }}
            </option>
          </select>
          <p v-if="form.errors.role" class="error">{{ form.errors.role }}</p>
        </div>

        <div>
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-md shadow hover:bg-blue-700 transition disabled:opacity-50"
          >
            {{ form.processing ? 'Creando...' : 'Crear Usuario' }}
          </button>
        </div>
      </form>
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
