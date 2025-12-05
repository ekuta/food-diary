<template>
  <v-app-bar color="primary" density="compact" extension-height="80">
    <v-container max-width="960">
      <v-app-bar-title>
        <div class="d-flex align-center justify-space-between">
          <v-icon :icon="mdiChevronLeft" @click="router.go(-1)" />
          {{ formatDate(getDate(recipeStore.date), 'm月d日(w)') }}
          {{ getMealTypeName(recipeStore.meal_type) }}
          {{ 'レシピ編集' }}
          <p></p>
        </div>
      </v-app-bar-title>
    </v-container>
  </v-app-bar>

  <v-main>
    <v-container max-width="960" class="pt-2 px-1">
      <v-list-item min-height="48" class="py-0 mb-1">
        <v-list-item-title>
        {{ recipeStore.name }}
        </v-list-item-title>
        <v-list-item-subtitle>
          レシピ分量: {{ recipeStore.servings }}{{ recipeStore.unit }}
        </v-list-item-subtitle>
        <template v-slot:append>
          <div class="text-caption pr-2 text-center">
            {{ unitAmountString(recipeStore.recipe) }} <br>
            {{ round(recipeStore.recipe.calory * recipeStore.recipe.amount / recipeStore.recipe.servings) }}kcal
          </div>
          <v-btn variant='tonal' color="secondary" slim text="編集" @click="onEdit()">
          </v-btn>
        </template>
      </v-list-item>
      <v-list lines="two" density="compact" class="pt-1">
        <v-list-item v-for="(item, index) in recipeStore.items" :key="item"
          min-height="48" border rounded="lg" class="py-0 mb-1">
          <v-list-item-title>
            {{ item.name }}
          </v-list-item-title>
          <v-list-item-subtitle>
            {{ round(item.calory) }}kcal
          </v-list-item-subtitle>
        <template v-slot:prepend>
          <v-icon :icon="mdiWindowClose" @click="onDelete(index, item)"></v-icon>
        </template>
        <template v-slot:append>
          <v-btn variant='outlined' slim :text="unitAmountString(item)" style="text-transform: none; font-size: 10px;"
            @click="onSelect(index, item)"
            >
          </v-btn>
        </template>
        </v-list-item>
        <v-list-item min-height="30" border rounded="lg" class="py-0 mb-1 text-center">
          <v-btn :prepend-icon="mdiPlus" color="primary" variant="text"
            @click="router.push({ name: 'select food' })"
            >
            食品を追加
          </v-btn>
        </v-list-item>

      </v-list>
    </v-container>
  </v-main>
  <v-footer app height="50" class="bg-blue-lighten-5">
    <v-container max-width="960" class="position-relative">
      Footer
      <v-fab absolute location="right center" variant="tonal" @click="router.go(-1);">
        完了
      </v-fab>
    </v-container>
  </v-footer>
  <InputAmountDialog v-model="dialog" @close="onClose" />
  <RecipeDialog v-model="recipeDialog" @close="onRecipeClose" />
</template>
<style scoped>
.v-list-item-subtitle {
  line-height: 1.2rem !important;
}
</style>

<script setup>
import router from '@/router';
import { useRoute } from 'vue-router';
import { ref, onMounted } from 'vue';
import { useRecipeStore } from '@/stores/recipe';
import { getFood, deleteDiary } from '@/utils/client';
import { getDate, formatDate, getMealTypeName, round } from '@/utils';
import { getUnits, createDiaryItem, unitAmountString } from '@/utils/food';
import { mdiWindowClose, mdiChevronLeft, mdiPlus} from '@mdi/js';
import InputAmountDialog from '@/components/InputAmountDialog.vue';
import RecipeDialog from '@/components/RecipeDialog.vue';

const route = useRoute();
const recipeStore = useRecipeStore();

const dialog = ref({
  open: false,
});
const recipeDialog = ref({
  open: false,
});

const rules = {
  required: (v) => !!v || '必須項目です',
};

const onDelete = async (index, item) => {
  if (item.id) {
    const res = await deleteDiary(item.id);
    if (!res.success) {
      return;
    }
  }
  recipeStore.deleteItem(index);
}

const onSelect = async (index, item) => {
  const res = await getFood(item.food_id);
  const food = res.data;
  dialog.value = {
    open: true,
    name: food.name,
    units: getUnits(food),
    unit: item.unit,
    amount: String(item.amount),
    first: true,
    food: food,
    index: index,
  }
}

const onClose = (result) => {
  dialog.value.open = false;
  if (result) {
    recipeStore.setItem(dialog.value.index, createDiaryItem(dialog.value));
  }
}

const onEdit = async () => {
  const recipe = recipeStore.recipe;
  recipeDialog.value = {
    open: true,
    unit: recipe.unit,
    name: recipe.name,
    servings: recipe.servings,
    amount: recipe.amount,
  }
}

const onRecipeClose = (result) => {
  recipeStore.recipe.unit = recipeDialog.value.unit;
  recipeStore.recipe.name = recipeDialog.value.name;
  recipeStore.recipe.servings = recipeDialog.value.servings;
  recipeStore.recipe.amount = recipeDialog.value.amount;
}

onMounted(async () => {
  console.log("edit recipe", recipeStore.recipe);
  if (!recipeStore.recipe_id) {
    router.push('/');
  } else if (recipeStore.isCreate) {
    router.push({ name: 'select food' });
  }
});
</script>
