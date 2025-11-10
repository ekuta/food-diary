<template>
  <v-container>
    <v-card max-width="500" class="mx-auto">
      <v-card-title class="text-center">
        {{ route.name == 'login' ? 'ログイン' : 'メール確認' }}
      </v-card-title>
      <v-card-subtitle v-if="route.name == 'verify-email'" class="text-center">
        メール確認のためログインしてください。
      </v-card-subtitle>
      <v-card-text v-show="errorMessage" class="bg-red-lighten-2 pt-4">
        {{ errorMessage }}
      </v-card-text>
      <v-card-actions>
        <v-col>
          <v-form ref="loginForm" :disabled="userStore.isLogin()">
            <v-text-field
              v-model="form.email"
              prepend-icon="mdi-account-circle"
              :rules="[rules.required, rules.email]"
              label="メールアドレス"
              type="email"
              >
            </v-text-field>
            <v-text-field
              v-model="form.password"
              prepend-icon="mdi-lock"
              :rules="[rules.required, rules.min8]"
              label="パスワード"
              :type="showPassword ? 'text' : 'password'"
              :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
              @click:append-inner="showPassword = !showPassword"
              >
            </v-text-field>
            <div v-if="route.name == 'login'" class="d-flex justify-space-between ml-10">
              <v-btn color="primary" variant="tonal" @click="onSubmit">
                ログイン
              </v-btn>
              <v-btn to="/register" color="primary" variant="text">
                新規ユーザ登録
              </v-btn>
            </div>
            <div v-else class="text-center">
              <v-btn color="primary" variant="tonal" @click="onSubmit">
                ログイン
              </v-btn>
            </div>
          </v-form>
        </v-col>
      </v-card-actions>
      <v-card-text v-if="verifyStatus == 'verifying' && route.name == 'login'" class="bg-yellow-lighten-4 pt-4">
        メールアドレスの確認が完了していません。メールアドレスの確認後、再度ログインを行ってください。
        <div class="text-right">
          <v-btn color="primary" variant="tonal" @click="resendEmail()" :disable="resending">
            {{ resending ? '送信しました' : '確認メールを再送する' }}
          </v-btn>
        </div>
      </v-card-text>
      <template v-if="route.name == 'verify-email'">
        <v-card-text v-if="verifyStatus == 'verifying'" class="bg-blue-lighten-5 pt-4">
          メールアドレスの確認中です。
        </v-card-text>
        <v-card-text v-if="verifyStatus == 'success'" class="bg-blue-lighten-5 pt-4">
          メールアドレスの確認が完了しました。
          <div class="d-flex justify-space-between ml-10">
            <v-btn to="/diary" color="primary" variant="tonal">
              食事記録に移動
            </v-btn>
          </div>
        </v-card-text>
      </template>
    </v-card>
  </v-container>
</template>

<script setup>
import { useRouter, useRoute } from 'vue-router';
import { ref, onMounted } from 'vue';
import { login, sendEmailVerification, verifyEmail } from '@/api/client';
import { useUserStore } from '@/stores/user';
import { useStateStore } from '@/stores/state';
import ConfirmDialog from '@/components/ConfirmDialog.vue';

const router = useRouter();
const route = useRoute();
const userStore = useUserStore();
const stateStore = useStateStore();

const loginForm = ref();
const form = ref({
  email: '',
  password: ''
});
const showPassword = ref(false);
const errorMessage = ref(null);
const verifyStatus = ref('initial');
const verifySuccess = ref(false);
const resending = ref(false);

const rules = {
  required: (v) => !!v || '必須項目です',
  email: (v) => /.+@.+\..+/.test(v) || 'メールアドレスが不正です',
  min8: (v) => v.length >= 8 || '8文字以上が必要です',
};

const onSubmit = async () => {
  errorMessage.value = null;
  const validate = await loginForm.value.validate();
  if (!validate.valid) {
    return;
  }
  stateStore.loading = true;
  const res = await login(form.value);
  stateStore.loading = false;
  if (res.success) {
    userStore.login(res.data);
    if (!res.data.email_verified_at) {
      verifyStatus.value = "verifying";
      if (route.name == 'verify-email') {
        await sendVerifyEmail();
      }
    } else {
      router.push({ name: 'diary' });
    }
  } else {
    errorMessage.value = res.message;
  }
}

const resendEmail = async () => {
  resending.value = true;
  await sendEmailVerification();
  setTimeout(() => { resending.value = false }, 30 * 1000);
}

const sendVerifyEmail = async () => {
  const res = await verifyEmail(route.params, route.query);
  console.log("sendVerifyEmail", res);
  if (res.success) {
    verifyStatus.value = "success";
  } else {
    if (res.response && [401, 419].includes(res.response.status)) {
      userStore.logout();
      verifyStatus.value = "init";
    }
  }
}

onMounted(async () => {
  if (userStore.isLogin() && route.name == 'verify-email') {
    if (userStore.user.email_verified_at) {
      verifyStatus.value = "success";
    } else {
      verifyStatus.value = "verifying";
      await sendVerifyEmail();
    }
  }
});
</script>
