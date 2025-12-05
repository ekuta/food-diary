import { defineStore } from 'pinia';
import { ref } from 'vue';
import { useDiaryStore } from '@/stores/diary';
import { useRecipeStore } from '@/stores/recipe';
import { getUser } from '@/utils/client';

export const useUserStore = defineStore('user', () => {
  const CHECK_INTERVAL = 5 * 60 * 1000; // 5 minutes
  let lastCheckTime = 0;
  const user = ref(null);

  function login(data) {
    user.value = data;
  }
  function logout() {
    const diaryStore = useDiaryStore();
    diaryStore.$reset();
    const recipeStore = useRecipeStore();
    recipeStore.$reset();
    user.value = null;
  }
  function isLogin() {
    if (Date.now() > lastCheckTime + CHECK_INTERVAL) {
      getUser();
      lastCheckTime = Date.now();
    }
    return user.value != null;
  }
  return { user, login, logout, isLogin }
}, { persist: { storage: sessionStorage } });

