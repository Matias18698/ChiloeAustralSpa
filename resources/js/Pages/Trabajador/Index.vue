<script setup>
import AppMain from '@/Layouts/AppMain.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';


const page = usePage();
const trabajadoresData = page.props.trabajadores || [];

// Estados reactivos
const currentPage = ref(1);
const itemsPerPage = 10;
const searchQuery = ref('');
const showModal = ref(false);
const trabajadorSeleccionado = ref(null);

// Procesamiento de datos
const trabajadores = ref(
  trabajadoresData.map(({ id, nombre, apellido, cargo, telefono, avatar, rut, fecha_nacimiento, estado_civil, nacionalidad, direccion, comuna, email, afp, tamaño_ropa, tipo_contrato, sueldo_real, sueldo_liquidacion }) => ({
    id, nombre, apellido, cargo, telefono, avatar, rut,
    fecha_nacimiento, estado_civil, nacionalidad, direccion, comuna, embarcacion_id: null,
    email, afp, tamaño_ropa, tipo_contrato, sueldo_real, sueldo_liquidacion
  }))
);


const trabajadoresFiltrados = computed(() => {
  const query = searchQuery.value.toLowerCase().trim();
  if (!query) return trabajadores.value;
  return trabajadores.value.filter(t =>
    t.nombre.toLowerCase().includes(query) ||
    t.apellido.toLowerCase().includes(query) ||
    t.rut.toLowerCase().includes(query)
  );
});

const totalPages = computed(() => Math.ceil(trabajadoresFiltrados.value.length / itemsPerPage));

const trabajadoresPaginados = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return trabajadoresFiltrados.value.slice(start, start + itemsPerPage);
});

// Funciones
const show = (id) => {
  trabajadorSeleccionado.value = trabajadores.value.find(t => t.id === id);
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  trabajadorSeleccionado.value = null;
};

const handleDelete = (id) => {
  if (confirm('¿Estás seguro de eliminar a este trabajador?')) {
    router.delete(route('trabajador.destroy', id), {
      preserveScroll: true,
      onSuccess: () => {
        trabajadores.value = trabajadores.value.filter(t => t.id !== id);
      },
      onError: error => {
        console.error('Error al eliminar:', error);
        alert('Error al eliminar el trabajador.');
      },
    });
  }
};


const redirectToCreate = () => {
  router.visit(route('trabajador.create'));
};

const formatCurrency = (value) =>
  new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP' }).format(value);

const formatDate = (fecha) => {
  if (!fecha) return '';
  return new Date(fecha).toLocaleDateString('es-CL');
};

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};
</script>

<template>
  <Head title="Trabajadores" />
  <AppMain>
    <div class="container">

      <!-- Header & button -->
      <header class="header">
        <h2 class="title"> Registro de Trabajadores</h2>
        <button @click="redirectToCreate" class="btn-primary">
          ➕ Nuevo Trabajador
        </button>
      </header>

      <!-- Search -->
      <div class="search-container">
        <input
          v-model="searchQuery"
          type="search"
          placeholder="Buscar por nombre o RUT..."
          aria-label="Buscar trabajadores"
          class="search-input"
        />
      </div>

      <!-- Table container -->
      <div class="table-wrapper" role="region" aria-live="polite" aria-label="Lista de trabajadores">
        <table class="trabajadores-table">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>RUT</th>
              <th>Cargo</th>
              <th>Teléfono</th>
              <th>Email</th>
              <th>Tipo Contrato</th>
              <th>Foto</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="trabajador in trabajadoresPaginados" :key="trabajador.id" class="table-row">
              <td data-label="Nombre">{{ trabajador.nombre }} {{ trabajador.apellido }}</td>
              <td data-label="RUT">{{ trabajador.rut }}</td>
              <td data-label="Cargo">{{ trabajador.cargo }}</td>
              <td data-label="Teléfono">{{ trabajador.telefono }}</td>
              <td data-label="Email">{{ trabajador.email }}</td>
              <td data-label="Tipo Contrato">{{ trabajador.tipo_contrato }}</td>
              <td data-label="Foto">
                <img
                  :src="`/storage/${trabajador.avatar}`"
                  alt="Avatar de {{ trabajador.nombre }}"
                  class="avatar"
                  loading="lazy"
                />
                </td>
                <td class="px-5 py-3 whitespace-nowrap text-center space-x-1">
                  <button @click="router.visit(route('trabajador.edit', trabajador.id))"
                    class="text-indigo-600 hover:text-indigo-900 font-semibold">✏️</button>
                  <button @click="handleDelete(trabajador.id)"
                    class="text-red-600 hover:text-red-900 font-semibold">🗑️</button>
                  <button @click="show(trabajador.id)" class="text-green-600 hover:text-green-900 font-semibold">👁️</button>
                </td>
          </tr>

            <tr v-if="trabajadoresPaginados.length === 0">
              <td colspan="8" class="no-results">No se encontraron trabajadores.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <nav class="pagination" role="navigation" aria-label="Paginación de trabajadores">
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
          class="btn-page"
          aria-disabled="currentPage === 1"
          aria-label="Página anterior"
        >
          ⬅️ Anterior
        </button>

        <template v-for="page in totalPages" :key="page">
          <button
            @click="goToPage(page)"
            :class="['btn-page', { 'btn-page-active': page === currentPage }]"
            :aria-current="page === currentPage ? 'page' : false"
          >
            {{ page }}
          </button>
        </template>

        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="btn-page"
          aria-disabled="currentPage === totalPages"
          aria-label="Página siguiente"
        >
          Siguiente ➡️
        </button>
      </nav>

    </div>

    <!-- Modal Detalle -->
    <transition name="fade">
      <div v-if="showModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-content">
          <h2 id="modal-title" class="modal-title">👁️ Detalles del Trabajador</h2>
          <div v-if="trabajadorSeleccionado" class="modal-body">
            <div class="modal-avatar-wrapper">
              <img
                v-if="trabajadorSeleccionado.avatar"
                :src="`/storage/${trabajadorSeleccionado.avatar}`"
                :alt="`Avatar de ${trabajadorSeleccionado.nombre}`"
                class="modal-avatar"
                loading="lazy"
              />
            </div>
            <dl class="modal-details">
              <div><dt>Nombre:</dt><dd>{{ trabajadorSeleccionado.nombre }} {{ trabajadorSeleccionado.apellido }}</dd></div>
              <div><dt>RUT:</dt><dd>{{ trabajadorSeleccionado.rut }}</dd></div>
              <div><dt>Fecha de Nacimiento:</dt><dd>{{ formatDate(trabajadorSeleccionado.fecha_nacimiento) }}</dd></div>
              <div><dt>Estado Civil:</dt><dd>{{ trabajadorSeleccionado.estado_civil }}</dd></div>
              <div><dt>Nacionalidad:</dt><dd>{{ trabajadorSeleccionado.nacionalidad }}</dd></div>
              <div><dt>Dirección:</dt><dd>{{ trabajadorSeleccionado.direccion }}</dd></div>
              <div><dt>Comuna:</dt><dd>{{ trabajadorSeleccionado.comuna }}</dd></div>
              <div><dt>Email:</dt><dd>{{ trabajadorSeleccionado.email }}</dd></div>
              <div><dt>Teléfono:</dt><dd>{{ trabajadorSeleccionado.telefono }}</dd></div>
              <div><dt>AFP:</dt><dd>{{ trabajadorSeleccionado.afp }}</dd></div>
              <div><dt>Cargo:</dt><dd>{{ trabajadorSeleccionado.cargo }}</dd></div>
              <div><dt>Tamaño de Ropa:</dt><dd>{{ trabajadorSeleccionado.tamaño_ropa }}</dd></div>
              <div><dt>Tipo de Contrato:</dt><dd>{{ trabajadorSeleccionado.tipo_contrato }}</dd></div>
              <div><dt>Sueldo Real:</dt><dd>{{ formatCurrency(trabajadorSeleccionado.sueldo_real) }}</dd></div>
              <div><dt>Sueldo Liquidación:</dt><dd>{{ formatCurrency(trabajadorSeleccionado.sueldo_liquidacion) }}</dd></div>
            </dl>
          </div>
          <button @click="closeModal" class="btn-close-modal" aria-label="Cerrar detalles trabajador">Cerrar</button>
        </div>
      </div>
    </transition>
  </AppMain>
</template>

<style scoped>
/* Container */
.container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 1rem 1rem 3rem;
}

/* Header */
.header {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  gap: 1rem;
}

.title {
  font-size: 1.9rem;
  color: #1e40af; /* blue-800 */
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-primary {
  background-color: #1e40af;
  color: #fff;
  padding: 0.5rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  border: none;
  transition: background-color 0.25s ease;
  white-space: nowrap;
}

.btn-primary:hover,
.btn-primary:focus {
  background-color: #1e3a8a;
  outline: none;
}

/* Search */
.search-container {
  margin-bottom: 1.5rem;
}

.search-input {
  width: 100%;
  max-width: 350px;
  padding: 0.5rem 1rem;
  border: 2px solid #93c5fd; /* blue-300 */
  border-radius: 0.5rem;
  font-size: 1rem;
  outline-offset: 2px;
  transition: border-color 0.3s ease;
}

.search-input:focus {
  border-color: #3b82f6; /* blue-500 */
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

/* Table wrapper */
.table-wrapper {
  overflow-x: auto;
  border-radius: 0.75rem;
  border: 1px solid #bfdbfe; /* blue-200 */
  background-color: #fff;
  box-shadow: 0 2px 8px rgb(0 0 0 / 0.05);
}

/* Table */
.trabajadores-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
  color: #374151; /* gray-700 */
}

.trabajadores-table thead {
  background-color: #eff6ff; /* blue-50 */
  color: #1e40af; /* blue-800 */
}

.trabajadores-table th,
.trabajadores-table td {
  padding: 0.75rem 1rem;
  text-align: left;
  vertical-align: middle;
  border-bottom: 1px solid #e0e7ff;
}

.trabajadores-table th {
  font-weight: 600;
  font-size: 1rem;
}

.table-row:hover {
  background-color: #dbf4ff; /* light blue */
  transition: background-color 0.25s ease;
}

/* Avatar */
.avatar {
  width: 3rem;
  height: 3rem;
  border-radius: 9999px;
  object-fit: cover;
  border: 1.5px solid #bfdbfe;
  box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
}

/* Actions */
.actions-cell {
  display: flex;
  gap: 0.4rem;
  flex-wrap: nowrap;
}

.actions-cell button {
  border: none;
  cursor: pointer;
  font-size: 1.1rem;
  padding: 0.3rem 0.5rem;
  border-radius: 0.375rem;
  color: white;
  transition: background-color 0.25s ease;
}

.btn-edit {
  background-color: #3b82f6; /* blue-500 */
}

.btn-edit:hover,
.btn-edit:focus {
  background-color: #2563eb; /* blue-600 */
  outline: none;
}

.btn-delete {
  background-color: #ef4444; /* red-500 */
}

.btn-delete:hover,
.btn-delete:focus {
  background-color: #dc2626; /* red-600 */
  outline: none;
}

.btn-view {
  background-color: #10b981; /* green-500 */
}

.btn-view:hover,
.btn-view:focus {
  background-color: #059669; /* green-600 */
  outline: none;
}

/* No results */
.no-results {
  text-align: center;
  color: #6b7280; /* gray-500 */
  padding: 1rem;
}

/* Pagination */
.pagination {
  margin-top: 1.75rem;
  display: flex;
  justify-content: center;
  gap: 0.35rem;
  flex-wrap: wrap;
}

.btn-page {
  cursor: pointer;
  border: 1.5px solid #3b82f6; /* blue-500 */
  background-color: transparent;
  color: #2563eb; /* blue-600 */
  font-weight: 600;
  border-radius: 0.375rem;
  padding: 0.4rem 0.8rem;
  transition: background-color 0.3s ease, color 0.3s ease;
  min-width: 2.5rem;
  text-align: center;
  user-select: none;
}

.btn-page:hover:not(:disabled),
.btn-page:focus:not(:disabled) {
  background-color: #bfdbfe; /* blue-200 */
  outline: none;
}

.btn-page:disabled {
  cursor: not-allowed;
  opacity: 0.4;
}

.btn-page-active {
  background-color: #2563eb; /* blue-600 */
  color: white;
  box-shadow: 0 0 6px rgba(37, 99, 235, 0.5);
}

/* Modal backdrop */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background-color: rgba(0,0,0,0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
  overflow-y: auto;
}

/* Modal content */
.modal-content {
  background-color: white;
  border-radius: 1rem;
  max-width: 480px;
  width: 100%;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  padding: 1.5rem 1.75rem 2rem;
  display: flex;
  flex-direction: column;
  max-height: 90vh;
  overflow-y: auto;
}

/* Modal Title */
.modal-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1e40af;
  margin-bottom: 1rem;
  text-align: center;
}

/* Modal avatar */
.modal-avatar-wrapper {
  display: flex;
  justify-content: center;
  margin-bottom: 1.25rem;
}

.modal-avatar {
  width: 7rem;
  height: 7rem;
  border-radius: 9999px;
  object-fit: cover;
  border: 3px solid #bfdbfe;
  box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4);
}

/* Modal details */
.modal-details {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 0.4rem 1rem;
  font-size: 0.95rem;
  color: #374151;
  margin-bottom: 1.5rem;
}

.modal-details dt {
  font-weight: 600;
  color: #1e40af;
}

.modal-details dd {
  margin: 0;
  word-break: break-word;
}

/* Modal close button */
.btn-close-modal {
  background-color: #1e40af;
  color: white;
  font-weight: 600;
  padding: 0.6rem 1.25rem;
  border-radius: 0.5rem;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
  align-self: stretch;
  text-align: center;
}

.btn-close-modal:hover,
.btn-close-modal:focus {
  background-color: #1c3aa1;
  outline: none;
}

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Responsive */

/* Small devices: stack header */
@media (max-width: 640px) {
  .header {
    flex-direction: column;
    align-items: flex-start;
  }

  .btn-primary {
    width: 100%;
    text-align: center;
  }

  .search-input {
    max-width: 100%;
  }

  /* Table: responsive rows */
  .trabajadores-table thead {
    display: none;
  }

  .trabajadores-table, 
  .trabajadores-table tbody, 
  .trabajadores-table tr, 
  .trabajadores-table td {
    display: block;
    width: 100%;
  }

  .trabajadores-table tr {
    margin-bottom: 1rem;
    border-radius: 0.75rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    background-color: white;
    padding: 1rem;
  }

  .trabajadores-table td {
    padding-left: 50%;
    position: relative;
    border: none;
    text-align: left;
    font-size: 0.9rem;
  }

  .trabajadores-table td::before {
    position: absolute;
    top: 50%;
    left: 1rem;
    transform: translateY(-50%);
    font-weight: 700;
    white-space: nowrap;
    color: #2563eb;
    content: attr(data-label);
  }

  .actions-cell {
    justify-content: flex-start;
    gap: 0.75rem;
  }
}

/* Medium devices and up */
@media (min-width: 641px) and (max-width: 900px) {
  .container {
    padding: 1rem 2rem 3rem;
  }

  .avatar {
    width: 2.5rem;
    height: 2.5rem;
  }

  .btn-primary {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
  }

  .trabajadores-table th,
  .trabajadores-table td {
    padding: 0.6rem 0.8rem;
    font-size: 0.9rem;
  }
}
</style>
