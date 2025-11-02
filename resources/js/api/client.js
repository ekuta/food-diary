import axios from 'axios';
import router from '@/router';
import { useUserStore } from '@/stores/user';

const client = axios.create({
  baseURL: '/api'
});

// レスポンスインターセプター
client.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    const userStore = useUserStore();
    // サーバーが401 Unauthorizedを返した場合
    if (error.response && (error.response.status === 401 || error.response.status === 419)) {
      userStore.logout();
      router.push('/login');
      return null;
    }
    return Promise.reject(error);
  }
)

export const login = async (email, password) => {
  console.log("login");
  try {
    await axios.get('/sanctum/csrf-cookie');
    const res = await client.post('/user/login', {email: email, password: password});
    console.log("login success");
    console.log(res);
  } catch (err) {
    console.log("login error");
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
    console.log("client success");
    console.log(res);
  } catch (err) {
    console.log("client error");
    console.log(err);
  }
}
