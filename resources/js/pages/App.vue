<template>
  <div class="min-h-screen bg-gray-100 flex flex-col items-center py-8">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl px-8 py-6">
      <h1 class="text-3xl font-bold text-blue-700 mb-8 text-center">Mini Wallet</h1>
      <div v-if="!isAuthenticated">
        <LoginRegister @auth-success="onAuthSuccess"/>
      </div>
      <div v-else>
        <div class="flex flex-col md:flex-row gap-8">
          <div class="flex-1">
            <div class="bg-blue-50 rounded-xl shadow p-6 mb-4">
              <TransferForm @transfer-success="onTransferSuccess" :currentUserId="currentUserId" />
            </div>
          </div>
          <div class="flex-1">
            <div class="bg-gray-50 rounded-xl shadow p-6">
              <TransactionsList :currentUserId="currentUserId" ref="txList" />
            </div>
          </div>
        </div>
        <button @click="logout" class="mt-8 bg-red-500 text-white px-5 py-2 shadow font-semibold hover:bg-red-700">
          Logout
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import TransferForm from '../components/Transfer/TransferForm.vue';
import TransactionsList from '../components/Transfer/TransactionsList.vue';
import LoginRegister from '../components/Common/LoginRegister.vue';

const isAuthenticated = ref(false);
const currentUserId = ref(null);

function setAuth(token, user) {
  if (token) {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
    isAuthenticated.value = true;
    currentUserId.value = user.id;
    localStorage.setItem('wallet_token', token);
  } else {
    delete axios.defaults.headers.common['Authorization'];
    isAuthenticated.value = false;
    currentUserId.value = null;
    localStorage.removeItem('wallet_token');
  }
}

function checkAuth() {
  const token = localStorage.getItem('wallet_token');
  if (token) {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
    axios.get('/api/user').then(({data}) => {
      isAuthenticated.value = true;
      currentUserId.value = data.id;
    }).catch(() => setAuth(null, null));
  } else {
    setAuth(null, null);
  }
}

onMounted(checkAuth);

function onAuthSuccess({ token, user }) {
  setAuth(token, user);
}

function onTransferSuccess(transaction) {
  const txList = (refs.txList);
  if (txList && txList.fetchTransactions) txList.fetchTransactions();
}

async function logout() {
  await axios.post('/api/logout');
  setAuth(null, null);
  location.reload();
}

const refs = {};
</script>