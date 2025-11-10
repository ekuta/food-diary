<template>
    <v-dialog v-model="dialog.open" max-width="600px" persistent>
      <v-card>
        <v-card-title>{{ dialog.title }}</v-card-title>
        <v-card-text class="border">
          <span class="text-pre-wrap" v-text="dialog.message"></span>
        </v-card-text>
        <v-card-actions>
        <v-col>
          <div v-if="dialog.type == 'confirm'" class="d-flex justify-space-between mx-10">
            <v-btn color="cancel" variant="tonal" @click="onClose(false)">
              {{ dialog.cancel_label }}
            </v-btn>
            <v-btn color="success" variant="tonal" @click="onClose(true)">
              {{ dialog.ok_label }}
            </v-btn>
          </div>
          <div v-if="dialog.type == 'information'" class="text-center">
            <v-btn color="success" variant="tonal" @click="onClose(true)">
              {{ dialog.ok_label }}
            </v-btn>
          </div>
        </v-col>
        </v-card-actions>
      </v-card>
    </v-dialog>
</template>

<script setup>
const props = defineProps(['dialog']);

props.dialog.type = props.dialog.type ?? 'confirm';
props.dialog.cancel_label = props.dialog.cancel_label ?? 'キャンセル';
props.dialog.ok_label = props.dialog.ok_label ?? 'OK';

const emit = defineEmits(['close']);

const onClose = (result = false) => {
  props.dialog.open = false;
  emit('close', result);
}

</script>
