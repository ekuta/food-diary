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
    <v-container max-width="960" class="pt-1">
      <v-row class="align-center">
        <v-col cols="9">
          <v-btn-toggle v-model="target" variant="outlined" divided mandatory class="tw:w-2xl">
            <v-btn value="history" class="px-2 flex-grow-1">
              {{ mealTypeName }}の履歴
            </v-btn>
            <v-btn value="historyAll" class="px-2 flex-grow-1">
              全ての履歴
            </v-btn>
            <v-btn value="regular" class="px-2 flex-grow-1">
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
        <v-text-field :prepend-inner-icon="mdiMagnify" density="compact" hide-details variant="outlined"
          rounded autofocus @change="search">
        </v-text-field>
      </v-col>
      </v-row>

      <v-list lines="two" density="compact" class="pt-1">
        <v-list-item v-for="recipe in recipes" @click="onSelect(recipe)">
          <v-list-item-title>
            {{ recipe.name }}
          </v-list-item-title>
          <v-list-item-subtitle>
            {{ formatDate(new Date(recipe.date), 'm月j日(w)') }}
            {{ getMealTypeName(recipe.meal_type) }}
            レシピ分量: {{ round(recipe.servings, 1) + recipe.unit }}
          </v-list-item-subtitle>
          <template v-slot:append>
            <div class="text-caption pr-2 text-center">
              {{ unitAmountString(recipe) }} <br>
              {{ round(recipe.calory * recipe.amount / recipe.servings) }}kcal
            </div>
          </template>
        </v-list-item>
      </v-list>
    </v-container>
  </v-main>
  <RecipeDialog v-model="dialog" @close="onClose" />
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
import { round } from '@/utils';
import { unitAmountString } from '@/utils/food';
import { useRecipeStore } from '@/stores/recipe';
import { useDiaryStore } from '@/stores/diary';
import { getDate, formatDate, getMealTypeName } from '@/utils';
import { getDiary } from '@/utils/client';
import { mdiWindowClose, mdiMagnify } from '@mdi/js';
import RecipeDialog from '@/components/RecipeDialog.vue';

const route = useRoute();
const recipeStore = useRecipeStore();
const diaryStore = useDiaryStore();

const target = ref('');
const recipes = ref([]);
const dialog = ref({});

const dateString = computed(() => formatDate(getDate(route.params.date), 'm月j日(w)'));
const mealTypeName = computed(() => getMealTypeName(route.params.meal_type));

const addNewRecipe = async () => {
  dialog.value = {
    open: true,
    unit: '人前',
    name: '',
    servings: null,
    amount: 1,
  }
}
const onClose = async (result) => {
  dialog.value.open = false;
  if (result) {
    const next_recipe_id = await diaryStore.getNextRecipeId(route.params.date, route.params.meal_type);
    console.log("next_recipe_id: ", next_recipe_id);

    recipeStore.init({
      title: 'レシピの食材を選ぶ',
      date: route.params.date,
      meal_type: route.params.meal_type,
      recipe: {
        recipe_id: next_recipe_id,
        name: dialog.value.name,
        servings: dialog.value.servings,
        amount: dialog.value.amount,
        unit: dialog.value.unit,
      },
    });
    await router.replace({ name: 'edit recipe' });
    router.push({ name: 'select food' });
  }
}

const search = () => {
}

const onSelect = async (recipe) => {
  const res = await getDiary(recipe.id);
  if (!res.success) {
    return;
  }
  res.data.recipe.recipe_id = await diaryStore.getNextRecipeId(route.params.date, route.params.meal_type);
  recipeStore.init({
    title: recipe.name + 'の食材を追加',
    date: route.params.date,
    meal_type: route.params.meal_type,
    recipe: res.data.recipe,
    items: res.data.items,
  });
  if (await recipeStore.storeRecipe()) {
    router.replace({ name: 'edit recipe' });
  }
}

watch(target, async (newValue) => {
    switch (newValue) {
    case 'history':
      recipes.value = await recipeStore.getHistory('recipes', route.params.meal_type);
      break;
    case 'historyAll':
      recipes.value = await recipeStore.getHistory('recipes');
      break;
    case 'regular':
      recipes.value = await recipeStore.getRegulars();
      break;
  }
})

onMounted(async () => {
  target.value = 'history';
})
</script>
