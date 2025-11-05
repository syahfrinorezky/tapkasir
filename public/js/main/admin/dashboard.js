document.addEventListener("alpine:init", () => {
  Alpine.data("dashboard", () => ({
    loaded: false,
    autoRefresh: true,
    _autoRefreshTimer: null,
    salesChart: null,
    morningChart: null,
    nightChart: null,
    data: {
      todaySales: 0,
      activeCashiers: 0,
      pendingUser: 0,
      productNeedRestock: 0,
      labels: [],
      totals: [],
      morningHours: [],
      morningTotals: [],
      nightHours: [],
      nightTotals: [],
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
        const res = await fetch("/admin/dashboard/data");
        const json = await res.json();

        this.data = {
          todaySales: json.todaySales,
          activeCashiers: json.activeCashiers,
          pendingUser: json.pendingUser,
          productNeedRestock: json.productNeedRestock,
          labels: json.labels,
          totals: json.totals,
          morningHours: json.morningHours,
          morningTotals: json.morningTotals,
          nightHours: json.nightHours,
          nightTotals: json.nightTotals,
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

      // destroy old charts to avoid leaks
      try {
        if (this.salesChart) this.salesChart.destroy();
      } catch (e) {}
      try {
        if (this.morningChart) this.morningChart.destroy();
      } catch (e) {}
      try {
        if (this.nightChart) this.nightChart.destroy();
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

      const morningCtx = document
        .getElementById("morningShiftChart")
        .getContext("2d");
      this.morningChart = new Chart(morningCtx, {
        type: "line",
        data: {
          labels: data.morningHours,
          datasets: [
            {
              label: "Shift Pagi (Rp)",
              data: data.morningTotals,
              borderColor: "rgba(234,179,8,1)",
              backgroundColor: "rgba(234,179,8,0.1)",
              borderWidth: 2,
              tension: 0.3,
              pointRadius: 4,
            },
          ],
        },
        options: {
          responsive: true,
        },
      });

      const nightCtx = document
        .getElementById("nightShiftChart")
        .getContext("2d");
      this.nightChart = new Chart(nightCtx, {
        type: "line",
        data: {
          labels: data.nightHours,
          datasets: [
            {
              label: "Shift Malam (Rp)",
              data: data.nightTotals,
              borderColor: "rgba(37,99,235,1)",
              backgroundColor: "rgba(37,99,235,0.1)",
              borderWidth: 2,
              tension: 0.3,
              pointRadius: 4,
            },
          ],
        },
        options: {
          responsive: true,
        },
      });
    },

    startAuto() {
      this.stopAuto();
      this._autoRefreshTimer = setInterval(
        () => this.fetchDataAndRender(),
        60000
      );
    },

    stopAuto() {
      if (this._autoRefreshTimer) {
        clearInterval(this._autoRefreshTimer);
        this._autoRefreshTimer = null;
      }
    },
  }));
});
