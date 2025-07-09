<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppMain from '@/Layouts/AppMain.vue'

const props = defineProps({
  users: Array,
  authUser: Object,
})

const password = ref('')

function eliminarUsuario(user) {
  const urlEliminar = route('usuarios.destroy', user.id)

  if (user.id === props.authUser.id) {
    const pwd = prompt('Para eliminar tu cuenta, por favor ingresa tu contraseña:')
    if (!pwd) return
    password.value = pwd

    if (confirm(`¿Estás seguro de que deseas eliminar tu cuenta? Esta acción es irreversible.`)) {
      router.delete(urlEliminar, { password: password.value }, {
        onSuccess: () => router.reload(),
        onError: (errors) => {
          alert("Error al eliminar tu cuenta:\n" + Object.values(errors).join('\n'))
        }
      })
    }
  } else {
    if (confirm(`¿Estás seguro de que deseas eliminar al usuario ${user.name}?`)) {
      router.delete(urlEliminar, {}, {
        onSuccess: () => router.reload(),
        onError: (errors) => {
          alert("Error al eliminar usuario:\n" + Object.values(errors).join('\n'))
        }
      })
    }
  }
}
</script>

<template>
  <Head title="Usuarios" />

  <AppMain>
    <div>
      <div class="max-w-6xl mx-auto bg-white p-6 rounded-2xl shadow-lg border border-blue-200">
        <!-- Título y botón -->
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-blue-800">👤 Usuarios del Sistema</h2>
          <Link
            :href="route('usuarios.create')"
            class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700 transition"
            title="Crear nuevo usuario"
            aria-label="Crear nuevo usuario"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Usuario
          </Link>
        </div>

        <!-- Tabla de usuarios -->
        <table v-if="users.length" class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-100 text-gray-700 text-left">
            <tr>
              <th class="px-4 py-2">Nombre</th>
              <th class="px-4 py-2">Correo</th>
              <th class="px-4 py-2">Roles</th>
              <th class="px-4 py-2">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="user in users" :key="`user-${user.id}`">
              <td class="px-4 py-2">{{ user.name }}</td>
              <td class="px-4 py-2">{{ user.email }}</td>
              <td class="px-4 py-2">
                <span
                  v-for="role in user.roles"
                  :key="`role-${role.id}-${user.id}`"
                  class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs mr-1"
                >
                  {{ role.name }}
                </span>
              </td>
              <td class="px-4 py-2 flex space-x-3">
                <Link
                  :href="route('usuarios.edit', user.id)"
                  class="inline-flex items-center text-blue-600 hover:underline px-3 py-1 rounded-md hover:bg-blue-100 transition"
                  title="Editar usuario"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 11l6 6m0 0l-3 3m3-3L9 11" />
                  </svg>
                  Editar
                </Link>
                <button
                  @click="eliminarUsuario(user)"
                  class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 flex items-center transition"
                  title="Eliminar usuario"
                >
                  🗑️
                  <span class="ml-1">Eliminar</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Mensaje si no hay usuarios -->
        <div v-else class="text-center text-gray-500 py-10">
          No hay usuarios registrados aún.
        </div>
        <!-- 📌 Paginación Mejorada -->
<div class="mt-8 flex justify-center items-center space-x-1 text-sm sm:text-base font-medium">
  <!-- Botón Anterior -->
  <button
    @click="goToPage(currentPage - 1)"
    :disabled="currentPage === 1"
    class="flex items-center px-3 py-1.5 rounded-md border border-blue-500 text-blue-500 hover:bg-blue-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
  >
    <span class="mr-1">⬅️</span> Anterior
  </button>

  <!-- Páginas -->
  <template v-for="page in totalPages" :key="page">
    <button
      @click="goToPage(page)"
      :class="[
        'px-3 py-1.5 rounded-md transition',
        page === currentPage
          ? 'bg-blue-600 text-white shadow-md'
          : 'border border-blue-300 text-blue-600 hover:bg-blue-100'
      ]"
      class="min-w-[2.5rem]"
    >
      {{ page }}
    </button>
  </template>

  <!-- Botón Siguiente -->
  <button
    @click="goToPage(currentPage + 1)"
    :disabled="currentPage === totalPages"
    class="flex items-center px-3 py-1.5 rounded-md border border-blue-500 text-blue-500 hover:bg-blue-100 transition disabled:opacity-50 disabled:cursor-not-allowed"
  >
    Siguiente <span class="ml-1">➡️</span>
  </button>
</div>
<!-- Fin Paginación -->

      </div>
    </div>
  </AppMain>
</template>
