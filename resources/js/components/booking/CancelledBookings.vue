<template>
  <div class="container px-8 mt-10">
    <div class="mx-2 mb-6 text-2xl font-semibold text-black">
      Cancelled Bookings
    </div>
    <div class="mt-4">
      Select Month
      <br />
      <month-picker-input
        @change="changeOfMonth"
        v-model="selectedMonthAndYear"
        :default-month="currentMonth"
        :default-year="currentYear"
      >
      </month-picker-input>
    </div>
    <div class="flex flex-col mt-8">
      <div class="w-full">Select Filter</div>
      <div class="flex flex-row items-center content-center">
        <div class="flex-col">
          <select
            v-model="this.$parent.cancelledFilterType"
            @change="changeFilterType"
            name="filterBy"
            id="filterBy"
            class="w-36 form-select appearance-none block px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
          >
            <option value="" selected disabled>Select Filter</option>
            <option value="name">Name</option>
            <option value="order_id">Order ID</option>
            <option value="phone">Phone</option>
            <option value="guest">Guest</option>
            <!-- <option value="check_in">Date of Arrival</option> -->
          </select>
        </div>
        <div class="flex-col">
          <input
            v-if="filtertype == 'check_in'"
            v-model="this.$parent.cancelledSearchKey"
            type="date"
            class="w-full form-select appearance-none block px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat border border-solid border-gray-300 transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
          />
          <input
            v-else
            v-model="this.$parent.cancelledSearchKey"
            type="text"
            name="key"
            id="key"
            class="w-full form-select appearance-none block px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat border border-solid border-gray-300 transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
          />
        </div>
        <div class="flex-col">
          <button @click="searchForBooking" class="p-2 rounded-r-lg bg-colorx">
            <svg
              fill="#ffffff"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 30 30"
              width="20px"
              height="20px"
            >
              <path
                d="M 13 3 C 7.4889971 3 3 7.4889971 3 13 C 3 18.511003 7.4889971 23 13 23 C 15.396508 23 17.597385 22.148986 19.322266 20.736328 L 25.292969 26.707031 A 1.0001 1.0001 0 1 0 26.707031 25.292969 L 20.736328 19.322266 C 22.148986 17.597385 23 15.396508 23 13 C 23 7.4889971 18.511003 3 13 3 z M 13 5 C 17.430123 5 21 8.5698774 21 13 C 21 17.430123 17.430123 21 13 21 C 8.5698774 21 5 17.430123 5 13 C 5 8.5698774 8.5698774 5 13 5 z"
              />
            </svg>
          </button>
        </div>
      </div>
      <br />
      <div class="p-4 bg-white rounded-xl">
        <a
          :href="
            '/download-lodge-cancelled-bookings?lodge_id=' +
            id +
            '&&month=' +
            selectedMonthAndYear.month +
            '&&year=' +
            selectedMonthAndYear.year
          "
          class="float-right px-4 py-1 mb-4 text-white bg-gray-800 hover:bg-gray-700"
          >Download Excel</a
        >
        <table class="min-w-full">
          <thead>
            <tr>
              <td
                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50"
              >
                Booking ID
              </td>
              <td
                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50"
              >
                Name
              </td>
              <td
                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50"
              >
                Contact
              </td>
              <td
                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50"
              >
                Lodge/Room
              </td>
              <td
                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50"
              >
                No. of Rooms
              </td>
              <td
                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50"
              >
                Check In/Check Out
              </td>
              <td
                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50"
              >
                Refund Date
              </td>
              <td
                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50"
              >
                Amount
              </td>
              <td
                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50"
              >
                Cancelled At
              </td>
              <td
                v-if="user.role == 'admin'"
                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-center text-gray-500 uppercase border-b border-gray-200 bg-gray-50"
              >
                Action
              </td>
            </tr>
          </thead>
          <tbody class="bg-white">
            <tr v-for="(booking, index) in bookings" :key="index">
              <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                <div
                  class="flex items-center text-center hover:underline hover:text-blue-500"
                >
                  <router-link
                    v-if="booking.order_id != null"
                    :to="{
                      name: 'cancelledBookingDetail',
                      params: { id: booking.order_id },
                    }"
                  >
                    {{ booking.order_id }}
                  </router-link>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                <div class="flex items-center text-center">
                  {{ booking.users.name }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                <div class="flex items-center text-center">
                  {{ booking.users.phone }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                <div
                  v-if="booking.lodge_room_data != null"
                  class="flex items-center text-center"
                >
                  {{ booking.lodge_room_data.lodge_room_types.name }} in
                  {{ booking.lodge_room_data.lodges.name }}
                </div>
                <div
                  v-else
                  class="flex items-center font-extrabold text-center text-red-500"
                >
                  Room/Lodge Deleted
                </div>
              </td>
              <td
                class="px-6 py-4 text-center whitespace-no-wrap border-b border-gray-200"
              >
                {{ booking.number_of_room_require }}
              </td>
              <td
                class="px-6 py-4 text-center whitespace-no-wrap border-b border-gray-200"
              >
                {{ format_date(booking.check_in) }} to
                {{ format_date(booking.check_out) }}
              </td>
              <td
                v-if="booking.payment != null"
                class="px-6 py-4 text-center whitespace-no-wrap border-b border-gray-200"
              >
                <span v-if="booking.payment.status == 'REFUNDED'">
                  {{ format_date(booking.payment.updated_at) }}
                </span>
              </td>
              <td
                v-else
                class="px-6 py-4 text-center whitespace-no-wrap border-b border-gray-200"
              ></td>
              <td
                v-if="booking.payment != null"
                class="px-6 py-4 text-center whitespace-no-wrap border-b border-gray-200"
              >
                {{ booking.payment.amount }}
              </td>
              <td
                v-else
                class="px-6 py-4 text-center whitespace-no-wrap border-b border-gray-200"
              ></td>
              <td
                class="px-6 py-4 text-center whitespace-no-wrap border-b border-gray-200"
              >
                {{ formatDateTime(booking.cancelled_at) }}
              </td>
              <td
                v-if="user.role == 'admin'"
                class="px-6 py-4 text-center whitespace-no-wrap border-b border-gray-200"
              >
                <button
                  @click="restoreCancellation(booking.id)"
                  class="px-4 py-1 text-white bg-green-800 hover:bg-green-700"
                >
                  Restore
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="w-full mt-10">
        <div class="flex flex-row items-center content-center justify-center">
          <div class="flex-col">
            <button
              v-if="previousLink != null"
              @click="changePage(previousLink)"
              class="p-2 pl-4 pr-4 text-white rounded-l-lg bg-colorx"
            >
              Previous
            </button>
            <button
              v-else
              disabled
              class="p-2 pl-4 pr-4 text-white bg-gray-500 rounded-l-lg"
            >
              Previous
            </button>
          </div>
          <div class="flex-col">
            <button
              disabled
              class="p-2 pl-4 pr-4 border-t border-b border-gray-500"
            >
              Showing {{ fromData }} to {{ toData }} of {{ total }} records
            </button>
          </div>
          <div class="flex-col">
            <button
              v-if="nextLink != null"
              @click="changePage(nextLink)"
              class="p-2 pl-4 pr-4 text-white rounded-r-lg bg-colorx"
            >
              Next
            </button>
            <button
              v-else
              disabled
              class="p-2 pl-4 pr-4 text-white bg-gray-500 rounded-r-lg"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import moment from "moment";
import { MonthPickerInput } from "vue-month-picker";

export default {
  components: {
    MonthPickerInput,
  },
  data() {
    return {
      id: null,
      bookings: null,
      filtertype: "",
      previousLink: "",
      nextLink: "",
      fromData: 0,
      toData: 0,
      total: 0,
      hasLoading: false,
      isSearched: false,
      user: [],
      today: 0,
      currentMonth: "",
      selectedMonthAndYear: {},
      currentYear: "",
    };
  },
  created() {
    this.today = Date.now();
    const current = new Date();
    this.currentMonth = current.getMonth() + 1;
    this.currentYear = current.getFullYear();
  },
  mounted() {
    this.user = User;
    this.id = this.$route.params.id;
    this.searchForBooking();
  },
  methods: {
    changeOfMonth(date) {
      this.selectedMonthAndYear = date;
      this.searchForBooking();
    },
    changeFilterType() {
      this.filterType = this.$parent.cancelledFilterType;
    },
    format_date(value) {
      var date;
      if (value) {
        date = moment(String(value)).format("DD/MM/YYYY");
        if (date == "Invalid date") {
          let splitDate = value.split("-");
          let convertedDate =
            splitDate[2] + "-" + splitDate[1] + "-" + splitDate[0];
          date = moment(String(convertedDate)).format("DD/MM/YYYY");
        }
        return date;
      }
    },
    formatDateTime(value) {
      var date;
      if (value) {
        date = moment(String(value)).format("DD/MM/YYYY h:mma");
        if (date == "Invalid date") {
          return "";
        }
        return date;
      }
    },
    changePage(url) {
      if (this.isSearched == true) {
        this.continueSearching(url);
      } else {
        axios.get(url).then((result) => {
          if (result.data.data == false) {
            this.hasLoading = false;
          } else {
            this.bookings = result.data.data;
            this.hasLoading = false;
            if (result.data.next_page_url != null) {
              this.nextLink =
                result.data.next_page_url +
                "&lodge_id=" +
                this.id +
                "&month=" +
                this.selectedMonthAndYear.month +
                "&year=" +
                this.selectedMonthAndYear.year;
            } else {
              this.nextLink = null;
            }
            if (result.data.prev_page_url != null) {
              this.previousLink =
                result.data.prev_page_url +
                "&lodge_id=" +
                this.id +
                "&month=" +
                this.selectedMonthAndYear.month +
                "&year=" +
                this.selectedMonthAndYear.year;
            } else {
              this.previousLink = null;
            }
            this.fromData = result.data.from;
            this.toData = result.data.to;
            this.total = result.data.total;
          }
        });
      }
    },

    searchForBooking() {
      let self = this;
      self.isSearched = true;
      if (self.$parent.filterType != "" && self.$parent.searchKey != "") {
        axios
          .get(
            "/api/search-for-cancelled-bookings?type=" +
              self.$parent.cancelledFilterType +
              "&key=" +
              self.$parent.cancelledSearchKey +
              "&lodge_id=" +
              this.id +
              "&month=" +
              this.selectedMonthAndYear.month +
              "&year=" +
              this.selectedMonthAndYear.year
          )
          .then((result) => {
            this.bookings = result.data.data;
            this.nextLink = result.data.next_page_url;
            this.previousLink = result.data.prev_page_url;
            this.fromData = result.data.from;
            this.toData = result.data.to;
            this.total = result.data.total;
          })
          .catch((err) => {
            throw err;
          });
      } else {
        alert("Please select filter type and enter search key");
      }
    },

    restoreCancellation(id) {
      if (confirm("Do you really want to restore this booking?")) {
        axios.post("/api/restore-cancelled-booking/" + id).then((result) => {
          if (result.status == 200) {
            this.$router.go();
          }
        });
      }
    },
    continueSearching(url) {
      let self = this;
      if (self.$parent.filterType != "" && self.$parent.searchKey != "") {
        axios
          .get(
            url +
              "&type=" +
              self.$parent.cancelledFilterType +
              "&key=" +
              self.$parent.cancelledSearchKey +
              "&lodge_id=" +
              self.id +
              "&month=" +
              self.selectedMonthAndYear.month +
              "&year=" +
              self.selectedMonthAndYear.year
          )
          .then((result) => {
            self.bookings = result.data.data;
            self.nextLink = result.data.next_page_url;
            self.previousLink = result.data.prev_page_url;
            self.fromData = result.data.from;
            self.toData = result.data.to;
            self.total = result.data.total;
          })
          .catch((err) => {
            throw err;
          });
      } else {
        alert("Please select filter type and enter search key");
      }
    },
  },
};
</script>
