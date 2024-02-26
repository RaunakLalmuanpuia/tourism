<template>
  <div class="m-4">
    <div class="text-2xl">Failed Bookings</div>
    <div class="flex flex-row items-center content-center mt-4">
      <div class="flex-col">
        <select v-model="this.$parent.unconfirmedFilterType" name="filterBy" id="filterBy"
          class="w-36 form-select appearance-none block  px-3 py-1.5 text-base  font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none">
          <option value="" selected disabled>Select Filter</option>
          <option value="order_id">Order ID</option>
        </select>
      </div>
      <div class="flex-col">
        <input v-model="this.$parent.unconfirmedSearchKey" type="text" name="key" id="key"
          class="w-full form-select appearance-none block  px-3 py-1.5 text-base  font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat border border-solid border-gray-300 transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none">
      </div>
      <div class="flex-col">
        <button
          @click="fetchData('/api/failed-bookings/?page=1&user_id='+this.user.id+'&key='+this.$parent.unconfirmedSearchKey+'&type='+this.$parent.unconfirmedFilterType)"
          class="p-2 rounded-r-lg bg-colorx">
          <svg fill="#ffffff" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="20px" height="20px">
            <path
              d="M 13 3 C 7.4889971 3 3 7.4889971 3 13 C 3 18.511003 7.4889971 23 13 23 C 15.396508 23 17.597385 22.148986 19.322266 20.736328 L 25.292969 26.707031 A 1.0001 1.0001 0 1 0 26.707031 25.292969 L 20.736328 19.322266 C 22.148986 17.597385 23 15.396508 23 13 C 23 7.4889971 18.511003 3 13 3 z M 13 5 C 17.430123 5 21 8.5698774 21 13 C 21 17.430123 17.430123 21 13 21 C 8.5698774 21 5 17.430123 5 13 C 5 8.5698774 8.5698774 5 13 5 z" />
          </svg>
        </button>
      </div>
    </div>
    <div class="w-full p-4 mt-4 bg-white rounded">
      <table class="table w-full">
        <thead>
          <tr>
            <th
              class="p-4 text-sm leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
              Order ID</th>
            <th
              class="p-4 text-sm leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
              Name</th>
            <th
              class="p-4 text-sm leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
              Email</th>
            <th
              class="p-4 text-sm leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
              Phone</th>
            <th
              class="p-4 text-sm leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
              Lodge</th>
            <th
              class="p-4 text-sm leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
              Room Required</th>
            <th
              class="p-4 text-sm leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
              Period</th>
            <th
              class="p-4 text-sm leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
              Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(booking, index) in bookings" :key="index">
            <td class="p-4 leading-4 tracking-wider text-center bg-white border-b border-gray-200">{{ booking.order_id
            }}</td>
            <td class="p-4 leading-4 tracking-wider text-center bg-white border-b border-gray-200">{{ booking.users.name
            }}</td>
            <td class="p-4 leading-4 tracking-wider text-center bg-white border-b border-gray-200">{{
            booking.users.email }}</td>
            <td class="p-4 leading-4 tracking-wider text-center bg-white border-b border-gray-200">{{
            booking.users.phone }}</td>
            <td class="p-4 leading-4 tracking-wider text-center bg-white border-b border-gray-200">
              {{ booking.lodge_room_data.lodge_room_types.name }} in
              <br>
              {{ booking.lodge_room_data.lodges.name }}
            </td>
            <td class="p-4 leading-4 tracking-wider text-center bg-white border-b border-gray-200">{{
            booking.number_of_room_require }}</td>
            <td class="p-4 leading-4 tracking-wider text-center bg-white border-b border-gray-200">{{
            format_date(booking.check_in) }} to {{ format_date(booking.check_out) }}</td>
            <td class="p-4 text-xs leading-4 tracking-wider text-center bg-white border-b border-gray-200">
              <router-link :to="{name: 'failedBookingDetail', params: {id: booking.order_id}}"
                class="p-2 text-white bg-colorx hover:bg-green-400">Track Payment</router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="w-full mt-10">
      <div class="flex flex-row items-center content-center justify-center">
        <div class="flex-col">
          <button v-if="previousLink != null"
            @click="fetchData(previousLink+'&user_id='+user.id+'&key='+this.unconfirmedSearchKey+'&type='+this.unconfirmedFilterType)"
            class="p-2 pl-4 pr-4 text-white rounded-l-lg bg-colorx">Previous</button>
          <button v-else disabled class="p-2 pl-4 pr-4 text-white bg-gray-500 rounded-l-lg">Previous</button>
        </div>
        <div class="flex-col">
          <button disabled class="p-2 pl-4 pr-4 border-gray-500">
            Showing {{ fromData }} to {{ toData }} of {{ total }} records
          </button>
        </div>
        <div class="flex-col">
          <button v-if="nextLink != null"
            @click="fetchData(nextLink+'&user_id='+user.id+'&key='+this.unconfirmedSearchKey+'&type='+this.unconfirmedFilterType)"
            class="p-2 pl-4 pr-4 text-white rounded-r-lg bg-colorx">Next</button>
          <button v-else disabled class="p-2 pl-4 pr-4 text-white bg-gray-500 rounded-r-lg">Next</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import moment from 'moment';

  export default{
    data(){
      return {
        user: null,
        bookings: [],
        previousLink: '',
        nextLink: '',
        fromData: 0,
        toData: 0,
        total: 0,
      }
    },
    mounted(){
      this.user = User;
      this.fetchData("/api/failed-bookings/?page=1&user_id=" + this.user.id + '&key=&type=');
    },
    methods: {
      format_date(value) {
        var date;
        if (value) {
          date = moment(String(value)).format('DD/MM/YYYY');
          if (date == 'Invalid date') {
            let splitDate = value.split('-');
            let convertedDate = splitDate[2] + '-' + splitDate[1] + '-' + splitDate[0];
            date = moment(String(convertedDate)).format('DD/MM/YYYY');
          }
          return date;
        }
      },
      fetchData(url){
        axios.get(url)
        .then((res) => {
          this.bookings = res.data.data;
          this.nextLink = res.data.next_page_url;
          this.previousLink = res.data.prev_page_url;
          this.fromData = res.data.from;
          this.toData = res.data.to;
          this.total = res.data.total;
        })
        .catch((err) => {
          throw err;
        });
      }
    }
  }
</script>
