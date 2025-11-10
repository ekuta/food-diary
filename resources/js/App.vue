<template>
  <v-app>
    <router-view />
    <Loading />
  </v-app>
</template>

<script setup>
import Loading from '@/components/Loading.vue';
</script>
