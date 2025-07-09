<script setup>
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import AppMain from '@/Layouts/AppMain.vue';

const gasto = usePage().props.gasto;

const form = useForm({
  cotizacion: gasto.cotizacion || '',
  centro_barco: gasto.centro_barco || '',
  orden_compra: gasto.orden_compra || '',
  fecha_oc: gasto.fecha_oc || '',
  hes: gasto.hes || '',
  fecha_hes: gasto.fecha_hes || '',
  valor_neto: gasto.valor_neto || '',
  empresa_facturar: gasto.empresa_facturar || '',
  factura: gasto.factura || '',
  estado: gasto.estado || 'pendiente',
  valor_facturado: gasto.valor_facturado || '', 
  
});

const submit = () => {
  form.clearErrors(); // Limpia errores previos

  // Campos requeridos con mensajes personalizados
  const requiredFields = {
    cotizacion: 'La cotización es obligatoria.',
    centro_barco: 'El centro-barco es obligatorio.',
    orden_compra: 'La orden de compra es obligatoria.',
    fecha_oc: 'La fecha de OC es obligatoria.',
    hes: 'El HES es obligatorio.',
    fecha_hes: 'La fecha del HES es obligatoria.',
    valor_neto: 'El valor neto es obligatorio.',
    empresa_facturar: 'La empresa a facturar es obligatoria.',
    factura: 'La factura es obligatoria.',
    estado: 'El estado es obligatorio.'
  };

  // Revisión de campos requeridos (valida también si es solo espacios)
  for (const [field, message] of Object.entries(requiredFields)) {
    const value = form[field];
    if (value === null || value === undefined || String(value).trim() === '') {
      form.errors[field] = message;
    }
  }

  // Validación especial para estado "pagada"
  if (form.estado === 'pagada') {
    const valor = parseFloat(form.valor_facturado);
    if (isNaN(valor) || valor <= 0) {
      form.errors.valor_facturado = 'El valor facturado debe ser mayor a 0 cuando el estado es "pagada".';
    }
  }

  // Detener envío si hay errores
  if (Object.keys(form.errors).length > 0) return;

  // Enviar el formulario
  form.post(route('gastos.update', gasto.id), {
    onSuccess: () => router.visit(route('gastos.index')),
  });
};


</script>

<template>
  <Head title="Editar Gasto" />

  <AppMain>
    <div class="w-full bg-gradient-to-b from-blue-50 to-blue-100 min-h-screen p-6">
      <div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow-lg border border-blue-200">
        <h2 class="text-2xl font-bold text-blue-800 mb-8">Editar Gasto</h2>

        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">Cotización</label>
              <input v-model="form.cotizacion" type="text" class="input" />
              <p v-if="form.errors.cotizacion" class="error">{{ form.errors.cotizacion }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Centro‑Barco</label>
              <input v-model="form.centro_barco" type="text" class="input" />
              <p v-if="form.errors.centro_barco" class="error">{{ form.errors.centro_barco }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Orden de Compra</label>
              <input v-model="form.orden_compra" type="text" class="input" />
              <p v-if="form.errors.orden_compra" class="error">{{ form.errors.orden_compra }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Fecha OC</label>
              <input v-model="form.fecha_oc" type="date" class="input" />
              <p v-if="form.errors.fecha_oc" class="error">{{ form.errors.fecha_oc }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">HES</label>
              <input v-model="form.hes" type="text" class="input" />
              <p v-if="form.errors.hes" class="error">{{ form.errors.hes }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Fecha HES</label>
              <input v-model="form.fecha_hes" type="date" class="input" />
              <p v-if="form.errors.fecha_hes" class="error">{{ form.errors.fecha_hes }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Valor Neto</label>
              <input v-model="form.valor_neto" type="number" step="0.01" class="input" />
              <p v-if="form.errors.valor_neto" class="error">{{ form.errors.valor_neto }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Valor Facturado</label>
              <input v-model="form.valor_facturado" type="number" step="0.01" class="input" />
              <p v-if="form.errors.valor_facturado" class="error">{{ form.errors.valor_facturado }}</p>
            </div>


            <div>
              <label class="block text-sm font-medium text-gray-700">Empresa a Facturar</label>
              <input v-model="form.empresa_facturar" type="text" class="input" />
              <p v-if="form.errors.empresa_facturar" class="error">{{ form.errors.empresa_facturar }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700">Factura</label>
              <input v-model="form.factura" type="text" class="input" />
              <p v-if="form.errors.factura" class="error">{{ form.errors.factura }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700">Estado</label>
              <select v-model="form.estado" class="input">
                <option value="pendiente">Pendiente</option>
                <option value="pagada">Pagada</option>
              </select>
              <p v-if="form.errors.estado" class="error">{{ form.errors.estado }}</p>
            </div>
          </div>

          <div>
            <button 
              type="submit"
              :disabled="form.processing"
              class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-md shadow hover:bg-blue-700 transition disabled:opacity-50"
            >
              {{ form.processing ? 'Guardando cambios...' : 'Actualizar Estado' }}
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
