import { defineStore } from 'pinia';
import { ref, computed, watchEffect } from 'vue';
import { getDiaryHistory, storeDiary } from '@/utils/client';
import { useDiaryStore } from '@/stores/diary';

export const useRecipeStore = defineStore('recipe', () => {
  const title = ref('');
  const date = ref('');
  const meal_type = ref('');
  const recipe = ref({});
  const items = ref([]);
  const history = ref({});
  const regulars = ref({});
  const selected = ref([]);

  // getters
  const recipe_id = computed(() => recipe.value.recipe_id);
  const name = computed(() => recipe.value.name);
  const servings = computed(() => recipe.value.servings);
  const amount = computed(() => recipe.value.amount);
  const unit = computed(() => recipe.value.unit);
  const itemCount = computed(() => items.value.length);
  const isUpdated = computed(() => items.value.find(item => item.id == undefined));
  const isCreate = computed(() => recipe.value.id == undefined);

  const getHistory = async (type, meal_type) => {
    if (history.value[type] == null) {
      const res = await getDiaryHistory();
      if (res.success) {
        history.value = res.data;
      }
    }
    if (meal_type) {
      selected.value = [];
      history.value[type].forEach((item) => {
        if (item.meal_type == meal_type) {
          selected.value.push(item);
        }
      });
      return selected.value;
    } else {
      return history.value[type];
    }
  }

  const getRegulars = async () => {
    return regulars.value;
  }

  // setters
  const $reset = () => {
    title.value = '';
    date.value = '';
    meal_type.value = '';
    recipe.value = {};
    items.value = [];
    history.value = {};
  }

  const init = (input) => {
    title.value = input.title;
    date.value = input.date;
    meal_type.value = input.meal_type;
    recipe.value = input.recipe;
    items.value = input.items ?? [];
    console.log("init: ", title.value, date.value, meal_type.value, recipe.value, items.value);
  }

  const storeRecipe = async () => {
    const res = await storeDiary({
      date: date.value,
      meal_type: meal_type.value,
      recipe: recipe.value,
      items: items.value,
    });
    if (res.success) {
      recipe.value = res.data.recipe;
      items.value = res.data.items;
      const diaryStore = useDiaryStore();
      diaryStore.apply(date.value, meal_type.value, recipe.value, items.value);
      return true;
    }
    return false;
  }

  const addItem = (item) => {
    items.value.push(item);
  }

  const setItem = (index, item) => {
    item.id = items.value[index].id;
    items.value[index] = item;
    console.log("setItem: ", item);
  }

  const deleteItem = (index) => {
    items.value.splice(index, 1);
  }

  watchEffect(() => {
    recipe.value.calory = recipe.value.protein = recipe.value.fat = recipe.value.carbs = recipe.value.salt = 0;
    items.value.forEach((item) => {
      recipe.value.calory += +item.calory;
      recipe.value.protein += +item.protein;
      recipe.value.fat += +item.fat;
      recipe.value.carbs += +item.carbs;
      recipe.value.salt += +item.salt;
    });
  });

  return {
    title, date, meal_type, recipe, items, history,
    recipe_id, name, servings, amount, unit, itemCount, isUpdated,
    getHistory, getRegulars,
    init, storeRecipe, addItem, setItem, deleteItem,
    $reset
  };
}, { persist: { storage: sessionStorage } });
