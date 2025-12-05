import { defineStore } from 'pinia';
import { ref } from 'vue';
import { getDiaries as clientGetDiaries} from '@/utils/client';

export const useDiaryStore = defineStore('diary', () => {
  const diaries = ref({});

  const getDiaries = async (date) => {
    if (diaries.value[date] != null) {
      return diaries.value[date];
    }
    const res = await clientGetDiaries(date);
    if (res.success) {
      diaries.value[date] = res.data;
      return diaries.value[date];
    }
  }
  const getNextRecipeId = async (date, meal_type) => {
    const target = (await getDiaries(date))[meal_type];
    return target.reduce((max, y) => { return max > y.recipe.recipe_id ? max : y.recipe.recipe_id }, 0) + 1;
  }

  const apply = async (date, meal_type, recipe, items) => {
    const target = (await getDiaries(date))[meal_type];
    const index = target.findIndex((diary) => diary.recipe.recipe_id == recipe.recipe_id);
    console.log("apply: index", index);
    if (index != -1) {
      target[index].recipe = recipe;
      target[index].items = items;
    } else {
      target.push({ recipe: recipe, items: items });
      console.log("apply: ", target);
    }
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
