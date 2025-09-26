<template>
  <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8">
    <div class="mb-6 flex justify-center gap-2">
      <button
          :class="tab==='login' ? activeTab : inactiveTab"
          @click="tab='login'">
        Login
      </button>
      <button
          :class="tab==='register' ? activeTab : inactiveTab"
          @click="tab='register'">
        Register
      </button>
    </div>
    <form v-if="tab==='login'" @submit.prevent="login" class="flex flex-col gap-4">
      <label class="text-gray-700 font-semibold">Email</label>
      <input v-model="loginEmail" placeholder="Email" type="email" class="input"/>
      <label class="text-gray-700 font-semibold">Password</label>
      <input v-model="loginPass" placeholder="Password" type="password" class="input"/>
      <button :disabled="loading" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg shadow disabled:opacity-50">Login</button>
      <div v-if="error" class="text-red-500 mt-1 text-sm">{{ error }}</div>
    </form>
    <form v-else @submit.prevent="register" class="flex flex-col gap-4">
      <label class="text-gray-700 font-semibold">Name</label>
      <input v-model="regName" placeholder="Name" type="text" class="input"/>
      <label class="text-gray-700 font-semibold">Email</label>
      <input v-model="regEmail" placeholder="Email" type="email" class="input"/>
      <label class="text-gray-700 font-semibold">Password</label>
      <input v-model="regPass" placeholder="Password" type="password" class="input"/>
      <label class="text-gray-700 font-semibold">Confirm Password</label>
      <input v-model="regPass2" placeholder="Confirm Password" type="password" class="input"/>
      <button :disabled="loading" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg shadow disabled:opacity-50">Register</button>
      <div v-if="error" class="text-red-500 mt-1 text-sm">{{ error }}</div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
const emit = defineEmits(['auth-success']);

const tab = ref('login');
const loginEmail = ref('');
const loginPass = ref('');
const regName = ref('');
const regEmail = ref('');
const regPass = ref('');
const regPass2 = ref('');
const loading = ref(false);
const error = ref('');

const activeTab = "px-4 py-2 bg-blue-600 text-white font-bold rounded-t-lg";
const inactiveTab = "px-4 py-2 bg-gray-200 text-gray-700 rounded-t-lg";

async function login() {
  error.value = ''; loading.value = true;
  try {
    const { data } = await axios.post('/api/login', { email: loginEmail.value, password: loginPass.value });
    emit('auth-success', data);
  } catch(e) {
    error.value = (e.response && e.response.data && e.response.data.message) || "Login failed";
  } finally { loading.value = false; }
}
async function register() {
  error.value = ''; loading.value = true;
  if (regPass.value !== regPass2.value) {
    error.value = "Passwords do not match"; loading.value = false; return;
  }
  try {
    const { data } = await axios.post('/api/register', {
      name: regName.value, email: regEmail.value,
      password: regPass.value, password_confirmation: regPass2.value
    });
    emit('auth-success', data);
  } catch(e) {
    error.value = (e.response && e.response.data && e.response.data.message) || "Registration failed";
  } finally { loading.value = false; }
}
</script>