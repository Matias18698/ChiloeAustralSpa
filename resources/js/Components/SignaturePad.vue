<template>
  <div class="border border-gray-300 rounded-md overflow-hidden">
    <canvas
      ref="canvasRef"
      class="w-full h-48 bg-white touch-auto"
    ></canvas>
  </div>
</template>

<script setup>
import SignaturePad from 'signature_pad'
import { ref, onMounted, defineExpose, onBeforeUnmount } from 'vue'

const canvasRef = ref(null)
let pad = null

onMounted(() => {
  const canvas = canvasRef.value
  pad = new SignaturePad(canvas)

  resizeCanvas()
  window.addEventListener('resize', resizeCanvas)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', resizeCanvas)
})

const clearSignature = () => pad.clear()
const isEmpty = () => pad.isEmpty()
const saveSignature = () => pad.toDataURL()

const resizeCanvas = () => {
  const canvas = canvasRef.value
  const ratio = Math.max(window.devicePixelRatio || 1, 1)
  canvas.width = canvas.offsetWidth * ratio
  canvas.height = canvas.offsetHeight * ratio
  canvas.getContext('2d').scale(ratio, ratio)
  pad.clear()
}

defineExpose({
  clearSignature,
  isEmpty,
  saveSignature,
})
</script>

<style scoped>
canvas {
  display: block;
}
</style>
