import { defineStore } from 'pinia';

export const useToastStore = defineStore('toast', {
  state: () => ({
    toasts: [],
  }),
  actions: {
    add(toast) {
      const id = Date.now() + Math.random();
      this.toasts.push({ id, ...toast });

      // Auto remove after 4s
      setTimeout(() => {
        this.remove(id);
      }, 4000);
    },
    remove(id) {
      this.toasts = this.toasts.filter(t => t.id !== id);
    },
  },
});
