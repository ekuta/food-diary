<template>
  <v-app-bar color="primary" density="compact" extension-height="80">
    <v-container max-width="960">
      <v-app-bar-title>
        <div class="d-flex align-center justify-space-between">
          <v-icon :icon="mdiWindowClose" @click="router.go(-1);" />
          栄養成分データベースを検索
          <p></p>
        </div>
      </v-app-bar-title>
    </v-container>
    <template v-slot:extension>
      <v-container max-width="960" class="pt-0 pb-0">
        <v-text-field :prepend-inner-icon="mdiMagnify" density="compact" hide-details variant="solo"
          rounded autofocus @change="search"></v-text-field>
       <div class="text-caption">
         可食部100gあたり。出典:日本食品標準成分表(八訂)増補2023年
       </div>
      </v-container>
    </template>
  </v-app-bar>

  <v-main>
  <v-container max-width="960" class="pa-0">
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
import { ref, onMounted } from 'vue';
import { useStateStore } from '@/stores/state';
import { searchMextFood } from '@/utils/client';
import { mdiWindowClose, mdiMagnify } from '@mdi/js';

const state = useStateStore();
const foods = ref([]);

const search = async (e) => {
  if (!e.target.value) {
    return;
  }
  const res = await searchMextFood(e.target.value);
  foods.value = res.data;
}

const onSelect = async (food) => {
  state.mextFood = food;
  router.go(-1);
}
</script>
