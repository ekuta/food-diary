<template>
  <v-dialog v-model="dialog.open" max-width="600px">
    <v-card>
      <v-card-title class="text-center" style="background-color: #3f51b5; color: #ffffff">レシピ情報</v-card-title>
      <v-card-text class="border">
        <v-form ref="recipeForm">
          <v-row dense>
            <v-col>
              <v-text-field v-model="dialog.name" label="レシピ名" maxlength="100" density="compact"
                :rules="[rules.required]" hide-details="auto"
                >
              </v-text-field>
            </v-col>
          </v-row>
          <v-row dense class="align-center">
            <v-col class="text-no-wrap flex-grow-0">使用単位:</v-col>
            <v-col class="mt-4">
              <v-btn-toggle v-model="dialog.unit" mandatory>
                <v-btn v-for="name in units" :key="name" :value="name" min-width="80" height="32"
                  rounded="xl" class="mx-1 mt-0 mb-0" variant="outlined"
                  >
                  {{ name }}
                </v-btn>
              </v-btn-toggle>
            </v-col>
          </v-row>
          <v-row dense>
            <v-col cols="6">
              <v-number-input v-model="dialog.servings" :label="getServingsLabel(dialog.unit)" :max="99999"
                density="compact" :rules="[rules.required]" hide-details="auto" :suffix="dialog.unit"
                control-variant="hidden" :precision="1" class="centered-input"
                >
              </v-number-input>
            </v-col>
            <v-col cols="6">
              <v-number-input v-model="dialog.amount" label="使用分量" :max="99999" density="compact"
                :rules="[rules.required]" hide-details="auto" :suffix="dialog.unit" control-variant="hidden"
                :precision="1" class="centered-input"
                >
              </v-number-input>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-col>
          <div class="d-flex">
            <v-spacer />
            <v-btn text="キャンセル" color="cancel" variant="tonal" @click="onClose(false)"></v-btn>
            <v-spacer />
            <v-btn text="OK" color="indigo-lighten-1" min-width="90" variant="flat" @click="onClose(true)"></v-btn>
            <v-spacer />
          </div>
        </v-col>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref } from 'vue';
import { getServingsLabel } from '@/utils/food';
const dialog = defineModel();
const emit = defineEmits(['close']);

const recipeForm = ref();

const units = ['人前', 'g', 'ml'];
const rules = {
  required: (v) => !!v || '必須項目です',                                                                               };


const onClose = async (result = false) => {
  if (result) {
    const validate = await recipeForm.value.validate();
    if (!validate.valid) {
      return;
    }
  }
  emit('close', result);
}
</script>
