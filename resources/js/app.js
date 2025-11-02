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

const vuetify = createVuetify();
app.use(vuetify);

app.mount("#app");
