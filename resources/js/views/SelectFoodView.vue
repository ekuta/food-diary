<template>
  <v-app-bar color="primary" density="compact" extension-height="80">
    <v-container max-width="960">
      <v-app-bar-title>
        <div class="d-flex align-center">
          <v-icon v-if="stateStore.selectFood?.items.length" :icon="mdiArrowLeft" @click="router.go(-1);" />
          <v-icon v-else :icon="mdiWindowClose" @click="router.go(-2);" />
          <v-spacer />
          {{ stateStore.selectFood?.title }}
          <v-spacer />
        </div>
      </v-app-bar-title>
    </v-container>
  </v-app-bar>

  <v-main>
    <v-container max-width="960" class="pt-1">
      <v-row>
        <v-col class="text-center">
          <v-btn-toggle v-model="target" variant="outlined" divided class="tw:w-2xl">
            <v-btn value="history" class="px-2 flex-grow-1">
              {{ mealTypeName }}の履歴
            </v-btn>
            <v-btn value="historyAll" class="px-2 flex-grow-1">
              全ての履歴
            </v-btn>
            <v-btn value="recipe" class="px-2 flex-grow-1">
              定番レシピ
            </v-btn>
            <v-btn value="food" class="px-2 flex-grow-1">
              食品を検索 
            </v-btn>
          </v-btn-toggle>
        </v-col>
      </v-row>
      <v-row no-gutters class="pt-1">
      <v-col>
        <v-text-field :prepend-inner-icon="mdiMagnify" density="compact" hide-details variant="outlined"
          rounded autofocus @change="search">
        </v-text-field>
      </v-col>
      </v-row>

      <v-row v-if="target == 'food' && foods.length == 0" no-gutters>
        <v-col class="text-right" @click="router.push({ name: 'register-food' , params: { id:  0 } })">
          <v-btn color="primary" variant="text">新しい食品を追加する</v-btn>
        </v-col>
      </v-row>

      <v-list lines="two" density="compact" class="pt-1">
        <v-list-item v-for="food in foods" @click="onSelect(food)">
          <v-list-item-title>
            {{ food.name }}
          </v-list-item-title>
          <v-list-item-subtitle>
            {{ food.calory }}kcal
          </v-list-item-subtitle>
        </v-list-item>
      </v-list>
    </v-container>
  </v-main>
  <InputAmountDialog v-model="dialog" @close="onClose" />
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
import { searchFood } from '@/utils/client';
import { getDate, formatDate, getMealTypeName } from '@/utils';
import { getUnits, createDiaryItem } from '@/utils/food';
import { mdiArrowLeft, mdiWindowClose, mdiMagnify } from '@mdi/js';
import InputAmountDialog from '@/components/InputAmountDialog.vue';

const route = useRoute();
const stateStore = useStateStore();

const target = ref('history');
const foods = ref([]);
const extractFood = ref(false);
const dialog = ref({
  open: false,
});

const mealTypeName = computed(() => getMealTypeName(stateStore.selectFood?.mealType));

const search = async (e) => {
  if (!e.target.value) {
    return;
  }
  const res = await searchFood(e.target.value);
  foods.value = res.data;
}

const onSelect = async (food) => {
  dialog.value.food = food;
  dialog.value.units = getUnits(food);
  dialog.value.unit = dialog.value.units[0]; // TODO select from localstorage
  dialog.value.amount = '';
  dialog.value.open = true;
}

const onClose = (result) => {
  dialog.value.open = false;
  if (result) {
    stateStore.selectFood.items.push(createDiaryItem(dialog.value));
    console.log('items: ', stateStore.selectFood.items);
  }
}

onMounted(async () => {
  console.log("food mount: ", stateStore.selectFood);
  if (stateStore.selectFood == null) {
    router.push('/');
  }
});
</script>
