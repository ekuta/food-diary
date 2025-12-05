<template>
  <v-app-bar color="primary" density="compact">
    <v-container max-width="960">
      <v-app-bar-title>
        <div class="d-flex align-center justify-space-between">
        <v-icon :icon="mdiWindowClose" @click="router.go(-1);" />
        食品の登録
        <v-btn color="secondary" variant="elevated" @click="onSubmit">登録</v-btn>
        </div>
      </v-app-bar-title>
    </v-container>
  </v-app-bar>

  <v-main>
    <v-container max-width="960" class="pt-1 px-1">
      <v-form ref="foodForm">
        <v-card variant="elevated">
          <div class="d-flex">
            <v-card-title class="text-subtitle-1 pt-1 px-4">基本情報</v-card-title>
            <v-btn v-if="form.id == 0" color="secondary lighten-2" variant="elevated" density="comfortable" @click="router.push({ name: 'mext-food'});">
              栄養成分データベースを検索
            </v-btn>
          </div>
          <v-card-text class="px-2 pt-0 pb-1">
            <v-row dense>
              <v-col>
                <v-text-field v-model="form.name" label="食品の名称" maxlength="100" density="compact"
                  hide-details="auto" :rules="[rules.required]"
                  >
              </v-text-field></v-col>
            </v-row>
            <v-row dense>
              <v-col>
                <v-text-field v-model="form.alias_names" label="検索キーワード(ひらがな/別名など)" maxlength="100"
                  density="compact" hide-details="auto" />
              </v-col>
            </v-row>
            <v-row no-gutters>
              <v-col>
                <v-radio-group v-model="form.food_type" hide-details="auto" density="compact" inline class="pt-2">
                  <v-label>食品区分：</v-label>
                  <v-radio label="市販品" :value="1"></v-radio>
                  <v-radio label="外食" :value="2"></v-radio>
                  <v-radio label="その他の食品" :value="3"></v-radio>
                </v-radio-group>
              </v-col>
            </v-row>
            <v-row v-show="form.food_type != 3" no-gutters>
              <v-col>
                <v-text-field v-model="form.maker" label="販売者/製造者/店名など" density="compact" maxlength="50"
                  hide-details="auto"
                  >
                </v-text-field>
              </v-col>
            </v-row>
          </v-card-text>
          <v-divider thickness="2" />

          <div class="d-flex align-end">
            <v-card-title class="text-subtitle-1 py-0 px-2">栄養成分表示</v-card-title>
            <div class="text-error text-caption">
              {{ calory_salt_error }}
            </div>
          </div>
          <v-card-text class="px-2 pt-0 pb-1">
          <v-row dense>
            <v-col>
              <v-radio-group v-model="form.food_unit" inline hide-details density="compact">
                <v-radio value="g"></v-radio>
                <div class="d-flex" @click="enableIfdisabled('g')">
                  <div class="tw:w-16 pr-1">
                    <v-number-input v-model="gram_amount" :max="99999" control-variant="hidden" density="compact"
                      hide-details="auto" :disabled="form.food_unit != 'g'" class="compact-input"
                      >
                    </v-number-input
                      >
                  </div>
                  gあたり
                </div>
                <v-radio value="ml"></v-radio>
                <div class="d-flex" @click="enableIfdisabled('ml')">
                  <div class="tw:w-16 pr-1">
                    <v-number-input v-model="ml_amount" :max="99999" control-variant="hidden" density="compact"
                      hide-details="auto"
                      :disabled="form.food_unit != 'ml'" class="compact-input"
                      >
                    </v-number-input>
                  </div>
                  mlあたり
                </div>
                <v-radio label="1単位(食/人前/袋/包装など)" value="単位" ></v-radio>
              </v-radio-group>
            </v-col>
          </v-row>
          <v-row dense>
            <v-col>
              <v-number-input v-model="form.calory" label="エネルギー" :max="99999" density="compact" hide-details
                suffix="kcal" control-variant="hidden" :precision="1" class="centered-input"
                @blur="validate('calory_salt')"
                >
              </v-number-input>
            </v-col>
            <v-col>
              <v-number-input v-model="form.protein" label="たんぱく質" :max="99999" density="compact"
                hide-details="auto" suffix="g" control-variant="hidden" :precision="1" class="centered-input"
                >
              </v-number-input>
            </v-col>
            <v-col>
              <v-number-input v-model="form.fat" label="脂質" :max="99999" density="compact" hide-details
                suffix="g" control-variant="hidden" :precision="1" class="centered-input"
                >
              </v-number-input>
            </v-col>
          </v-row>
          <v-row dense>
            <v-col>
              <v-number-input v-model="form.carbs" label="炭水化物" :max="99999" class="centered-input"
                density="compact" hide-details suffix="g" control-variant="hidden" :precision="1"
                >
              </v-number-input>
            </v-col>
            <v-col>
              <v-number-input v-model="form.salt" label="食塩相当量" :max="99999" class="centered-input"
                density="compact" hide-details="auto" suffix="g" control-variant="hidden" :precision="2"
                @blur="validate('calory_salt')"
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
              <v-col cols="8" v-if="use_weight && form.food_unit != 'g'">
                <div class="d-flex pt-2 text-body-1">
                  {{ form.food_unit == 'ml' ? ml_amount + 'ml' : '1単位' }}あたり：
                  <div class="tw:w-14 pr-1">
                    <v-number-input v-model="form.gram_per_food_unit" :max="99999" control-variant="hidden"
                      density="compact" hide-details class="compact-input" @blur="validate('use_weight')"
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
              <v-col cols="8" v-if="use_spoon && !use_volume && form.food_unit != 'ml'">
                <div class="d-flex pt-2 text-body-1">
                  大さじ1あたり：
                  <div class="tw:w-14 pr-1 mt-n1">
                    <v-number-input v-model="form.amount_per_tablespoon" :max="99999" control-variant="hidden"
                      density="compact" hide-details class="compact-input" @blur="validate('use_spoon')"
                      >
                    </v-number-input>
                  </div>
                  {{ form.food_unit }}
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
                    <v-number-input v-model="form.ml_per_food_unit" :max="99999" control-variant="hidden"
                      density="compact" hide-details class="compact-input" @blur="validate('use_volume')"
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
                <v-col cols="8">
                  <div class="d-flex pt-2 text-body-1">
                    名称：
                    <div class="tw:w-30 pr-1 mt-n1">
                      <v-text-field v-model="form.package_unit" maxlength="10" control-variant="hidden"
                        density="compact" hide-details class="compact-input" @blur="validate('package_unit')"
                        >
                      </v-text-field>
                    </div>
                    <div class="tw:w-10 mt-n1">
                      <v-select v-model="selected_name" :items="package_units" density="compact" hide-details :menu-props="{ 'max-height': 600 }"
                        class="compact-input" @update:modelValue="form.package_unit = selected_name"
                        >
                      </v-select>
                    </div>
                  </div>
                  <div class="text-error text-caption">
                    {{ package_unit_error }}
                  </div>
                </v-col>
                <v-col offset="4" cols="8">
                  <div class="d-flex pt-2 text-body-1 pb-2">
                    1{{ form.package_unit }}あたり
                    <div class="tw:w-14 pr-1 mt-n1">
                      <v-number-input v-model="form.amount_per_package" :max="99999" control-variant="hidden"
                        density="compact" hide-details class="compact-input" @blur="validate('use_package')"
                      ></v-number-input>
                    </div>
                    {{ form.food_unit }}
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

<style scoped>
.v-radio-group {
  line-height: 2.4rem;
}
</style>

<script setup>
import router from '@/router';
import { useRoute } from 'vue-router';
import { ref, onMounted } from 'vue';
import { useStateStore } from '@/stores/state';
import { getFood, storeFood, updateFood } from '@/utils/client';
import { mdiWindowClose } from '@mdi/js';

const route = useRoute();
const stateStore = useStateStore();

// form

const form = ref({
  name: '',
  alias_names: '',
  food_type: 1,
  maker: null,
  food_unit: 'g',
  calory: null,
  protein: null,
  fat: null,
  carbs: null,
  gram_per_food_unit: null,
  amount_per_tablespoon: null,
  ml_per_food_unit: null,
  amount_per_package: null,
  package_unit: '個',
});
const use_weight = ref(false);
const use_spoon = ref(false);
const use_volume = ref(false);
const use_package = ref(false);
const selected_name = ref('個');

const rules = {
  required: (v) => !!v || '必須項目です',
};
const calory_salt_error = ref('');
const unit_error = ref('');
const use_weight_error = ref('');
const use_spoon_error = ref('');
const use_volume_error = ref('');
const package_unit_error = ref('');
const use_package_error = ref('');

const package_units = ['個', '人前', '本', '袋', '枚', '箱', '缶', '杯', '切れ', 'カップ', '日分'];

const foodForm = ref();
const gram_amount = ref(100);
const ml_amount = ref(100);

const validate = async (target = 'all') => {
  let valid = true;
  if (['all', 'form'].includes(target)) {
    valid = await foodForm.value.validate();
  }
  if (['all', 'calory_salt'].includes(target)) {
    calory_salt_error.value = '';
    if (!form.value.calory && !form.value.salt) {
      calory_salt_error.value = 'エネルギーか食塩相当量のどちらかが必要です。';
      valid = false;
    }
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
    if ((use_weight.value && form.value.food_unit != 'g') && !form.value.gram_per_food_unit) {
      use_weight_error.value = '必須項目です。';
      valid = false;
    }
  }
  if (['all', 'use_spoon'].includes(target)) {
      use_spoon_error.value = '';
    if (use_spoon.value && !use_package.value && form.value.food_unit != 'ml' && !form.value.amount_per_tablespoon) {
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
  if (['all', 'package_unit'].includes(target)) {
    package_unit_error.value = '';
    if (use_package.value) {
      if (!form.value.package_unit) {
        package_unit_error.value = '必須項目です。';
      } else if (['g', 'ml', '大さじ', '小さじ'].includes(form.value.package_unit)) {
        package_unit_error.value = 'この名称は使用できません。';
      }
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
  if (!await validate()) {
    return;
  }
  let food = Object.assign({}, form.value);
  console.log("food", food);
  food.food_amount = food.food_unit == 'g' ? gram_amount.value : (food.food_unit == 'ml' ? ml_amount.value : 1);
  if (food.food_type == 3) food.maker = null;
  food.usage_type = +use_weight.value + (+use_spoon.value << 1) + (+use_volume.value << 2) + (+use_package.value << 3);
  if (use_spoon.value && !use_volume.value && food.food_unit != 'ml') {
    if (food.food_unit == 'g') {
      food.ml_per_food_unit = 15 * food.food_amount / food.amount_per_tablespoon;
    }
  }
  if (route.params.id != '0') {
    const res = await updateFood(food);
    router.go(-1);
  } else {
    const res = await storeFood(food);
    router.go(-1);
  }
}

onMounted(async () => {
  if (route.params.id != '0') {
    const result = await getFood(route.params.id);
    const food = result.data;
    form.value = food;
    if (food.usage_type & 0x1) {
      use_weight.value = true;
    }
    if (food.usage_type & 0x2) {
      use_spoon.value = true;
    }
    if (food.usage_type & 0x4) {
      use_volume.value = true;
    }
    if (food.usage_type & 0x8) {
      use_package.value = true;
    }
  } else {
    form.value.id = 0;
    if (stateStore.mextFood) {
      form.value.name = stateStore.mextFood.name;
      form.value.calory = stateStore.mextFood.calory;
      form.value.protein = stateStore.mextFood.protein;
      form.value.fat = stateStore.mextFood.fat;
      form.value.carbs = stateStore.mextFood.carbs;
      form.value.salt = stateStore.mextFood.salt;
      form.value.food_type = 3;
      form.value.food_unit = 'g';
      gram_amount.value = 100;
    }
  }
  stateStore.mextFood = null;
});
</script>
