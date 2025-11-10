import axios from 'axios';
import router from '@/router';
import { useUserStore } from '@/stores/user';

const client = axios.create({
  baseURL: '/api',
  withCredentials: true,
  withXSRFToken: true,
});

// レスポンスインターセプター
client.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    const userStore = useUserStore();
    if ([401, 419].includes(error.response?.status)) {
      userStore.logout();
      router.push({ name: 'login'});
    } else if (error.response?.status == 429) {
      return { 'data': { 'success': false, 'message': '試行回数が多すぎます。しばらくしてから再度お試しください。' } };
    }
    return Promise.reject(error);
  }
)

export const register = async (form) => {
  try {
    await axios.get('/sanctum/csrf-cookie');
    const res = await client.post('/register', form);
    console.log("register: ", res);
    return res.data;
  } catch (err) {
    console.log("api/register err: ", err);
    return err.response;
  }
}

export const login = async (form) => {
  try {
    await axios.get('/sanctum/csrf-cookie');
    const res = await client.post('/login', form);
    console.log("api/login", res);
    return res.data;
  } catch (err) {
    console.log("api/login error", err);
    return null;
  }
}

export const logout = async () => {
  try {
    const res = await client.post('/logout');
  } catch (err) {
    console.log("api/logout", err);
  }
}

export const sendEmailVerification = async () => {
  try {
    const res = await client.post('/email/verification-notification');
    return res.data;
  } catch (err) {
    console.log("verification-notification: ", err);
  }
}

export const verifyEmail = async (params, query) => {
  try {
    const res = await axios.get(`/api/email/verify/${params.id}/${params.hash}?expires=${query.expires}&signature=${query.signature}`,
        { withCredentials: true, withXSRFToken: true });
    console.log("email/verify: ", res);
    return res.data;
  } catch (err) {
    console.log("email/verify err: ", err);
    return err;
  }
}

export const getDiary = async (date = null) => {
  if (date == null) {
    const today = new Date();
    const month = ('0' + (today.getMonth() + 1)).slice(-2);
    date = today.getFullYear() + month + ('0' + today.getDate()).slice(-2);
  }
  try {
    const res = await client.get('/diary/' + date);
    console.log("getDiary: ", res);
    return res.data;
  } catch (err) {
    console.log("getDiary error: ", err);
  }
}
