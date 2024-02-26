import { createApp } from "vue";
import VueApexCharts from "vue3-apexcharts";
require("./bootstrap");
import App from "./App.vue";
import router from "./router";
import store from "./store";

const app = createApp(App);
app.use(router);
app.use(VueApexCharts);
app.use(store);
app.mount("#app");
