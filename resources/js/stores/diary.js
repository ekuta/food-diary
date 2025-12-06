import { defineStore } from 'pinia';
import { ref } from 'vue';
import { getDiaries as clientGetDiaries} from '@/utils/client';
import { Recipe } from '@/utils/food';

export const useDiaryStore = defineStore('diary', () => {
  const diaries = ref({});

  const getDiaries = async (date) => {
    if (diaries.value[date] != null) {
      return diaries.value[date];
    }
    const res = await clientGetDiaries(date);
    if (res.success) {
      const meals = {};
      Object.entries(res.data).forEach(([key, value]) => {
        meals[key] = [];
        value.forEach((recipe) => {
          meals[key].push(new Recipe(recipe));
        });
      });
      diaries.value[date] = meals;
      return diaries.value[date];
    }
  }
  const getNextRecipeId = async (date, meal_type) => {
    const target = diaries.value[recipe.date][recipe.meal_type];
    return target.reduce((max, y) => { return max > y.recipe_id ? max : y.recipe_id }, 0) + 1;
  }

  const apply = async (recipe) => {
    const target = diaries.value[recipe.date][recipe.meal_type];
    const index = target.findIndex((diary) => diary.recipe_id == recipe.recipe_id);
    if (index != -1) {
      target[index].recipe = recipe;
    } else {
      target.push(recipe);
    }
    console.log("apply: ", target);
  }

  const $reset = () => {
    diaries.value = {};
  }

  return { diaries, getDiaries, getNextRecipeId, apply, $reset };
}, {persist: { storage: sessionStorage } });

export default () => {
  const $store = useDiaryStore()
  return { ...$store, ...storeToRefs($store) }
}
