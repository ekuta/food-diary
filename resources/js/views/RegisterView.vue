<template>
  <v-container max-width="640">
    <v-card class="mx-auto">
      <v-card-title class="text-center">
        新規ユーザ登録
      </v-card-title>
      <v-card-text v-show="errorMessage" class="bg-red-lighten-2 pt-4">
        {{ errorMessage }}
      </v-card-text>
      <v-card-actions>
        <v-col>
          <v-form ref="loginForm" :disabled="verifingEmail">
            <v-text-field v-model="form.name" label="ニックネーム" maxlength="100"
              :prepend-icon="mdiAccountCircle"
              :rules="[rules.required]"
              name="name"
              type="text"
            />
            <v-text-field v-model="form.email" label="メールアドレス" maxlength="100"
              :prepend-icon="mdiMail"
              :rules="[rules.required, rules.email]"
              type="email"
            />
            <v-text-field v-model="form.password" label="パスワード" maxlength="100"
              :prepend-icon="mdiLock"
              :rules="[rules.required, rules.min8]"
              :type="showPassword ? 'text' : 'password'"
              :append-inner-icon="showPassword ? mdiEye : mdiEyeOff"
              @click:append-inner="showPassword = !showPassword"
            />
            <v-text-field v-model="form.password_confirmation" label="パスワード確認" maxlength="100"
              :prepend-icon="mdiLock"
              :rules="[rules.required, rules.same]"
              :type="showPassword ? 'text' : 'password'"
              :append-inner-icon="showPassword ? mdiEye : mdiEyeOff"
              @click:append-inner="showPassword = !showPassword"
            />
            <div class="d-flex justify-space-between ml-10">
              <v-btn color="primary" variant="tonal" @click="onSubmit">
                登録
              </v-btn>
              <v-btn to="/login" color="primary" variant="text">
                ログイン
              </v-btn>
            </div>
          </v-form>
        </v-col>
      </v-card-actions>
      <v-card-text v-if="verifingEmail" class="bg-blue-lighten-5 pt-4">
        確認メールを送付しました。受信したメールからメールアドレスを確認した後に先に進んでください。
        <div class="d-flex justify-space-between ml-10">
          <v-btn to="/" color="primary" variant="tonal">
            先に進む
          </v-btn>
          <v-btn color="primary" variant="tonal" @click="resendEmail()" :disable="resending">
            {{ resending ? '送信しました' : '確認メールを再送する' }}
          </v-btn>
        </div>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup>
import router from '@/router';
import { ref } from 'vue';
import { register, sendEmailVerification } from '@/utils/client';
import { useUserStore } from '@/stores/user';
import { mdiAccountCircle, mdiLock, mdiEye, mdiEyeOff, mdiMail } from '@mdi/js';

const userStore = useUserStore();

const loginForm = ref();
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});
const showPassword = ref(false);
const errorMessage = ref(null);
const verifingEmail = ref(false);
const resending = ref(false);

const rules = {
  required: (v) => !!v || '必須項目です',
  same: (v) => v == form.value.password || 'パスワードが一致しません',
  email: (v) => /.+@.+\..+/.test(v) || 'メールアドレスが不正です',
  min8: (v) => v.length >= 8 || '8文字以上が必要です',
};

const onSubmit = async () => {
  errorMessage.value = null;
  const validate = await loginForm.value.validate();
  if (!validate.valid) {
    return;
  }
  const res = await register(form.value);
  if (res.success) {
    verifingEmail.value = true;
    userStore.login(res.data);   
  } else {
    if (res.data.errors.email == 'validation.unique') {
      errorMessage.value = "このメールアドレスは既に登録済みです。";
    } else if (res.message) {
      errorMessage.value = res.message;
    }
  }
}

const resendEmail = async () => {
  resending.value = true;
  await sendEmailVerification();
  setTimeout(() => { resending.value = false }, 30 * 1000);
}
</script>
