import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useStateStore = defineStore('state', () => {
  const loading = ref(false);
  const diary = ref(null);
  const mextFood = ref(null);
  const selectFood = ref(null);
  return { loading, diary, mextFood, selectFood };
}, {persist: true});
