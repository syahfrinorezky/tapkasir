import Alpine from "alpinejs";
import "@fortawesome/fontawesome-free/js/all.js";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);
window.Chart = Chart;
window.Alpine = Alpine;

Alpine.start();