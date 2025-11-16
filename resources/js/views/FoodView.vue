<template>
  <v-app-bar color="primary" density="compact">
    <v-container>
      <v-app-bar-title>
        <div class="d-flex align-center justify-space-between">
        <v-icon @click="router.go(-1);">mdi-window-close</v-icon>
        食品の登録
        <v-btn color="secondary" variant="elevated" @click="onSubmit">登録</v-btn>
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
              栄養成分データベースを検索
            </v-btn>
          </div>
          <v-card-text class="pa-2">
            <v-row dense>
              <v-col><v-text-field v-model="form.name" label="食品の名称" density="compact"
                :rules="[rules.required]"
                hide-details="auto"
                >
              </v-text-field></v-col>
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
              <v-radio-group v-model="form.food_unit" inline hide-details="auto" density="compact">
                <v-radio value="g"></v-radio>
                <div class="d-flex" @click="enableIfdisabled('g')">
                  <div class="tw:w-14 pr-1">
                    <v-number-input v-model="gram_amount" control-variant="hidden" density="compact" hide-details="auto"
                      :disabled="form.food_unit != 'g'" class="compact-input"></v-number-input
                      >
                  </div>
                  gあたり
                </div>
                <v-radio value="ml"></v-radio>
                <div class="d-flex" @click="enableIfdisabled('ml')">
                  <div class="tw:w-14 pr-1">
                    <v-number-input v-model="ml_amount" control-variant="hidden" density="compact" hide-details="auto"
                      :disabled="form.food_unit != 'ml'" class="compact-input"
                      >
                    </v-number-input>
                  </div>
                  mlあたり
                </div>
                <v-radio label="1単位(食/人前/袋/包装など)" value="serving" class="mt-n2"></v-radio>
              </v-radio-group>
            </v-col>
          </v-row>
          <v-row dense>
            <v-col>
              <v-number-input v-model="form.calory" label="エネルギー" class="centered-input" density="compact"
                :rules="[rules.required]"
                hide-details="auto" suffix="kcal" control-variant="hidden"
                >
              </v-number-input>
            </v-col>
            <v-col>
              <v-number-input v-model="form.protein" label="たんぱく質" class="centered-input" density="compact"
                hide-details="auto" suffix="g" control-variant="hidden" :precision="1"
                >
              </v-number-input>
            </v-col>
            <v-col>
              <v-number-input v-model="form.fat" label="脂質" class="centered-input" density="compact"
                hide-details="auto" suffix="g" control-variant="hidden" :precision="1"
                >
              </v-number-input>
            </v-col>
          </v-row>
          <v-row dense>
            <v-col>
              <v-number-input v-model="form.carbs" label="炭水化物" class="centered-input" density="compact"
                hide-details="auto" suffix="g" control-variant="hidden" :precision="1"
                >
              </v-number-input>
            </v-col>
            <v-col>
              <v-number-input v-model="form.salt" label="食塩相当量" class="centered-input" density="compact"
                hide-details="auto" suffix="g" control-variant="hidden" :precision="2"
                >
              </v-number-input>
            </v-col>
            <v-col />
          </v-row>
          </v-card-text>

          <v-divider thickness="2" />
          <div class="d-flex align-end">
            <v-card-title class="text-subtitle-1 py-0 px-2">使用単位</v-card-title>
            <div class="text-error text-caption">
              {{ unit_error }}
            </div>
          </div>
          <v-card-text class="px-2 py-0">
            <v-row no-gutters>
              <v-col cols="4">
                <v-checkbox v-model="use_weight" label="重量(g)" density="compact" hide-details @change="validate('unit')"></v-checkbox>
              </v-col>
              <v-col cols="8" v-if="use_weight && form.food_unit != 'g' || use_spoon && form.food_unit == 'serving'">
                <div class="d-flex pt-2 text-body-1">
                  {{ form.food_unit == 'ml' ? ml_amount + 'ml' : '1単位' }}あたり：
                  <div class="tw:w-14 pr-1">
                    <v-number-input v-model="form.gram_per_food_unit" control-variant="hidden" density="compact"
                      hide-details class="compact-input" @blur="validate('use_weight')"
                      >
                    </v-number-input>
                  </div>
                  g
                </div>
                <div class="text-error text-caption">
                  {{ use_weight_error }}
                </div>
              </v-col>
            </v-row>
            <v-row no-gutters>
              <v-col cols="4">
                <v-checkbox v-model="use_spoon" label="大/小さじ" density="compact" hide-details @change="validate('unit')"></v-checkbox>
              </v-col>
              <v-col cols="8" v-if="use_spoon && form.food_unit != 'ml'">
                <div class="d-flex pt-2 text-body-1">
                  大さじ1あたり：
                  <div class="tw:w-14 pr-1 mt-n1">
                    <v-number-input v-model="form.gram_per_tablespoon" control-variant="hidden" density="compact"
                      hide-details class="compact-input" @blur="validate('use_spoon')"
                      >
                    </v-number-input>
                  </div>
                  g
                </div>
                <div class="text-error text-caption">
                  {{ use_spoon_error }}
                </div>
              </v-col>
            </v-row>
            <v-row no-gutters>
              <v-col cols="4">
                <v-checkbox v-model="use_volume" label="容量(ml)" density="compact" hide-details @change="validate('unit')"></v-checkbox>
              </v-col>
              <v-col cols="8" v-if="use_volume && form.food_unit != 'ml'">
                <div class="d-flex pt-2 text-body-1">
                  {{ form.food_unit == 'g' ? gram_amount + 'g' : '1単位' }}あたり：
                  <div class="tw:w-14 pr-1 mt-n1">
                    <v-number-input v-model="form.ml_per_food_unit" control-variant="hidden" density="compact"
                      hide-details class="compact-input" @blur="validate('use_volume')"
                      >
                    </v-number-input>
                  </div>
                  ml
                </div>
                <div class="text-error text-caption">
                  {{ use_volume_error }}
                </div>
              </v-col>
            </v-row>
            <v-row no-gutters>
              <v-col cols="4">
                <v-checkbox v-model="use_package" label="容器/包装" density="compact" hide-details @change="validate('unit')"></v-checkbox>
              </v-col>
              <template v-if="use_package">
                <v-col cols="8" class="d-flex pt-2 text-body-1">
                  名称：
                  <div class="tw:w-30 mt-n1">
                    <v-select v-model="form.package_name" :items="package_names" density="compact" hide-details
                      :menu-props="{ 'max-height': 600 }"
                      class="compact-input"
                      >
                    </v-select>
                  </div>
                </v-col>
                <v-col offset="4" cols="8">
                  <div class="d-flex pt-2 text-body-1 pb-2">
                    1{{ form.package_name }}あたり
                    <div class="tw:w-14 pr-1 mt-n1">
                      <v-number-input v-model="form.amount_per_package" control-variant="hidden" density="compact"
                        hide-details class="compact-input" @blur="validate('use_package')"
                      ></v-number-input>
                    </div>
                    {{ form.food_unit == 'g' || form.food_unit == 'ml' ? form.food_unit : '単位' }}
                  </div>
                  <div class="text-error text-caption">
                    {{ use_package_error }}
                  </div>
                </v-col>
              </template>
            </v-row>
          </v-card-text>
        </v-card>
      </v-form>
    </v-container>
  </v-main>
</template>

<script setup>
import router from '@/router';
import { useRoute } from 'vue-router';
import { ref, onMounted } from 'vue';
import { useStateStore } from '@/stores/state';
import { storeFood } from '@/utils/client';

const route = useRoute();
const stateStore = useStateStore();

// form

const form = ref({
  name: '',
  alias_names: '',
  food_type: '1',
  food_unit: 'g',
  calory: null,
  protein: null,
  fat: null,
  carbs: null,
  gram_per_food_unit: null,
  gram_per_tablespoon: null,
  ml_per_food_unit: null,
  amount_per_package: null,
  package_name: '個',
});
const use_weight = ref(false);
const use_spoon = ref(false);
const use_volume = ref(false);
const use_package = ref(false);

const rules = {
  required: (v) => !!v || '必須項目です',
};
const unit_error = ref('');
const use_weight_error = ref('');
const use_spoon_error = ref('');
const use_volume_error = ref('');
const use_package_error = ref('');

const food_unit_type = {
  g: 1,
  ml: 2,
  serving: 3,
};

const package_names = ['個', '人前', '本', '袋', '枚', '箱', '缶', '杯', '切れ', 'カップ', '日分'];

const foodForm = ref();
const gram_amount = ref(100);
const ml_amount = ref(100);

const validate = async (target = 'all') => {
  let valid = true;
  if (['all', 'form'].includes(target)) {
    valid = await foodForm.value.validate();
  }
  if (['all', 'unit'].includes(target)) {
    unit_error.value = '';
    if (!use_weight.value && !use_spoon.value && !use_volume.value && !use_package.value) {
      unit_error.value = '使用単位を一つ以上選択してください。';
      valid = false;
    }
  }
  if (['all', 'use_weight'].includes(target)) {
    use_weight_error.value = '';
    if ((use_weight.value && form.value.food_unit != 'g' || use_spoon.value && form.value.food_unit == 'serving') && !form.value.gram_per_food_unit) {
      use_weight_error.value = '必須項目です。';
      valid = false;
    }
  }
  if (['all', 'use_spoon'].includes(target)) {
      use_spoon_error.value = '';
    if (use_spoon.value && form.value.food_unit != 'ml' && !form.value.gram_per_tablespoon) {
      use_spoon_error.value = '必須項目です。';
      valid = false;
    }
  }
  if (['all', 'use_volume'].includes(target)) {
    use_volume_error.value = '';
    if (use_volume.value && form.value.food_unit != 'ml' && !form.value.ml_per_food_unit) {
      use_volume_error.value = '必須項目です。';
      valid = false;
    }
  }
  if (['all', 'use_package'].includes(target)) {
    use_package_error.value = '';
    if (use_package.value && !form.value.amount_per_package) {
      use_package_error.value = '必須項目です。';
      valid = false;
    }
  }
  return valid;
};

const enableIfdisabled = (unit) => {
  if (form.value.food_unit != unit) {
    form.value.food_unit = unit;
  }
}

const onSubmit = async () => {
    console.log(typeof route.params.id);
  if (!validate()) {
    return;
  }
  let food = Object.assign({}, form.value);
  food.food_unit = food_unit_type[food.food_unit];
  food.food_amount = food.food_unit == 1 ? gram_amount.value : (food.food_unit == 2 ? ml_amount.value : 0);
  food.usage_type = +use_weight.value + (+use_spoon.value << 1) + (+use_volume.value << 2) + (+use_package.value << 3);
  if (route.params.id != '0') {
    console.log('?? call storeFood');
  } else {
    const res = await storeFood(food);
  }
}

onMounted(async () => {
  if (stateStore.mextFood) {
    form.value.name = stateStore.mextFood.name;
    form.value.calory = stateStore.mextFood.calory;
    form.value.protein = stateStore.mextFood.protein;
    form.value.fat = stateStore.mextFood.fat;
    form.value.carbs = stateStore.mextFood.carbs;
    form.value.salt = stateStore.mextFood.salt;
    form.value.food_unit = 'g';
    gram_amount.value = 100;
    stateStore.mextFood = null;
  }
});
</script>
