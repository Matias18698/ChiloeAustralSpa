<template>
  <header :class="['app-header', { scrolled: isScrolled }]">
    <nav class="header-container">
      <div class="header-title">Mi Aplicación</div>
      <!-- Puedes añadir aquí íconos o botones para menú móvil -->
    </nav>
  </header>
</template>

<script>
export default {
  name: 'AppHeaderLayout',
  data() {
    return {
      isScrolled: false,
    };
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll);
  },
  beforeUnmount() {
    window.removeEventListener('scroll', this.handleScroll);
  },
  methods: {
    handleScroll() {
      this.isScrolled = window.scrollY > 10;
    },
  },
};
</script>

<style scoped>
.app-header {
  background-color: #1f2937;
  color: white;
  position: sticky;
  top: 0;
  z-index: 50;
  transition: all 0.3s ease;
  padding: 0.75rem 1rem;
  height: 60px; /* Asegura una altura fija */
  max-height: 100vh; /* No dejar que el header sobrepase la pantalla */
  overflow: hidden; /* Evita que el contenido se desborde */
}

.app-header.scrolled {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1280px;
  margin: 0 auto;
}

.header-title {
  font-size: 1.25rem;
  font-weight: 700;
  white-space: nowrap;
}

/* RESPONSIVE TWEAKS */
@media (min-width: 640px) {
  .app-header {
    padding: 1rem 2rem;
  }

  .header-title {
    font-size: 1.5rem;
  }
}

/* DARK MODE SUPPORT */
@media (prefers-color-scheme: dark) {
  .app-header {
    background-color: #111827;
  }

  .app-header.scrolled {
    box-shadow: 0 2px 8px rgba(255, 255, 255, 0.1);
  }
}
</style>
