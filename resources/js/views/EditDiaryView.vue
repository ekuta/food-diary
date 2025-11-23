<template>
  <v-app-bar color="primary" density="compact" extension-height="80">
    <v-container max-width="960">
      <v-app-bar-title>
        <div class="d-flex align-center justify-space-between">
          <v-icon :icon="mdiWindowClose" @click="router.go(-1);" />
          {{ dateString }} {{ mealTypeName }} レシピ登録
          <p></p>
        </div>
      </v-app-bar-title>
    </v-container>
  </v-app-bar>

  <v-main>
    <v-container max-width="960" class="pt-2 px-1">
      <v-row dense>
        <v-col cols="8">
          <v-text-field v-model="form.name" label="レシピ名" density="compact"
            :rules="[rules.required]"
            hide-details="auto"
            >
          </v-text-field>
        </v-col>
        <v-col cols="4">
          <v-number-input v-model="form.servings" label="分量" density="compact"
            :rules="[rules.required]"
            hide-details="auto" suffix="人分" control-variant="hidden" :precision="1"
            >
          </v-number-input>
        </v-col>
      </v-row>
      <v-list lines="two" density="compact" class="pt-1">
        <v-list-item v-for="(diary, index) in stateStore.selectFood.items" :key="diary"
          min-height="48" border rounded="lg" class="py-0 mb-1">
          <v-list-item-title>
            {{ diary.name }}
          </v-list-item-title>
          <v-list-item-subtitle>
            {{ Math.round(diary.calory) }}kcal
          </v-list-item-subtitle>
        <template v-slot:prepend>
          <v-icon :icon="mdiWindowClose" @click="onDelete(index)"></v-icon>
        </template>
        <template v-slot:append>
          <v-btn variant='outlined' slim style="text-transform: none; font-size: 10px;">
            <template v-if="diary.unit == '大さじ' || diary.unit == '小さじ'">
              {{ diary.unit }}{{ diary.amount }}
            </template>
            <template v-else>
              {{ diary.amount }}{{ diary.unit }}
            </template>
          </v-btn>
        </template>
        </v-list-item>
        <v-list-item min-height="30" border rounded="lg" class="py-0 mb-1 text-center">
          <v-btn :prepend-icon="mdiPlus" variant="text" @click="router.push({ name: 'select food' })"> 食材を追加 </v-btn>
        </v-list-item>

      </v-list>
    </v-container>
  </v-main>
  <v-footer app height="30px" class="bg-blue-lighten-5">
    <v-container max-width="960" class="position-relative">
  Footer
    </v-container>
  </v-footer>
</template>
<style scoped>
.v-list-item-subtitle {
  line-height: 1.2rem !important;
}
</style>

<script setup>
import router from '@/router';
import { useRoute } from 'vue-router';
import { ref, computed, onMounted } from 'vue';
import { useStateStore } from '@/stores/state';
import { searchMextFood } from '@/utils/client';
import { getDate, formatDate, getMealTypeName } from '@/utils';
import { mdiWindowClose, mdiPlus} from '@mdi/js';

const route = useRoute();
const stateStore = useStateStore();

const dateString = computed(() => formatDate(getDate(route.params.date), 'm月d日(w)'));
const mealTypeName = computed(() => getMealTypeName(route.params.mealType));

const form = ref({
});

const rules = {
  required: (v) => !!v || '必須項目です',
};

const onDelete = async (index) => {
}

const onSelect = async (food) => {
}

onMounted(async () => {
  console.log("edit diary: ", stateStore.selectFood);
  if (!stateStore.selectFood) {
    router.push('/');
  } else if (stateStore.selectFood.items.length == 0) {
    router.push({ name: 'select food' });
  }
});
</script>
