<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppMain from '@/Layouts/AppMain.vue';
import { usePage } from '@inertiajs/vue3';

// Obtener la embarcación desde los props
const { embarcacion } = usePage().props;

// Crear el formulario con Inertia.js y los valores iniciales de la embarcación
const form = useForm({
    nombre: embarcacion.nombre,
    tipo: embarcacion.tipo,
    patente: embarcacion.patente,
    capacidad: embarcacion.capacidad,
    estado: embarcacion.estado,
    trabajadores: embarcacion.trabajadores ? embarcacion.trabajadores.map(t => t.id) : [], 
});

// Función para enviar el formulario
const submit = () => {
    form.post(route('embarcacion.update', embarcacion), {
        onSuccess: (e) => {
            embarcacion.value = e.props.embarcacion; // Actualiza el ID de la embarcación
            console.log('Embarcación actualizada:', embarcacion.value);
        },
    });
};
</script>

<template>
    <Head title="Editar embarcación" />

    <AppMain>
        <div class="w-full bg-gradient-to-b from-blue-50 to-blue-100 min-h-screen p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-blue-800 flex items-center">
                    <span class="mr-2">🚤</span> Editar Embarcación
                </h2>
            </div>

            <div class="overflow-hidden bg-white rounded-xl shadow-lg p-6 border border-blue-200">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Sección: Información de la embarcación -->
                    <div>
                        <h3 class="text-lg font-semibold text-blue-800 border-b pb-2 mb-4">Información de la Embarcación</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-black">Nombre</label>
                                <input type="text" id="nombre" v-model="form.nombre" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="tipo" class="block text-sm font-medium text-black">Tipo</label>
                                <input type="text" id="tipo" v-model="form.tipo" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="patente" class="block text-sm font-medium text-black">Patente</label>
                                <input type="text" id="patente" v-model="form.patente" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="capacidad" class="block text-sm font-medium text-black">Capacidad</label>
                                <input type="number" id="capacidad" v-model="form.capacidad" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="estado" class="block text-sm font-medium text-black">Estado</label>
                                <select id="estado" v-model="form.estado" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Botón de envío -->
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition" :disabled="form.processing">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppMain>
</template>

<script>
export default {
  props: {
    embarcacion: Object,
    trabajadores: Array,  // Añadir trabajadores como prop
  },
  data() {
    return {
      form: {
        nombre: this.embarcacion.nombre,
        tipo: this.embarcacion.tipo,
        patente: this.embarcacion.patente,
        capacidad: this.embarcacion.capacidad,
        estado: this.embarcacion.estado,
        trabajadores: this.embarcacion.trabajadores ? this.embarcacion.trabajadores.map(t => t.id) : [], // Asignar los trabajadores existentes
      },
    };
  },
  methods: {
    async submitForm() {
      // Enviar la embarcación actualizada y los trabajadores seleccionados
      await this.$inertia.post(route('embarcacion.update', this.embarcacion.id), this.form);
    },
  },
};
</script>

<style scoped>
/* Animación para el modal */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Fondo oscuro con opacidad para el modal */
.bg-opacity-50 {
  background-color: rgba(0, 0, 0, 0.5);
}
</style>
