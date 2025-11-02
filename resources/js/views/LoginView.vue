<template>
  <v-container>
    <v-card max-width="500" class="mx-auto" title="ログイン">
      <v-card-actions>
        <v-col>
          <v-form ref="loginForm">
            <v-text-field
              v-model="email"
              prepend-icon="mdi-account-circle"
              :rules="[rules.required]"
              label="メールアドレス"
              type="email"
              >
            </v-text-field>
            <v-text-field
              v-model="password"
              prepend-icon="mdi-lock"
              :rules="[rules.required]"
              label="パスワード"
              :type="showPassword ? 'text' : 'password'"
              :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
              @click:append-inner="showPassword = !showPassword"
              >
            </v-text-field>
            <v-btn color="primary" variant="tonal" @click="onSubmit">
              ログイン
            </v-btn>
          </v-form>
        </v-col>
      </v-card-actions>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { login } from '@/api/client';
import { useUserStore } from '@/stores/user';

const loginForm = ref();
const email = ref('');
const password = ref('');
const showPassword = ref(false);

const rules = {
  required: (v) => !!v || '必須項目です',
};

const onSubmit = async () => {
  const validate = await loginForm.value.validate();
  if (validate.valid) {
    const res = await login(email.value, password.value);
  }
}
</script>
