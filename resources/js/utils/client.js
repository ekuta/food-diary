import axios from 'axios';
import router from '@/router';
import { useUserStore } from '@/stores/user';
import { useStateStore } from '@/stores/state';

const client = axios.create({
  baseURL: '/api',
  withCredentials: true,
  withXSRFToken: true,
});


client.interceptors.request.use(request => {
  const stateStore = useStateStore();
  stateStore.loading = true;
  return request;
})

client.interceptors.response.use(
  (response) => {
    const stateStore = useStateStore();
    stateStore.loading = false;
    return response;
  },
  (error) => {
    const stateStore = useStateStore();
    stateStore.loading = false;
    const userStore = useUserStore();
    if ([401, 403, 419].includes(error.response?.status)) {
      userStore.logout();
      router.push({ name: 'login'});
    } else if (error.response?.status == 429) {
      return { 'data': { 'success': false, 'message': '試行回数が多すぎます。しばらくしてから再度お試しください。' } };
    }
    return Promise.reject(error);
  }
)

export const getUser = async () => {
  try {
    console.log("getUser");
    const res = await client.get('/user');
    return res.data;
  } catch (err) {
    console.log("getUser err: ", err);
  }
}

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

export const getDiaries = async (date) => {
  try {
    const res = await client.get('/diary', { params: { date: date } });
    console.log("getDiaries: ", res);
    return res.data;
  } catch (err) {
    console.log("getDiaries error: ", err);
    return err;
  }
}

export const getDiaryHistory = async () => {
  try {
    const res = await client.get('/diary/history');
    console.log("getDiaryHistory: ", res);
    return res.data;
  } catch (err) {
    console.log("getDiaryHistory error: ", err);
    return err;
  }
}

export const storeDiary = async (recipe) => {
  try {
    console.log("storeDiary: ", recipe);
    const res = await client.post('/diary', recipe);
    console.log("storeDiary: ", res);
    return res.data;
  } catch (err) {
    console.log("storeDiary error: ", err);
    return err;
  }
}

export const updateDiary = async (diary) => {
  try {
    console.log("updateDiary: ", diary);
    const res = await client.put('/diary/' + diary.id, diary);
    console.log("updateDiary: ", res);
    return res.data;
  } catch (err) {
    console.log("storeDiary error: ", err);
    return err;
  }
}

export const deleteDiary = async (id) => {
  try {
    const res = await client.delete('/diary/' + id);
    console.log("deleteDiary: ", res);
    return res.data;
  } catch (err) {
    console.log("storeDiary error: ", err);
    return err;
  }
}

export const getDiary = async (id) => {
  try {
    const res = await client.get('/diary/' + id);
    console.log("getDiary: ", res);
    return res.data;
  } catch (err) {
    console.log("getDiary error: ", err);
    return err;
  }
}

export const searchFood = async (searchString) => {
  try {
    const res = await client.put('/food/search', { searchString: searchString });
    console.log("searchFood: ", res);
    return res.data;
  } catch (err) {
    console.log("searchFood error: ", err);
    return err;
  }
}

export const getFood = async (food_id) => {
  try {
    const res = await client.get('/food/' + food_id);
    console.log("getFood: ", res);
    return res.data;
  } catch (err) {
    console.log("getFood error: ", err);
    return err;
  }
}

export const storeFood = async (food) => {
  try {
    console.log("storeFood: ", food);
    const res = await client.post('/food', food);
    console.log("storeFood: ", res);
    return res.data;
  } catch (err) {
    console.log("storeFood error: ", err);
    return err;
  }
}

export const updateFood = async (food) => {
  try {
    console.log("updateFood: ", food);
    const res = await client.put('/food/' + food.id, food);
    console.log("updateFood: ", res);
    return res.data;
  } catch (err) {
    console.log("storeFood error: ", err);
    return err;
  }
}

export const searchMextFood = async (searchString) => {
  try {
    const res = await client.put('/mext-food/search', { searchString: searchString });
    console.log("searchMextFood: ", res);
    return res.data;
  } catch (err) {
    console.log("searchMextFood error: ", err);
    return err;
  }
}
