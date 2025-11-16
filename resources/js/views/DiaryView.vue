<template>
  <v-app-bar color="primary" density="compact">
    <v-container>
      <v-app-bar-title>
        <div class="d-flex align-center">
          Application Bar
          <v-spacer />
          <v-menu>
          <template v-slot:activator="{ props }">
            <v-btn icon v-bind="props">
              <v-icon :icon="mdiMenu" />
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
        <v-row class="border-t-lg pt-2 pb-2" no-gutters>
          <v-icon :icon="mdiWeatherSunset"/>朝食
          <v-spacer/>
          <v-icon :icon="mdiPlus" />
        </v-row>
      </div>
      <div @click="addDiary('Lunch')">
        <v-row class="border-t-lg pt-2 pb-2" no-gutters>
          <v-icon :icon="mdiWhiteBalanceSunny" />昼食
          <v-spacer />
          <v-icon :icon="mdiPlus" />
        </v-row>
      </div>
      <div @click="addDiary('Dinner')">
        <v-row class="border-t-lg pt-2 pb-2" no-gutters>
          <v-icon :icon="mdiWeatherNight" />夕食
          <v-spacer />
          <v-icon :icon="mdiPlus" />
        </v-row>
      </div>
      <div @click="addDiary('Snack')">
        <v-row class="border-t-lg pt-2 pb-2" no-gutters>
          <v-icon :icon="mdiFoodForkDrink" />間食
          <v-spacer />
          <v-icon :icon="mdiPlus" />
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
import { logout, getDiary } from '@/utils/client';
import { useUserStore } from '@/stores/user';
import { mdiMenu, mdiWeatherSunset, mdiPlus, mdiWhiteBalanceSunny, mdiWeatherNight, mdiFoodForkDrink, mdiTarget, mdiFood, mdiLogout } from '@mdi/js';

const userStore = useUserStore();

const diaries = ref(null);

const items = [
  {
    title: '目標',
    value: 'target',
    props: { prependIcon: mdiTarget },
  },
  {
    title: '食品の登録',
    value: 'food',
    props: { prependIcon: mdiFood },
  },
  {
    title: 'ログアウト',
    value: 'logout',
    props: { prependIcon: mdiLogout },
  },
];


const onMenuSelect = async (val) => {
  switch (val.id) {
    case 'logout':
      await logout();
      userStore.logout();
      router.push({ name: 'login'});
      break;
    case 'food':
      router.push({ name: 'food', params: { id:  0 } });
      break;
  }
}

onMounted(async () => {
  diaries.value = await getDiary();
});

</script>
