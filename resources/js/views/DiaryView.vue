<template>
  <v-app-bar color="primary" density="compact">
    <v-container max-width="960">
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
    <v-container max-width="960">
      <v-row class="border-t-lg pt-2 pb-2" no-gutters>
        <v-icon :icon="mdiWeatherSunset"/>朝食
        <v-spacer/>
        <v-btn color="primary" :prepend-icon="mdiNotebookPlusOutline" variant="tonal"
          @click="addRecipe('Breakfirst')">レシピ追加</v-btn>
        <v-btn color="primary" :prepend-icon="mdiFood" variant="tonal" class="mx-1"
          @click="addFood('Breakfirst')">食品追加</v-btn>
      </v-row>
      <v-row class="border-t-lg pt-2 pb-2" no-gutters>
        <v-icon :icon="mdiWhiteBalanceSunny" />昼食
        <v-spacer />
        <v-icon :icon="mdiPlus" />
      </v-row>
      <v-row class="border-t-lg pt-2 pb-2" no-gutters>
        <v-icon :icon="mdiWeatherNight" />夕食
        <v-spacer />
        <v-icon :icon="mdiPlus" />
      </v-row>
      <v-row class="border-t-lg pt-2 pb-2" no-gutters>
        <v-icon :icon="mdiFoodForkDrink" />間食
        <v-spacer />
        <v-icon :icon="mdiPlus" />
      </v-row>
    </v-container>
  </v-main>
  <v-footer app height="30px" class="bg-blue-lighten-5">
    <v-container max-width="960" class="position-relative">
  Footer
    <v-fab
      class="tw:-top-12 tw:right-3 position-absolute"
      :icon="mdiPlus"
    ></v-fab>
    </v-container>
  </v-footer>

</template>

<script setup>
import router from '@/router';
import { useRoute } from 'vue-router';
import { useUserStore } from '@/stores/user';
import { useStateStore } from '@/stores/state';
import { ref, onMounted } from 'vue';
import { logout, getDiary } from '@/utils/client';
import { getDate, formatDate, getMealTypeName } from '@/utils';
import { mdiMenu, mdiWeatherSunset, mdiPlus, mdiWhiteBalanceSunny, mdiWeatherNight, mdiFoodForkDrink, mdiTarget,
  mdiFood, mdiLogout, mdiNotebookPlusOutline } from '@mdi/js';

const route = useRoute();
const userStore = useUserStore();
const stateStore = useStateStore();

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
      router.push({ name: 'register-food', params: { id:  0 } });
      break;
  }
}

const addRecipe = (mealType) => {
  router.push({ name: 'select recipe', params: { date: route.params.date, mealType: mealType } });
}

const addFood = (mealType) => {
  stateStore.selectFood = {
    title: formatDate(getDate(route.params.date), 'm月d日(w) ') + getMealTypeName(mealType) + 'の追加',
    date: route.params.date,
    mealType: mealType,
    recipe_id: 0,
    items: [],
  };
  router.push({ name: 'edit diary', params: { date: route.params.date, mealType: mealType } });
}

onMounted(async () => {
  stateStore.diary = await getDiary(route.params.date);
});

</script>
