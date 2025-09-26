<template>
  <div>
    <h3 class="text-xl font-semibold text-blue-800 mb-4">Send Money</h3>
    <form @submit.prevent="submit" class="flex flex-col gap-4">
      <div>
        <label class="block text-gray-700 mb-1">Receiver ID</label>
        <input v-model="receiver_id" type="number" required
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300" />
      </div>
      <div>
        <label class="block text-gray-700 mb-1">Amount</label>
        <input v-model="amount" type="number" step="0.01" min="0.01" required
               class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300" />
      </div>
      <button :disabled="loading"
              class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg shadow disabled:opacity-50">
        Send
      </button>
      <div v-if="error" class="text-red-500 mt-1 text-sm">{{ error }}</div>
      <div v-if="success" class="text-green-600 mt-1 text-sm">{{ success }}</div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
const emit = defineEmits(['transfer-success']);

const receiver_id = ref('');
const amount = ref('');
const loading = ref(false);
const error = ref('');
const success = ref('');

async function submit() {
  error.value = '';
  success.value = '';
  loading.value = true;
  try {
    const { data } = await axios.post('/api/transactions', {
      receiver_id: parseInt(receiver_id.value),
      amount: parseFloat(amount.value),
    });
    success.value = data.message || 'Sent';
    emit('transfer-success', data.transaction);

    receiver_id.value = '';
    amount.value = '';
  } catch (err) {
    if (err.response && err.response.data && err.response.data.message) {
      error.value = err.response.data.message;
    } else {
      error.value = 'Unexpected error';
    }
  } finally {
    loading.value = false;
  }
}
</script>