import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useStateStore = defineStore('state', () => {
  const loading = ref(false);
  const mextFood = ref(null);
  return { loading, mextFood };
});
