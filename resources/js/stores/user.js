import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useUserStore = defineStore('user', () => {
  const user = ref(null);

  function login(data) {
    user.value = data;
  }
  function logout() {
    user.value = null;
  }
  function isLogin() {
    return user.value != null;
  }
  return { user, login, logout, isLogin }
}, {persist: true});

