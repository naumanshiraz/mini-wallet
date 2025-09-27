<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-xl font-semibold text-blue-800">Your Balance: <span class="text-green-600">{{ balanceDisplay }}</span></h3>
      <button @click="fetchTransactions"
        class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-4 rounded-lg shadow text-sm">
        Refresh
      </button>
    </div>
    <ul>
      <li v-for="tx in transactions.data" :key="tx.id"
          class="mb-3 bg-white rounded-lg shadow p-4 flex flex-col">
        <div>
          <span class="font-bold" :class="tx.sender_id === currentUserId ? 'text-blue-700' : 'text-green-700'">
            {{ tx.sender_id === currentUserId ? 'Sent' : 'Received' }}
          </span>
          <span class="ml-2 text-gray-700 font-semibold">{{ tx.amount }}</span>
          <span class="ml-2 text-xs text-gray-400">(fee: {{ tx.commission_fee }})</span>
        </div>
        <div class="text-xs text-gray-500 mt-1">
          From: <span class="font-medium text-gray-700">{{ tx.sender.name }}</span>
          To: <span class="font-medium text-gray-700">{{ tx.receiver.name }}</span>
        </div>
        <div class="text-xs text-gray-400 mt-0.5">at {{ tx.created_at }}</div>
      </li>
    </ul>
    <div v-if="transactions.next_page_url" class="mt-4">
      <button @click="loadMore" :disabled="loading"
              class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg shadow disabled:opacity-60">
        Load more
      </button>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
  currentUserId: { type: Number, required: true }
});

const transactions = ref({data: []});
const balance = ref(0);
const loading = ref(false);

const balanceDisplay = computed(() => Number(balance.value).toFixed(2));

async function fetchTransactions(url = '/api/transactions') {
  loading.value = true;
  try {
    const { data } = await axios.get(url);
    transactions.value = data.transactions;
    balance.value = data.balance;
  } catch (e) {

  } finally {
    loading.value = false;
  }
}

async function loadMore() {
  if (!transactions.value || !transactions.value.next_page_url) return;
  loading.value = true;
  try {
    const { data } = await axios.get(transactions.value.next_page_url);
    transactions.value.data.push(...data.transactions.data);
    transactions.value.next_page_url = data.transactions.next_page_url;
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchTransactions();

  if (window.Echo && props.currentUserId) {
    console.log(`Attempting to listen to private channel: user.${props.currentUserId}`);

    window.Echo.private(`user.${props.currentUserId}`)
      .listen('.MoneyTransferred', (e) => {
        console.log('MoneyTransferred event received:', e);
        if (!e || !e.transaction) return;
        const tx = e.transaction;
        tx.sender = e.sender;
        tx.receiver = e.receiver;
        
        if (tx.sender_id === props.currentUserId) balance.value = e.sender.balance;
        if (tx.receiver_id === props.currentUserId) balance.value = e.receiver.balance;
        if (transactions.value && transactions.value.data) {
          transactions.value.data.unshift(tx);
        }
      });
  }
});

defineExpose({ fetchTransactions });
</script>