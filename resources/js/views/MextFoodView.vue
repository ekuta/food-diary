<template>
  <v-app-bar color="primary" density="compact" extension-height="80">
    <v-container>
      <v-app-bar-title>
        <div class="d-flex align-center justify-space-between">
          <v-icon @click="router.go(-1);">mdi-window-close</v-icon>
          栄養成分データベースを検索
          <p></p>
        </div>
      </v-app-bar-title>
    </v-container>
    <template v-slot:extension>
      <v-container class="pt-0 pb-0">
         <v-text-field prepend-inner-icon="mdi-magnify" density="compact" hide-details variant="solo" @change="search"></v-text-field>
       <div class="text-caption">
         可食部100gあたり。出典:日本食品標準成分表(八訂)増補2023年
       </div>
      </v-container>
    </template>
  </v-app-bar>

  <v-main>
  <v-container class="pa-0">
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
import { ref, onMounted } from 'vue';
import { useStateStore } from '@/stores/state';
import { searchMextFood } from '@/utils/client';
import router from '@/router';

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
