<template>
  <v-dialog v-model="dialog.open" max-width="600px">
    <v-card>
      <v-card-title class="text-center" style="background-color: #3f51b5; color : #ffffff">{{ dialog.title }}</v-card-title>
      <v-card-text class="border">
        <span class="text-pre-wrap" v-text="dialog.message"></span>
      </v-card-text>
      <v-card-actions>
        <v-col>
          <div v-if="dialog.type == 'confirm'" class="d-flex justify-space-between mx-10">
            <v-btn color="cancel" variant="tonal" @click="onClose(false)">
              {{ dialog.cancel_label ? dialog.cancel_label : 'キャンセル' }}
            </v-btn>
            <v-btn color="success" variant="tonal" @click="onClose(true)">
              {{ dialog.ok_label ? dialog.ok_label : 'OK' }}
            </v-btn>
          </div>
          <div v-if="dialog.type == 'information'" class="text-center">
            <v-btn color="success" variant="tonal" @click="onClose(true)">
              {{ dialog.ok_label ? dialog.ok_label : 'OK' }}
            </v-btn>
          </div>
        </v-col>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
const props = defineProps(['dialog']);
const emit = defineEmits(['close']);

const onClose = (result = false) => {
  props.dialog.open = false;
  emit('close', result);
}
</script>
