<template>
  <v-app-bar color="primary" density="compact" extension-height="60">
    <v-container max-width="960">
      <v-app-bar-title>
        <div class="d-flex align-center justify-space-between">
          <v-icon :icon="mdiWindowClose" @click="router.go(-1);" />
          食品情報の編集
          <div class="tw:w-7.5"></div>
        </div>
      </v-app-bar-title>
    </v-container>
    <template v-slot:extension>
      <v-container max-width="960" class="pt-0 pb-0">
        <v-text-field :prepend-inner-icon="mdiMagnify" density="compact" hide-details variant="solo"
          rounded autofocus @change="search"></v-text-field>
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
      <v-row v-if="foods.length == 0" no-gutters>
        <v-col class="text-right" @click="router.push({ name: 'edit food' , params: { id:  0 } })">
          <v-btn color="primary" variant="text">新しい食品を追加する</v-btn>
        </v-col>
      </v-row>
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
import { searchFood } from '@/utils/client';
import { mdiWindowClose, mdiMagnify } from '@mdi/js';

const state = useStateStore();
const foods = ref([]);

const search = async (e) => {
  if (!e.target.value) {
    return;
  }
  const res = await searchFood(e.target.value);
  foods.value = res.data;
}

const onSelect = async (food) => {
  router.push({ name: 'edit food', params: { id: food.id } });
}
</script>
