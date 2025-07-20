<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import AppMain from '@/Layouts/AppMain.vue'

const props = defineProps({
  user: Object,
  roles: Array,
  availableRoles: Array,
  mustVerifyEmail: Boolean,
  status: String,
})

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  role: props.roles[0] || 'user', // por si tiene más de un rol
})

const submit = () => {
  form.clearErrors()

  form.post(route('usuarios.update', props.user.id), {
    onSuccess: () => router.visit(route('usuarios.index'))
  })
}
</script>

<template>
  <Head title="Editar Usuario" />

  <AppMain>
    <div class="w-full bg-gradient-to-b from-blue-50 to-blue-100 min-h-screen p-6">
      <div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow-lg border border-blue-200">
        <h2 class="text-2xl font-bold text-blue-800 mb-8"> Editar Usuario</h2>

        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nombre</label>
              <input v-model="form.name" type="text" class="input" />
              <p v-if="form.errors.name" class="error">{{ form.errors.name }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Correo electrónico</label>
              <input v-model="form.email" type="email" class="input" />
              <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700">Rol</label>
              <select v-model="form.role" class="input">
                <option v-for="role in availableRoles" :key="role" :value="role">{{ role }}</option>
              </select>
              <p v-if="form.errors.role" class="error">{{ form.errors.role }}</p>
            </div>
          </div>

          <div>
            <button 
              type="submit"
              :disabled="form.processing"
              class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-md shadow hover:bg-blue-700 transition disabled:opacity-50"
            >
              {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
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
