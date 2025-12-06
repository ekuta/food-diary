<template>
  <v-app-bar color="primary" density="compact" extension-height="80">
    <v-container max-width="960">
      <v-app-bar-title>
        <div class="d-flex align-center">
          <v-icon :icon="mdiChevronLeft" @click="onSubmit()" />
          <v-spacer />
          {{ recipeStore.title }}
          <v-spacer />
        </div>
      </v-app-bar-title>
    </v-container>
  </v-app-bar>

  <v-main>
    <v-container max-width="960" class="pt-1">
      <v-row>
        <v-col class="text-center">
          <v-btn-toggle v-model="target" variant="outlined" divided mandatory class="tw:w-2xl">
            <v-btn value="history" class="px-2 flex-grow-1">
              {{ getMealTypeName(recipeStore.recipe.meal_type) }}の履歴
            </v-btn>
            <v-btn value="historyAll" class="px-2 flex-grow-1">
              全ての履歴
            </v-btn>
            <v-btn value="regular" class="px-2 flex-grow-1">
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
        <v-text-field ref="searchField" v-model="searchString" :prepend-inner-icon="mdiMagnify" density="compact"
          hide-details variant="outlined" rounded autofocus @change="onSearch"
          >
        </v-text-field>
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
          <template v-slot:append>
            <div v-if="target != 'food'" class="text-caption pr-2">
              {{ unitAmountString(food) }}
            </div>
          </template>
        </v-list-item>
      </v-list>
      <v-row v-if="target == 'food'" no-gutters>
        <v-col class="text-right" @click="router.push({ name: 'edit food' , params: { id:  0 } })">
          <v-btn color="primary" variant="text">新しい食品を追加する</v-btn>
        </v-col>
      </v-row>
    </v-container>
  </v-main>
  <v-footer app height="60px" class="bg-blue-lighten-5">
    <v-container max-width="960" class="position-relative">
      <v-row>
        <v-col>
          <div class="text-caption">
              {{ added_message }} <br>
              {{ total_message }}
            </div>
        </v-col>
        <v-col>
        </v-col>
      </v-row>
      <v-fab absolute location="right center" @click="onSubmit()">
        完了
      </v-fab>
    </v-container>
  </v-footer>
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
import { ref, computed, watch, onMounted } from 'vue';
import { useRecipeStore } from '@/stores/recipe';
import { useDiaryStore } from '@/stores/diary';
import { searchFood, storeDiary, getFood } from '@/utils/client';
import { getDate, formatDate, getMealTypeName } from '@/utils';
import { getUnits, unitAmountString, createDiaryItem } from '@/utils/food';
import { mdiChevronLeft, mdiWindowClose, mdiMagnify } from '@mdi/js';
import InputAmountDialog from '@/components/InputAmountDialog.vue';

const route = useRoute();
const recipeStore = useRecipeStore();
const diaryStore = useDiaryStore();

const searchField = ref();
const searchString = ref();
const added_message = ref('');
const total_message = ref('');
const target = ref('');
const foods = ref([]);
const extractFood = ref(false);
const dialog = ref({
  open: false,
});

const onSearch = async (e) => {
  if (!e.target.value) {
    return;
  }
  const res = await searchFood(e.target.value);
  foods.value = res.data;
}

const onSelect = async (item) => {
  if (target.value == 'history' || target.value == 'historyAll') {
    const res = await getFood(item.food_id);
    const food = res.data;
    const units = getUnits(food);
    dialog.value = {
      food: food,
      name: food.name,
      units: units,
      unit: item.unit,
      amount: item.amount,
      first: true,
      open: true,
    }
  } else {
    const food = item;
    const units = getUnits(food);
    dialog.value = {
      food: food,
      name: food.name,
      units: units,
      unit: units[0], // TODO select from cache
      amount: '',
      first: true,
      open: true,
    }
  }
}

const onClose = (result) => {
  dialog.value.open = false;
  if (result) {
    const item = createDiaryItem(dialog.value);
    recipeStore.addItem(item);
    searchString.value = '';
    foods.value = [];
    searchField.value.focus();
    added_message.value = `${item.name}を追加`;
    console.log("select food: onClose: ", recipeStore.items);
  }
}

const onSubmit = async () => {
  if (recipeStore.recipe_id || !recipeStore.isUpdated) {
    router.go(-1);
    return;
  }
  if (await recipeStore.storeRecipe()) {
    recipeStore.$reset();
    router.go(-1);
  }
}

watch(target, async (newValue) => {
  switch (newValue) {
    case 'history':
      foods.value = await recipeStore.getHistory('items', recipeStore.meal_type);
      break;
    case 'historyAll':
      foods.value = await recipeStore.getHistory('items');
      break;
    case 'regular':
      foods.value = await recipeStore.getRegulars();
      break;
    case 'food':
      foods.value = [];
      break;
  }
});

onMounted(async () => {
  if (recipeStore.recipe == null) {
    router.push('/');
  }
  target.value = 'history';
});
</script>
