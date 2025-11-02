function transactionsManagement() {
  return {
    date: new Date().toISOString().slice(0, 10),
    shiftId: "",
    shifts: [],
    transactions: [],
    items: [],
    showItemsModal: false,
    currentTransactionId: null,
    currentTransactionNo: null,
    autoRefresh: true,
    _autoRefreshTimer: null,
    dataTransactionsPage: 1,
    dataTransactionsPageSize: 10,

    init() {
      if (!this.date) this.date = new Date().toISOString().slice(0, 10);
      this.fetchShifts().then(() => {
        this.fetchTransactions();
        if (this.autoRefresh) this.startAuto();
      });

      this.$watch("autoRefresh", (val) => {
        if (val) this.startAuto();
        else this.stopAuto();
      });
      this.$watch("date", () => (this.dataTransactionsPage = 1));
      this.$watch("shiftId", () => (this.dataTransactionsPage = 1));
    },

    get filteredTransactions() {
      return Array.isArray(this.transactions) ? this.transactions : [];
    },

    get paginatedTransactions() {
      const start =
        (this.dataTransactionsPage - 1) * this.dataTransactionsPageSize;
      const end = start + this.dataTransactionsPageSize;
      return this.filteredTransactions.slice(start, end);
    },

    get totalTransactionPages() {
      return (
        Math.ceil(
          this.filteredTransactions.length / this.dataTransactionsPageSize
        ) || 1
      );
    },

    changeDataTransactionPage(page) {
      if (page >= 1 && page <= this.totalTransactionPages)
        this.dataTransactionsPage = page;
    },

    getDataTransactionsNumber() {
      return Array.from(
        { length: this.totalTransactionPages },
        (_, i) => i + 1
      );
    },

    getTransactionRowNumber(i) {
      return (
        (this.dataTransactionsPage - 1) * this.dataTransactionsPageSize + i + 1
      );
    },

    formatCurrency(a) {
      return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
      }).format(a);
    },

    async fetchShifts() {
      try {
        const res = await fetch("/admin/shifts/data", {
          headers: { Accept: "application/json" },
        });
        if (!res.ok) throw new Error("Gagal memuat data shift");
        const json = await res.json();
        this.shifts = json.shifts || [];
      } catch (err) {
        console.error(err);
        this.shifts = [];
      }
    },

    async fetchTransactions() {
      try {
        const params = new URLSearchParams();
        if (this.date) params.set("date", this.date);
        if (this.shiftId) params.set("shift_id", this.shiftId);
        const url =
          "/admin/transactions/data" +
          (params.toString() ? "?" + params.toString() : "");

        const res = await fetch(url, {
          headers: { Accept: "application/json" },
        });
        if (!res.ok) throw new Error("Gagal memuat transaksi");
        const json = await res.json();
        this.transactions = json.transactions || [];
      } catch (err) {
        console.error(err);
        this.transactions = [];
      }
    },

    startAuto() {
      this.stopAuto();
      this._autoRefreshTimer = setInterval(
        () => this.fetchTransactions(),
        5000
      );
    },

    stopAuto() {
      if (this._autoRefreshTimer) {
        clearInterval(this._autoRefreshTimer);
        this._autoRefreshTimer = null;
      }
    },

    async openItems(transactionId) {
      this.currentTransactionId = transactionId;
      // set the human-readable transaction number if available
      try {
        const tx = Array.isArray(this.transactions)
          ? this.transactions.find(
              (t) => String(t.id) === String(transactionId)
            )
          : null;
        this.currentTransactionNo = tx?.no_transaction ?? null;
      } catch (e) {
        this.currentTransactionNo = null;
      }
      this.items = [];
      this.showItemsModal = true;
      try {
        const res = await fetch("/admin/transactions/items/" + transactionId, {
          headers: { Accept: "application/json" },
        });
        if (!res.ok) throw new Error("Gagal memuat items");
        const json = await res.json();
        this.items = json.items || [];
      } catch (err) {
        console.error(err);
        this.items = [];
      }
    },

    closeItems() {
      this.showItemsModal = false;
      this.currentTransactionId = null;
      this.currentTransactionNo = null;
      this.items = [];
    },
  };
}
