<template>
  <div>
    <div></div>
    <br>
    <div class="mb-10">
      <p>Select Year</p>
      <input type="number" v-model="selectedYear" placeholder="Enter Year" @change="fetchData" />
      <br>
      <br>
      <p>Monthly Bookings for {{ selectedYear }}</p>
      <div class="apex-chart-container">
        <apexchart :options="chartOptions" :series="bookingsSeries" type="bar" />
      </div>
    </div>
    <div class="pt-20 mt-30">
      <p>Monthly Users for {{ selectedYear }}</p>
      <div class="apex-chart-container">
        <apexchart :options="chartOptions" :series="usersSeries" type="bar" />
      </div>
    </div>
  </div>
</template>

<script>
import VueApexCharts from "vue3-apexcharts";

export default {
  components: {
    apexchart: VueApexCharts,
  },
  data() {
    return {
      chartOptions: {
        chart: {
          id: "bar",
        },
        xaxis: {
          categories: [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December",
          ],
        },
        stroke: {
          curve: 'stepline' // Set curve to 'stepline'
        },
      },
      selectedYear: new Date().getFullYear(),
      bookingsSeries: [],
      usersSeries: [],
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      axios
        .get(`/api/bookingsMonthly?year=${this.selectedYear}`)
        .then((result) => {
          console.log("Received booking data from server:", result.data);
          const data = result.data.data;
          if (Array.isArray(data)) {
            const series = [{
              name: "Total Bookings",
              data: data.map(item => item.total_bookings)
            }];
            this.bookingsSeries = series;
            console.log("Transformed booking series data:", this.bookingsSeries);
          } else {
            console.error("Error: Booking data is not an array.");
          }
        })
        .catch((error) => {
          console.error("Error fetching booking data from server:", error);
        });

      axios
        .get(`/api/getUsersMonthly?year=${this.selectedYear}`)
        .then((result) => {
          console.log("Received user data from server:", result.data);
          const data = result.data.data;
          if (Array.isArray(data)) {
            const series = [{
              name: "Total Users",
              data: data.map(item => item.total_users)
            }];
            this.usersSeries = series;
            console.log("Transformed user series data:", this.usersSeries);
          } else {
            console.error("Error: User data is not an array.");
          }
        })
        .catch((error) => {
          console.error("Error fetching user data from server:", error);
        });
    },
    getMonthName(monthNumber) {
      const months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
      ];
      return months[monthNumber - 1];
    },
  },
};
</script>

<style>
.apex-chart-container {
  width: 800px;
  height: 400px;
}
</style>
