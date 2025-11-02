<template>
Diary
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { getDiary } from '@/api/client';
import { useUserStore } from '@/stores/user';

const diaries = ref(null);

onMounted(async () => {
  diaries.value = await getDiary();
});

</script>
