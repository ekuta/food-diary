<template>
  <v-overlay v-model="stateStore.loading" class="align-center justify-center" persistent>
    <v-progress-circular indeterminate />
  </v-overlay>
</template>

<script setup>
import { useStateStore } from '@/stores/state';
import Loading from '@/components/Loading.vue';

const stateStore = useStateStore();
</script>
