<template>
      <v-app-bar color="primary" density="compact">
        <v-container>
          <v-toolbar color="primary">
            <v-btn icon="mdi-close" @click="isActive.value = false"></v-btn>
            <v-toolbar-title>栄養成分データベースを検索</v-toolbar-title>
          </v-toolbar>
        </v-container>
        <template v-slot:extension>
          <v-container>
           <v-card-text class="mb-2">
             <v-text-field prepend-inner-icon="mdi-magnify" density="compact" hide-details variant="solo" @change="search"></v-text-field>
           </v-card-text>
          </v-container>
        </template>
      </v-app-bar>
      <v-main>
      <v-card>
      <v-container>
        <v-card-text>
          <v-list lines="two" density="compact">
            <v-list-item v-for="food in foods">
              <v-list-item-title> {{ food.name }} </v-list-item-title>
              <v-list-item-subtitle>
                100ｇあたり {{ food.calory }}kcal 
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card-text>
      </v-container>
      </v-card>
      </v-main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useStateStore } from '@/stores/state';
import { searchMextFood } from '@/utils/client';

const state = useStateStore();

const foods = ref([]);

const search = async (e) => {
  if (!e.target.value) {
    return;
  }
  const res = await searchMextFood(e.target.value);
  foods.value = res.data;
}
</script>
