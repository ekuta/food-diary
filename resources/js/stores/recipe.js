import { defineStore } from 'pinia';
import { ref, computed, watchEffect } from 'vue';
import { getDiaryHistory, storeDiary, updateDiary } from '@/utils/client';
import { useDiaryStore } from '@/stores/diary';
import { Recipe } from '@/utils/food';

export const useRecipeStore = defineStore('recipe', () => {
  const title = ref('');
  const recipe = ref({});
  const history = ref({});
  const regulars = ref({});
  const selected = ref([]);

  // getters
  const recipe_id = computed(() => recipe.value.recipe_id);
  const name = computed(() => recipe.value.name);
  const servings = computed(() => recipe.value.servings);
  const amount = computed(() => recipe.value.amount);
  const unit = computed(() => recipe.value.unit);
  const itemCount = computed(() => recipe.value.items.length);
  const isUpdated = computed(() => recipe.value.items.find(item => item.id == undefined));
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
    recipe.value = {};
    history.value = {};
  }

  const init = (input) => {
    title.value = input.title;
    recipe.value = structuredClone(input.recipe);
    console.log("init: ", title.value, recipe.value);
  }

  const submit = async () => {
    let res;
    if (recipe.value.id) {
      res = await updateDiary(recipe.value);
    } else {
      res = await storeDiary(recipe.value);
    }
    if (res.success) {
      recipe.value = new Recipe(res.data);
      const diaryStore = useDiaryStore();
      diaryStore.apply(recipe.value);
      return true;
    }
    return false;
  }

  const addItem = (item) => {
    recipe.value.items.push(item);
  }

  const deleteItem = (index) => {
    recipe.value.items.splice(index, 1);
  }

  watchEffect(() => {
    if (recipe.value?.items == null) {
      return;
    }
    recipe.value.calory = recipe.value.protein = recipe.value.fat = recipe.value.carbs = recipe.value.salt = 0;
    recipe.value.items.forEach((item) => {
      recipe.value.calory += +item.calory;
      recipe.value.protein += +item.protein;
      recipe.value.fat += +item.fat;
      recipe.value.carbs += +item.carbs;
      recipe.value.salt += +item.salt;
    });
  });

  return {
    title, recipe, history,
    recipe_id, name, servings, amount, unit, itemCount, isUpdated,
    getHistory, getRegulars,
    init, submit, addItem, deleteItem,
    $reset
  };
}, { persist: { storage: sessionStorage } });
