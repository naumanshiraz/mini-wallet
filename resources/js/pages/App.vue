<template>
  <div class="min-h-screen bg-gray-100 flex flex-col items-center py-8">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl px-8 py-6">
      <h1 class="text-3xl font-bold text-blue-700 mb-8 text-center">Mini Wallet</h1>
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
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import TransferForm from '../components/Transfer/TransferForm.vue';
import TransactionsList from '../components/Transfer/TransactionsList.vue';

const currentUserId = ref(null);

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/user');
    currentUserId.value = data.id;
  } catch (e) {
    console.error('Authentication required:', e);
  }
});

function onTransferSuccess(transaction) {
  const txList = (refs.txList);
  if (txList && txList.fetchTransactions) txList.fetchTransactions();
}

const refs = {};
</script>