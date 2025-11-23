<template>
  <v-app-bar color="primary" density="compact" extension-height="80">
    <v-container max-width="960">
      <v-app-bar-title>
        <div class="d-flex align-center justify-space-between">
          <v-icon :icon="mdiWindowClose" @click="router.go(-1);" />
          {{ dateString }} {{ mealTypeName }} レシピ選択
          <p></p>
        </div>
      </v-app-bar-title>
    </v-container>
  </v-app-bar>

  <v-main>
    <v-container max-width="960" class="text-center pt-1">
      <v-row class="align-center">
        <v-col cols="9">
          <v-btn-toggle v-model="target" variant="outlined" divided class="tw:w-2xl">
            <v-btn value="history" class="px-2 flex-grow-1">
              {{ mealTypeName }}の履歴
            </v-btn>
            <v-btn value="historyAll" class="px-2 flex-grow-1">
              全ての履歴
            </v-btn>
            <v-btn value="myRecipe" class="px-2 flex-grow-1">
              定番レシピ
            </v-btn>
          </v-btn-toggle>
        </v-col>
        <v-col cols="3">
          <v-btn @click="addNewRecipe()">
            新レシピ
          </v-btn>
        </v-col>
      </v-row>

      <v-row no-gutters class="pt-1">
      <v-col>
        <v-text-field v-model="searchString" :prepend-inner-icon="mdiMagnify" density="compact" hide-details variant="outlined"
          rounded autofocus @change="search">
        </v-text-field>
      </v-col>
      </v-row>

      <v-list lines="two" density="compact" class="pt-1">
        <v-list-item v-for="food in foods" @click="onSelect(food)">
          <v-list-item-title>
            {{ food.name }}
          </v-list-item-title>
          <v-list-item-subtitle>
            {{ food.calory }}kcal {{ food.memo }}
          </v-list-item-subtitle>
        </v-list-item>
      </v-list>
    </v-container>
  </v-main>
</template>
<style scoped>
.v-list-item:nth-child(odd) {
  background-color: #f5f5f5 !important; /* 薄い灰色 */
}
.v-list-item-subtitle {
  line-height: 1.2rem !important;
}
</style>

<script setup>
import router from '@/router';
import { useRoute } from 'vue-router';
import { ref, computed, onMounted } from 'vue';
import { useStateStore } from '@/stores/state';
import { searchMextFood } from '@/utils/client';
import { getDate, formatDate, getMealTypeName } from '@/utils';
import { mdiWindowClose, mdiMagnify } from '@mdi/js';

const route = useRoute();
const stateStore = useStateStore();

const target = ref('history');
const searchString = ref('');
const foods = ref([]);

const dateString = computed(() => formatDate(getDate(route.params.date), 'm月d日(w)'));
const mealTypeName = computed(() => getMealTypeName(route.params.mealType));

const addNewRecipe = async () => {
  stateStore.selectFood = {
    title: 'レシピの材料を選ぶ',
    mealType: route.params.mealType,
    foods: [],
  };
  router.replace({ name: 'edit diary', params: route.params });
}

const search = () => {
}

const onSelect = async (food) => {
}
</script>
