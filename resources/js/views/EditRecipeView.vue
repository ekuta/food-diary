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
    <v-container max-width="960" class="text-center pt-1 px-1">
      <v-card variant="elevated" class="px-1">
        <v-card-text class="px-0 pt-1 pb-1">
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
        </v-card-text>
      </v-card>
    </v-container>
  </v-main>
  <v-footer app height="30px" class="bg-blue-lighten-5">
    <v-container max-width="960" class="position-relative">
  Footer
    </v-container>
  </v-footer>
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
import { searchMextFood } from '@/utils/client';
import { getDate, formatDate, getMealTypeName } from '@/utils';
import { mdiWindowClose, mdiPlus} from '@mdi/js';

const route = useRoute();
const state = useStateStore();

const dateString = computed(() => formatDate(getDate(route.params.date), 'm月d日(w)'));
const mealTypeName = computed(() => getMealTypeName(route.params.mealType));

const form = ref({
});

const rules = {
  required: (v) => !!v || '必須項目です',
};

const search = () => {
}

const onSelect = async (food) => {
  state.mextFood = food;
  router.go(-1);
}
</script>
