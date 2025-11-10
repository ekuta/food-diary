import './bootstrap';

import { createApp } from "vue";
import { createPinia } from 'pinia';
import { createPersistedState } from "pinia-plugin-persistedstate";
import App from "./App.vue";
import router from './router';
import 'vuetify/styles';
import { createVuetify } from 'vuetify';
import '@mdi/font/css/materialdesignicons.css';

const app = createApp(App);

app.use(router);

const pinia = createPinia();
pinia.use(createPersistedState());
app.use(pinia);

const myTheme = {
  dark: false,
  colors: {
    primary: '#3f51b5',
    secondary: '#2196f3',
    accent: '#00bcd4',
    error: '#ff5722',
    warning: '#ffeb3b',
    info: '#cddc39',
    success: '#03a9f4'
  }
};

const vuetify = createVuetify({
  theme: {
    defaultTheme: 'myTheme',
    themes: {
      myTheme,
    }
  }
});
app.use(vuetify);

app.mount("#app");
