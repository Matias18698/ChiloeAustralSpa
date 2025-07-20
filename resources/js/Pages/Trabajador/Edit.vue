<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppMain from '@/Layouts/AppMain.vue';
import { usePage } from '@inertiajs/vue3';

// Obtener el trabajador desde los props
const { trabajador, embarcaciones } = usePage().props;

// Crear el formulario con Inertia.js y los valores iniciales del trabajador
const form = useForm({
    nombre: trabajador.nombre,
    apellido: trabajador.apellido, 
    avatar: trabajador.avatar,
    rut: trabajador.rut,
    fecha_nacimiento: trabajador.fecha_nacimiento,
    estado_civil: trabajador.estado_civil,
    nacionalidad: trabajador.nacionalidad,
    direccion: trabajador.direccion,
    comuna: trabajador.comuna,
    email: trabajador.email,
    telefono: trabajador.telefono,
    afp: trabajador.afp,
    cargo: trabajador.cargo,
    tamaño_ropa: trabajador.tamaño_ropa,
    tipo_contrato: trabajador.tipo_contrato,
    sueldo_real: trabajador.sueldo_real,
    sueldo_liquidacion: trabajador.sueldo_liquidacion,
    embarcacion_id: trabajador.embarcacion_id, 


    id: trabajador.id, // Agregar ID del trabajador para la actualización
});

// Función para enviar el formulario
const submit = () => {
    form.post(route('trabajador.update', trabajador), {
        onSuccess: (e) => {
            trabajador.value = e.props.trabajador;
            console.log('Trabajador actualizado:', trabajador.value);
            window.location.href = route('trabajador.index');
        },
    });
};

// Función para manejar el avatar
const onSelectAvatar = async (event) => {
  const file = event.target.files[0];

  if (!file) return;

  try {
    // Opciones para la compresión
    const options = {
      maxSizeMB: 1,
      maxWidthOrHeight: 300, // Tamaño máximo
      useWebWorker: true,
    };

    // Comprimir la imagen
    const compressedFile = await imageCompression(file, options);

    // Asignar el archivo comprimido al formulario
    form.avatar = compressedFile;

    // Crear URL para la vista previa
    previewUrl.value = URL.createObjectURL(compressedFile);

    // Opcional: para ver el tamaño original y el comprimido
    console.log(`Original: ${file.size / 1024} KB`);
    console.log(`Comprimido: ${compressedFile.size / 1024} KB`);
    
  } catch (error) {
    console.error('Error al comprimir imagen:', error);
  }
};
</script>
<template>
    <Head title="Editar trabajador" />

    <AppMain>
        <div class="w-full bg-gradient-to-b from-blue-50 to-blue-100 min-h-screen p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-blue-800 flex items-center">
                    Editar Trabajador
                </h2>
            </div>

            <div class="overflow-hidden bg-white rounded-xl shadow-lg p-6 border border-blue-200">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Sección: Datos Personales -->
                    <div>
                        <h3 class="text-lg font-semibold text-blue-800 border-b pb-2 mb-4">Datos Personales</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-black">Nombre</label>
                                <input type="text" id="nombre" v-model="form.nombre" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="apellido" class="block text-sm font-medium text-black">Apellido</label>
                                <input type="text" id="apellido" v-model="form.apellido" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="rut" class="block text-sm font-medium text-black">RUT</label>
                                <input type="text" id="rut" v-model="form.rut" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black" placeholder="20.520.354-4"/>
                            </div>
                            <div>
                                <label for="fecha_nacimiento" class="block text-sm font-medium text-black">Fecha de Nacimiento</label>
                                <input type="date" id="fecha_nacimiento" v-model="form.fecha_nacimiento" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="estado_civil" class="block text-sm font-medium text-black">Estado Civil</label>
                                <select id="estado_civil" v-model="form.estado_civil" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black">
                                    <option value="">Seleccione</option>
                                    <option value="Soltero/a">Soltero/a</option>
                                    <option value="Casado/a">Casado/a</option>
                                    <option value="Divorciado/a">Divorciado/a</option>
                                    <option value="Viudo/a">Viudo/a</option>
                                </select>
                            </div>
                            <div>
                                <label for="nacionalidad" class="block text-sm font-medium text-black">Nacionalidad</label>
                                <input type="text" id="nacionalidad" v-model="form.nacionalidad" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            
                            
                                <div>
                                <label for="embarcacion_id" class="block text-sm font-medium text-black">Embarcación</label>
                                <select
                                    id="embarcacion_id"
                                    v-model="form.embarcacion_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"
                                >
                                    <option value="">Sin asignar</option>
                                    <option v-for="embarcacion in embarcaciones" :key="embarcacion.id" :value="embarcacion.id">
                                    {{ embarcacion.nombre }}
                                    </option>
                                </select>
                                </div>

                                <!-- Sección: Avatar -->
                                <div>
                                    <label for="avatar" class="block text-sm font-medium text-black">Avatar</label>
                                    <input type="file" id="avatar" @change="onSelectAvatar" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black" accept="image/*"/>

                                
                                </div>

                        </div>
                    </div>

                    <!-- Sección: Contacto -->
                    <div>
                        <h3 class="text-lg font-semibold text-blue-800 border-b pb-2 mb-4">Contacto</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="direccion" class="block text-sm font-medium text-black">Domicilio</label>
                                <input type="text" id="direccion" v-model="form.direccion" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="comuna" class="block text-sm font-medium text-black">Comuna</label>
                                <input type="text" id="comuna" v-model="form.comuna" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-black">Email</label>
                                <input type="email" id="email" v-model="form.email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="telefono" class="block text-sm font-medium text-black">Número de Contacto</label>
                                <input type="text" id="telefono" v-model="form.telefono" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black" placeholder="+56988245789"/>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Información Laboral -->
                    <div>
                        <h3 class="text-lg font-semibold text-blue-800 border-b pb-2 mb-4">Información Laboral</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="afp" class="block text-sm font-medium text-black">AFP</label>
                                <input type="text" id="afp" v-model="form.afp" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="cargo" class="block text-sm font-medium text-black">Cargo</label>
                                <input type="text" id="cargo" v-model="form.cargo" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="tamaño_ropa" class="block text-sm font-medium text-black">Talla de Ropa</label>
                                <input type="text" id="tamaño_ropa" v-model="form.tamaño_ropa" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="tipo_contrato" class="block text-sm font-medium text-black">Tipo de Contrato</label>
                                <input type="text" id="tipo_contrato" v-model="form.tipo_contrato" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="sueldo_real" class="block text-sm font-medium text-black">Sueldo Real</label>
                                <input type="number" id="sueldo_real" v-model="form.sueldo_real" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
                            </div>
                            <div>
                                <label for="sueldo_liquidacion" class="block text-sm font-medium text-black">Sueldo por Liquidación</label>
                                <input type="number" id="sueldo_liquidacion" v-model="form.sueldo_liquidacion" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-black"/>
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
