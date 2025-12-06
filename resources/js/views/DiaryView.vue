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
    <v-container max-width="960" class="px-2">
      <template v-for="(icon, type) in meals">
        <v-row class="border-t-lg pt-1 pb-1 align-center" no-gutters>
          <v-icon :icon="icon"/> {{ getMealTypeName(type) }}
          <v-spacer/>
          <v-btn color="primary" :prepend-icon="mdiNotebookPlusOutline" variant="tonal"
            @click="addRecipe(type)">レシピ追加</v-btn>
          <v-btn color="primary" :prepend-icon="mdiFood" variant="tonal" class="mx-1"
            @click="addFood(type, diaries[type][0])">食品追加</v-btn>
        </v-row>
        <template v-for="(recipe, dIdx) in diaries[type]" :key="recipe.id">
          <v-list v-if="recipe.items.length" density="compact" slim rounded border class="pb-0 pt-0 mb-1">
            <v-list-item v-if="recipe.recipe_id" class="recipe-list-item px-1 py-1">
              <v-list-item-title> {{ recipe.name }} </v-list-item-title>
              <v-list-item-subtitle>
                {{ round(recipe.calory * recipe.amount / recipe.servings) }}kcal
                </v-list-item-subtitle>
              <template v-slot:prepend>
                <v-icon :icon="mdiWindowClose" @click="onDeleteRecipe(diaries[type], dIdx)"></v-icon>
              </template>
              <template v-slot:append>
                <v-btn variant='outlined' slim :text="unitAmountString(recipe)"
                  style="text-transform: none; font-size: 10px;" @click="onSelectRecipe(recipe)">
                </v-btn>
              </template>
            </v-list-item>
            <v-list-item v-for="(item, iIdx) in recipe.items" :key="item.id" min-height="30"
              class="px-1 py-0" :class="{'border-t-sm': iIdx}"
              >
              <v-list-item-title> {{ item.name }} </v-list-item-title>
              <v-list-item-subtitle v-if="recipe.recipe_id == 0"> {{ round(item.calory) }}kcal </v-list-item-subtitle>
              <template v-slot:prepend>
                <div v-if="recipe.recipe_id" class="px-3"></div>
                <v-icon v-else :icon="mdiWindowClose" @click="onDeleteItem(recipe.items, iIdx)"></v-icon>
              </template>
              <template v-slot:append>
                <div v-if="recipe.recipe_id" class="text-caption pr-2">
                  {{ unitAmountString(item) }}
                </div>
                <v-btn v-else variant='outlined' slim :text="unitAmountString(item)"
                  style="text-transform: none; font-size: 10px;" @click="onSelectItem(item)"
                  >
                </v-btn>
              </template>
            </v-list-item>

            <v-list-item v-if="recipe.recipe_id" min-height="30" class="border-t-sm pa-0">
              <div class="d-flex align-center text-body-2">
              <v-spacer/>
                レシピ分量: {{ round(recipe.servings, 1) + recipe.unit }}
              <v-spacer/>
              <v-btn :prepend-icon="mdiNotebookEditOutline" color="primary" variant="text"
                @click="onEditRecipe(recipe)"
                >
                レシピを編集
              </v-btn>
              <v-spacer/>
              </div>
            </v-list-item>
          </v-list>
        </template>
      </template>
    </v-container>
  </v-main>
  <v-footer app height="60px" class="bg-blue-lighten-5">
    <v-container max-width="960" class="position-relative">
  Footer
    </v-container>
  </v-footer>
  <InputAmountDialog v-model="dialog" @close="onClose" />
  <ConfirmDialog :dialog="confirm" @close="onCloseConfirm" />
</template>
<style scoped>
.recipe-list-item {
  background-color: #EEE;
}
</style>
<script setup>
import router from '@/router';
import { useRoute } from 'vue-router';
import { useUserStore } from '@/stores/user';
import { useDiaryStore } from '@/stores/diary';
import { useRecipeStore } from '@/stores/recipe';
import { ref, onMounted, toRaw } from 'vue';
import { formatDate, getMealTypeName, round } from '@/utils';
import { logout, updateDiary, deleteDiary, getFood } from '@/utils/client';
import { unitAmountString, getUnits, createDiaryItem } from '@/utils/food';
import { mdiMenu, mdiWeatherSunset, mdiPlus, mdiWhiteBalanceSunny, mdiWeatherNight, mdiFoodForkDrink, mdiTarget,
  mdiFood, mdiWindowClose, mdiLogout, mdiNotebookPlusOutline, mdiNotebookEditOutline } from '@mdi/js';
import InputAmountDialog from '@/components/InputAmountDialog.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';

const route = useRoute();
const userStore = useUserStore();
const diaryStore = useDiaryStore();
const recipeStore = useRecipeStore();

const diaries = ref({});

const items = [
  {
    title: '目標の登録',
    value: 'target',
    props: { prependIcon: mdiTarget },
  },
  {
    title: '食品情報',
    value: 'food',
    props: { prependIcon: mdiFood },
  },
  {
    title: 'ログアウト',
    value: 'logout',
    props: { prependIcon: mdiLogout },
  },
];

const meals = {
  1: mdiWeatherSunset,
  2: mdiWhiteBalanceSunny,
  3: mdiWhiteBalanceSunny,
  4: mdiFoodForkDrink,
}

const onMenuSelect = async (val) => {
  switch (val.id) {
    case 'logout':
      await logout();
      userStore.logout();
      router.push({ name: 'login'});
      break;
    case 'food':
      router.push({ name: 'food' });
      break;
  }
}

const addRecipe = (meal_type) => {
  router.push({ name: 'select recipe', params: { date: route.params.date, meal_type: meal_type } });
}

const addFood = (meal_type, recipe) => {
  recipeStore.init({
    title: formatDate(new Date(route.params.date), 'm月d日(w) ') + getMealTypeName(meal_type) + 'の追加',
    recipe: toRaw(recipe),
  });
  router.push({ name: 'select food' });
}

const onEditRecipe = (recipe) => {
  console.log("onEditRecipe: ", recipe);
  recipeStore.init({
      title: recipe.name + 'の食材を追加',
      recipe: toRaw(recipe),
    });
  router.push({ name: 'edit recipe' });
}


const confirm = ref({
  open: false,
});

const onDeleteRecipe = async (diaries, index) => {
  const recipe = diaries[index].recipe;
  confirm.value = {
    open: true,
    type: 'confirm',
    title: 'レシピ削除の確認',
    message: recipe.name + 'のレシピを削除してよろしいですか？',
    diaries: diaries,
    index: index,
  }
}

const onCloseConfirm = async (result) => {
  if (result) {
    const recipe = confirm.value.diaries[confirm.value.index].recipe;
    const res = await deleteDiary(recipe.id);
    if (res.success) {
      confirm.value.diaries.splice(confirm.value.index, 1);
    }
  }
  confirm.value = { open: false, };
}

const onDeleteItem = async (items, index) => {
  const item = items[index];
  const res = await deleteDiary(item.id);
  if (res.success) {
    items.splice(index, 1);
  }
}

const dialog = ref({
  open: false,
});

const onSelectRecipe = async (recipe) => {
  dialog.value = {
    open: true,
    name: recipe.name,
    units: [recipe.unit],
    unit: recipe.unit,
    amount: String(recipe.amount),
    first: true,
    target: recipe,
  }
}

const onSelectItem = async (item) => {
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
    target: item,
  }
}

const onClose = (result) => {
  let target = dialog.value.target;
  if (result) {
    if (target.recipe_id == undefined) {
      Object.assign(target, createDiaryItem(dialog.value));
    } else {
      target.amount = +dialog.value.amount;
    }
    const res = updateDiary(target);
  }
  dialog.value = { open: false };
}

onMounted(async () => {
  diaries.value = await diaryStore.getDiaries(route.params.date);
});

</script>
