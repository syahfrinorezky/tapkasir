document.addEventListener("alpine:init", () => {
  Alpine.data("dashboard", () => ({
    loaded: false,
    autoRefresh: true,
    _autoRefreshTimer: null,
    salesChart: null,
    hourlySalesChart: null,
    data: {
      todaySales: 0,
      todayTransactions: 0,
      todayItemsSold: 0,
      productNeedRestock: 0,
      labels: [],
      totals: [],
      hourlyLabels: [],
      hourlyTotals: [],
      topProducts: [],
      recentTransactions: [],
    },
    async init() {
      try {
        await this.fetchDataAndRender();
        this.loaded = true;
        if (this.autoRefresh) this.startAuto();
      } catch (err) {
        console.error("Gagal memuat data dashboard:", err);
      }
    },

    async fetchDataAndRender() {
      try {
        const res = await fetch(`/admin/dashboard/data`);
        const json = await res.json();

        this.data = {
          todaySales: json.todaySales,
          todayTransactions: json.todayTransactions,
          todayItemsSold: json.todayItemsSold,
          productNeedRestock: json.productNeedRestock,
          labels: json.labels,
          totals: json.totals,
          hourlyLabels: json.hourlyLabels,
          hourlyTotals: json.hourlyTotals,
          topProducts: json.topProducts,
          recentTransactions: json.recentTransactions,
        };

        this.renderCharts();
      } catch (err) {
        console.error("Gagal memuat data dashboard:", err);
      }
    },
    formatRupiah(v) {
      return "Rp " + parseFloat(v ?? 0).toLocaleString("id-ID");
    },
    renderCharts() {
      const data = this.data;

      try {
        if (this.salesChart) this.salesChart.destroy();
      } catch (e) {}
      try {
        if (this.hourlySalesChart) this.hourlySalesChart.destroy();
      } catch (e) {}

      const salesCtx = document.getElementById("salesChart").getContext("2d");
      this.salesChart = new Chart(salesCtx, {
        type: "bar",
        data: {
          labels: data.labels,
          datasets: [
            {
              type: "bar",
              label: "Total Penjualan (Rp)",
              data: data.totals,
              backgroundColor: "rgba(99,102,241,0.3)",
              borderColor: "rgba(99,102,241,1)",
              borderWidth: 1,
              borderRadius: 6,
            },
            {
              type: "line",
              label: "Tren Penjualan",
              data: data.totals,
              borderColor: "rgba(16,185,129,1)",
              backgroundColor: "rgba(16,185,129,0.1)",
              borderWidth: 2,
              tension: 0.4,
              pointRadius: 4,
            },
          ],
        },
        options: {
          responsive: true,
        },
      });

      const hourlyCtx = document.getElementById("hourlySalesChart").getContext("2d");
      this.hourlySalesChart = new Chart(hourlyCtx, {
        type: "bar",
        data: {
          labels: data.hourlyLabels,
          datasets: [
            {
              label: "Penjualan Per Jam (Rp)",
              data: data.hourlyTotals,
              backgroundColor: "rgba(245, 158, 11, 0.5)",
              borderColor: "rgba(245, 158, 11, 1)",
              borderWidth: 1,
              borderRadius: 4,
            },
          ],
        },
        options: {
          responsive: true,
        },
      });
    },

    startAuto() {
      this._autoRefreshTimer = setInterval(() => {
        this.fetchDataAndRender();
      }, 30000);
    },
    destroy() {
      if (this._autoRefreshTimer) clearInterval(this._autoRefreshTimer);
    },
  }));
});
