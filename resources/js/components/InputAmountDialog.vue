<template>
    <v-bottom-sheet v-model="dialog.open" width="960" opacity="0">
      <v-card color="grey-darken-4">
        <v-card-title class="d-flex align-center justify-space-between pt-0 pb-0">
          <v-btn :icon="mdiWindowClose" variant="text" @click="onClose(false)"></v-btn>
          使用分量
          <v-btn text="完了" color="grey-darken-4" variant="flat" @click="onClose(true)"></v-btn>
        </v-card-title>
        <v-card-text class="pt-2" style="background-color: #424242">
        <v-row v-if="dialog.units?.length != 1">
          <v-col>
            <v-btn-toggle v-model="dialog.unit" mandatory>
              <v-btn v-for="name in dialog.units" :key="name" :value="name" :min-width="80"
                rounded="xl" class="mx-1 mt-2 mb-2" variant="flat" base-color="grey-darken-2" color="grey"
                style="text-transform: none"
                >
                {{ name }}
              </v-btn>
           </v-btn-toggle>
          </v-col>
        </v-row>
        <v-row no-gutters>
          <v-col>
            <v-sheet color="grey-darken-2" height="42" class="d-flex justify-center align-center">
              {{dialog.name}}
            </v-sheet>
          </v-col>
          <v-col>
            <v-sheet color="grey-darken-2" height="42" class="d-flex justify-center align-center">
              {{ unitAmountString(dialog) }}
            </v-sheet>
          </v-col>
        </v-row>
        <v-row dense>
          <v-col>
            <v-btn text="1" size="large" variant="flat" color="grey-darken-1" block
              @click="onClick('1')" class="font-weight-bold"></v-btn>
          </v-col>
          <v-col>
            <v-btn text="2" size="large" variant="flat" color="grey-darken-1" block
              @click="onClick('2')" class="font-weight-bold"></v-btn>
          </v-col>
          <v-col>
            <v-btn text="3" size="large" variant="flat" color="grey-darken-1" block
              @click="onClick('3')" class="font-weight-bold"></v-btn>
          </v-col>
        </v-row>
        <v-row dense>
          <v-col>
            <v-btn text="4" size="large" variant="flat" color="grey-darken-1" block
              @click="onClick('4')" class="font-weight-bold"></v-btn>
          </v-col>
          <v-col>
            <v-btn text="5" size="large" variant="flat" color="grey-darken-1" block
              @click="onClick('5')" class="font-weight-bold"></v-btn>
          </v-col>
          <v-col>
            <v-btn text="6" size="large" variant="flat" color="grey-darken-1" block
              @click="onClick('6')" class="font-weight-bold"></v-btn>
          </v-col>
        </v-row>
        <v-row dense>
          <v-col>
            <v-btn text="7" size="large" variant="flat" color="grey-darken-1" block
              @click="onClick('7')" class="font-weight-bold"></v-btn>
          </v-col>
          <v-col>
            <v-btn text="8" size="large" variant="flat" color="grey-darken-1" block
              @click="onClick('8')" class="font-weight-bold"></v-btn>
          </v-col>
          <v-col>
            <v-btn text="9" size="large" variant="flat" color="grey-darken-1" block
              @click="onClick('9')" class="font-weight-bold"></v-btn>
          </v-col>
        </v-row>
        <v-row dense>
          <v-col>
            <v-btn text="." size="large" variant="text" block
              @click="onClick('.')" class="font-weight-bold"></v-btn>
          </v-col>
          <v-col>
            <v-btn text="0" size="large" variant="flat" color="grey-darken-1" block
              @click="onClick('0')" class="font-weight-bold"></v-btn>
          </v-col>
          <v-col>
            <v-btn size="large" variant="text" block @click="onClick('del')">
              <v-icon :icon="mdiBackspaceOutline" />
            </v-btn>
          </v-col>
        </v-row>
        <v-row dense>
        </v-row>
        </v-card-text>
      </v-card>
    </v-bottom-sheet>
</template>
<style scoped>
</style>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { getUnits, unitAmountString } from '@/utils/food';
import { mdiWindowClose, mdiBackspaceOutline } from '@mdi/js';
const dialog = defineModel();
const emit = defineEmits(['close']);

const onClick = (key) => {
  if (dialog.value.first) {
    dialog.value.amount = '';
    dialog.value.first = false;
  }
  const amount = dialog.value.amount;
  if (key == 'del') {
    dialog.value.amount = amount.slice(0, -1);
  } else if (key == '.') {
    if (amount.indexOf('.') == -1) {
      dialog.value.amount += key;
    }
  } else {
    const pos = amount.indexOf('.');
    if (pos == -1 && amount.length < 5 || amount.length - pos < 3) {
      dialog.value.amount += key;
    }
  }
};

const onClose = (result) => {
  emit('close', result);
}
</script>
