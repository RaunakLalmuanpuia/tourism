<template>
  <div class="container px-8 mt-10 ">
    <button class="mt-10 ml-5" onclick="history.back()">Go Back</button>
    <div v-if="loading" class="flex items-center justify-center">
      Loading
      <div class="w-8 h-8 spinner-border animate-spin " role="status">
        <span class="visually-hidden">.</span>
      </div>
    </div>
    <div v-else>
      <div class="mx-2 mb-6 text-2xl font-semibold text-black">{{ id }}</div>
      <div class="flex flex-col mt-8">
        <div class="py-2 -my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
          <div
            class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
            <div class="container p-4 mx-auto bg-white">
              <div class="text-xl">Transaction Details</div>
              <hr>
              <table class="table w-full">
                <tr>
                  <td>Date</td>
                  <td>{{ details.txnDate }}</td>
                </tr>
                <tr>
                  <td>Status</td>
                  <td>{{ resultInfo.resultStatus }}</td>
                </tr>
                <tr>
                  <td>Amount</td>
                  <td>{{ details.txnAmount }}</td>
                </tr>
              </table>
              <br>
              <button @click="resentConfirmation" v-if='resultInfo.resultStatus == "TXN_SUCCESS"'
                class="p-2 pl-4 pr-4 text-sm text-white bg-green-600 rounded-full hover:bg-green-500">Resend
                Confirmation</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import axios from 'axios';
import { allowedNodeEnvironmentFlags } from 'process';
  export default{
    data(){
      return {
        id: null,
        details: [],
        loading: false,
        resultInfo: [],
      }
    },
    mounted(){
      this.id = this.$route.params.id;
      this.checkTransaction();
    },
    methods: {
      checkTransaction(){
        this.loading = true;
        axios.get('/api/check-status?orderId=' + this.id)
        .then((res) => {
          this.loading = false;
          this.details = res.data.response.response.body;
          this.resultInfo = this.details.resultInfo;
          if (this.resultInfo.resultStatus == 'TXN_FAILURE'){
            this.updateTransactionFailure(this.id);
          }
        })
        .catch((err) => {
          this.loading = false;
          throw err;
        })
      },
      updateTransactionFailure(id){
        this.loading = true;
        axios.post('/api/update-failed-transaction', {orderId: id})
        .then((res) => {
          if(res.data == 'saved'){
            alert('Transaction failed and was moved to archive');
            this.$router.push({name: 'failedBookings'});
          }else{
            this.loading = false;
          }
        })
        .then((err) => {
          this.loading = false;
          throw err;
        })
      },
      resentConfirmation(){
        this.loading = true;
        axios.post('/api/resend-confirmation', {
          orderId: this.id,
          currency: 'INR',
          status: 'TXN_SUCCESS',
          amount: this.details.txnAmount,
          transactionId: this.details.txnId,
        })
        .then((res) => {
          if(res.data.success == "Email resend successful"){
            alert('Confirmation Mail Sent Successfully')
          }
          this.loading = false;
          this.$router.push({name: 'failedPayments'});
        })
        .catch((err) => {
          this.loading = false;
          throw err;
        })
      }
    }
  }
</script>
