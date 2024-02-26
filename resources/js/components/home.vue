<template>
  <div>
    <div></div>
    <p>Hello Raunak 1234</p>
    <div>
      <apexchart :options="chartOptions" :series="series" type="line" />
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
          id: "basic-line",
        },
        xaxis: {
          categories: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
          ],
        },
      },
      // series: [
      //   {
      //     name: "Series 1",
      //     data: [30, 40, 45, 50, 49, 60, 70, 91, 125],
      //   },
      // ],
      series: [],
      data: null,
    };
  },
  created() {
    axios
      .get("/api/visitorsMonthly")
      .then((result) => {
        console.log("Received data from server:", result.data);
        // Access the data directly from the result object
        const data = result.data.data;
        // Ensure that data is an array before processing
        if (Array.isArray(data)) {
          // Initialize an empty array for series data
          const series = [];
          // Iterate through the data and format it into series
          data.forEach((item) => {
            series.push({
              name: this.getMonthName(item.month),
              data: [item.total_visitors], // Ensure data is wrapped in an array
            });
          });
          // Assign the series data to this.series
          this.series = series;
          console.log("Transformed series data:", this.series);
        } else {
          console.error("Error: Data is not an array.");
        }
      })
      .catch((error) => {
        console.error("Error fetching data from server:", error);
      });
  },
  methods: {
    getMonthName(monthNumber) {
      // Convert month number to name
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
