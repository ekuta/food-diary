<template>
  <v-app-bar color="primary" density="compact">
    <v-container>
      <v-app-bar-title>
        <div class="d-flex align-center justify-space-between">
        <v-icon small>mdi-window-close</v-icon>
        アイテム登録
        <v-btn color="secondary" variant="elevated">登録</v-btn>
        </div>
      </v-app-bar-title>
    </v-container>
  </v-app-bar>

  <v-main>
    <v-container>
      <v-form ref="foodForm">
        <v-card variant="elevated">
          <div class="d-flex">
            <v-card-title class="text-subtitle-1 pt-1 px-4">基本情報</v-card-title>
            <v-btn color="secondary lighten-2" variant="elevated" density="comfortable" @click="router.push({ name: 'mext-food'});">
              栄養成分データベースから入力
            </v-btn>
          </div>
          <v-card-text class="pa-2">
            <v-row dense>
              <v-col><v-text-field v-model="form.name" label="食品の名称" density="compact" hide-details="auto"></v-text-field></v-col>
            </v-row>
            <v-row dense>
              <v-col><v-text-field v-model="form.alias_names" label="検索キーワード(ひらがな/別名など)" density="compact" hide-details="auto"></v-text-field></v-col>
            </v-row>
            <v-radio-group v-model="form.food_type" hide-details="auto" density="compact" inline class="pt-2">
                <v-label>食品区分：</v-label>
                <v-radio label="食材" value="1"></v-radio>
                <v-radio label="市販品" value="2"></v-radio>
                <v-radio label="メニュー" value="3"></v-radio>
            </v-radio-group>
          </v-card-text>

          <v-divider thickness="2" />
          <v-card-title class="text-subtitle-1 py-0 px-2">栄養成分表示</v-card-title>
          <v-card-text class="pa-2">
          <v-row dense>
            <v-col>
              <v-radio-group v-model="form.unit_group" inline hide-details="auto" density="compact" class="flex-grow-0" @change="selectUnitGroup()">
                <v-radio value="g"></v-radio>
                <div class="d-flex" @click="enableIfdisabled('g')">
                  <div class="tw:w-14 pr-1">
                    <v-number-input v-model="gram_amount" control-variant="hidden" density="compact" hide-details="auto"
                        :disabled="form.unit_group != 'g'" class="centered-input compact-input"></v-number-input>
                  </div>
                  gあたり
                </div>
                <v-radio value="ml"></v-radio>
                <div class="d-flex" @click="enableIfdisabled('ml')">
                  <div class="tw:w-14 pr-1">
                    <v-number-input v-model="ml_amount" control-variant="hidden" density="compact" hide-details="auto"
                        :disabled="form.unit_group != 'ml'" class="centered-input compact-input"></v-number-input>
                  </div>
                  mlあたり
                </div>
                <v-radio label="1単位(食/人前/袋/包装など)" value="serving"></v-radio>
              </v-radio-group>
            </v-col>
          </v-row>
          <v-row dense>
            <v-col>
              <v-number-input v-model="form.calory" label="エネルギー" class="centered-input" density="compact" hide-details="auto" suffix="kcal" control-variant="hidden" :precision="1"></v-number-input>
            </v-col>
            <v-col>
              <v-number-input v-model="form.protein" label="たんぱく質" class="centered-input" density="compact" hide-details="auto" suffix="g" control-variant="hidden" :precision="1"></v-number-input>
            </v-col>
            <v-col>
              <v-number-input v-model="form.fat" label="脂質" class="centered-input" density="compact" hide-details="auto" suffix="g" control-variant="hidden" :precision="1"></v-number-input>
            </v-col>
          </v-row>
          <v-row dense>
            <v-col>
              <v-number-input v-model="form.carbs" label="炭水化物" class="centered-input" density="compact" hide-details="auto" suffix="g" control-variant="hidden" :precision="1"></v-number-input>
            </v-col>
            <v-col>
              <v-number-input v-model="form.salt" label="食塩相当量" class="centered-input" density="compact" hide-details="auto" suffix="g" control-variant="hidden" :precision="2"></v-number-input>
            </v-col>
            <v-col />
          </v-row>
          </v-card-text>

          <v-divider thickness="2" />
          <v-card-title class="text-subtitle-1 py-0 px-2">食品の使い方</v-card-title>
          <v-card-text class="px-2 py-0">
            <v-row no-gutters>
              <v-col cols="4">
                <v-checkbox label="グラム(g)" density="compact" hide-details></v-checkbox>
              </v-col>
            </v-row>
            <v-row no-gutters>
              <v-col cols="4">
                <v-checkbox label="大/小さじ" density="compact" hide-details></v-checkbox>
              </v-col>
            </v-row>
            <v-row no-gutters>
              <v-col cols="4">
                <v-checkbox label="容器/包装" density="compact" hide-details></v-checkbox>
              </v-col>
            </v-row>
            <v-row no-gutters>
              <v-col cols="4">
                <v-checkbox label="容量(ml)" density="compact" hide-details></v-checkbox>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-form>
    </v-container>
  </v-main>
</template>
<script setup>
import router from '@/router';
import { ref, onMounted } from 'vue';
import { useStateStore } from '@/stores/state';

const state = useStateStore();

const form = ref({
  name: '',
  alias_names: '',
  food_type: '1',
  unit_group: 'g',
  calory: null,
  protein: null,
  fat: null,
  carbs: null,
});

const gram_amount = ref(100);
const ml_amount = ref(100);
const dialog = ref(false);

const enableIfdisabled = (unit) => {
  if (form.value.unit_group != unit) {
    form.value.unit_group = unit;
  }
}

onMounted(async () => {
  if (state.mextFood) {
    form.value.name = state.mextFood.name;
    form.value.calory = state.mextFood.calory;
    form.value.protein = state.mextFood.protein;
    form.value.fat = state.mextFood.fat;
    form.value.carbs = state.mextFood.carbs;
    form.value.salt = state.mextFood.salt;
    form.value.unit_group = 'g';
    gram_amount.value = 100;
    state.mextFood = null;
  }
});
</script>
