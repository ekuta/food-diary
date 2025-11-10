import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useStateStore = defineStore('state', () => {
  const loading = ref(false);
  return { loading };
});

