<template>
  <v-app-bar color="primary" density="compact" width="1024px">
    <v-container>
    <v-app-bar-title>
      <div class="d-flex align-center">
        Application Bar
        <v-spacer />
        <v-menu>
        <template v-slot:activator="{ props }">
          <v-btn icon v-bind="props">
            <v-icon small>mdi-menu</v-icon>
          </v-btn>
        </template>
        <v-list :items="items" @click:select="onMenuSelect">
        </v-list>
      </v-menu>
    </div>
    </v-app-bar-title>
    </v-container>
  </v-app-bar>

  <v-main>
    <v-container>
      <div @click="addDiary('Breakfirst')">
        <v-row class="border-t-lg" no-gutters>
          <v-icon small>mdi-weather-sunset</v-icon>朝食
          <v-spacer/>
          <v-icon small>mdi-plus</v-icon>
        </v-row>
      </div>
      <div @click="addDiary('Lunch')">
        <v-row class="border-t-lg" no-gutters>
          <v-icon small>mdi-white-balance-sunny</v-icon>昼食
          <v-spacer />
          <v-icon small>mdi-plus</v-icon>
        </v-row>
      </div>
      <div @click="addDiary('Dinner')">
        <v-row class="border-t-lg" no-gutters>
          <v-icon small>mdi-weather-night</v-icon>夕食
          <v-spacer />
          <v-icon small>mdi-plus</v-icon>
        </v-row>
      </div>
      <div @click="addDiary('Snack')">
        <v-row class="border-t-lg" no-gutters>
          <v-icon small>mdi-food-fork-drink</v-icon>間食
          <v-spacer />
          <v-icon small>mdi-plus</v-icon>
        </v-row>
      </div>
    </v-container>
  </v-main>
  <v-footer app height="30px" class="bg-blue-lighten-5">
    <v-container class="position-relative">
  Footer
    <v-fab
      class="tw:-top-12 tw:right-3 position-absolute"
      icon="mdi-plus"
    ></v-fab>
    </v-container>
  </v-footer>

</template>

<script setup>
import router from '@/router';
import { ref, onMounted } from 'vue';
import { logout, getDiary } from '@/api/client';
import { useUserStore } from '@/stores/user';

const userStore = useUserStore();

const diaries = ref(null);

const items = [
  {
    title: '目標',
    value: 'target',
    props: { prependIcon: 'mdi-target' },
  },
  {
    title: 'ログアウト',
    value: 'logout',
    props: { prependIcon: 'mdi-logout' },
  },
];


const onMenuSelect = async (val) => {
  switch (val.id) {
    case 'logout':
      await logout();
      userStore.logout();
      router.push({ name: 'login'});
      break;
  }
}

onMounted(async () => {
  diaries.value = await getDiary();
});

</script>
