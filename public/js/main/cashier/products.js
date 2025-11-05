function cashierProducts() {
  return {
    autoRefresh: true,
    _autoTimer: null,
    products: [],
    categories: [],
    message: "",
    error: "",
    searchQuery: "",
    activeCategoryFilter: "all",
    dataProductPage: 1,
    dataProductPageSize: 10,

    restocks: [],
    restocksPage: 1,
    restocksPageSize: 5,

    openRestockModal: false,
    selectedProduct: null,
    restockQty: "1",
    restockNote: "",

    init() {
      this.fetchProducts();
      this.fetchMyRestocks();
      if (this.autoRefresh) this.startAuto();
      this.$watch("autoRefresh", (val) => {
        if (val) this.startAuto();
        else this.stopAuto();
      });
    },

    get filteredProducts() {
      let list = Array.isArray(this.products) ? this.products : [];
      if (this.activeCategoryFilter && this.activeCategoryFilter !== "all") {
        list = list.filter(
          (p) => String(p.category_id) === String(this.activeCategoryFilter)
        );
      }
      if (this.searchQuery && this.searchQuery.trim() !== "") {
        const q = this.searchQuery.toLowerCase();
        list = list.filter(
          (p) =>
            (p.product_name || "").toLowerCase().includes(q) ||
            (p.barcode || "").toLowerCase().includes(q)
        );
      }
      return list;
    },
    get paginatedProducts() {
      const start = (this.dataProductPage - 1) * this.dataProductPageSize;
      const end = start + this.dataProductPageSize;
      return this.filteredProducts.slice(start, end);
    },
    get totalProductPages() {
      return (
        Math.ceil(this.filteredProducts.length / this.dataProductPageSize) || 1
      );
    },
    changeDataProductPage(page) {
      if (page >= 1 && page <= this.totalProductPages)
        this.dataProductPage = page;
    },
    getDataProductsNumber() {
      return Array.from({ length: this.totalProductPages }, (_, i) => i + 1);
    },
    getProductRowNumber(i) {
      return (this.dataProductPage - 1) * this.dataProductPageSize + i + 1;
    },
    setCategoryFilter(id) {
      this.activeCategoryFilter = id === "all" ? "all" : id;
      this.dataProductPage = 1;
    },

    get paginatedRestocks() {
      const start = (this.restocksPage - 1) * this.restocksPageSize;
      const end = start + this.restocksPageSize;
      return (this.restocks || []).slice(start, end);
    },
    get totalRestockPages() {
      return (
        Math.ceil((this.restocks || []).length / this.restocksPageSize) || 1
      );
    },
    changeRestocksPage(page) {
      if (page >= 1 && page <= this.totalRestockPages) this.restocksPage = page;
    },
    getRestocksNumber() {
      return Array.from({ length: this.totalRestockPages }, (_, i) => i + 1);
    },

    formatCurrency(a) {
      return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
      }).format(a || 0);
    },
    formatDateTime(dt) {
      if (!dt) return "-";
      try {
        const d = new Date(String(dt).replace(" ", "T"));
        return d.toLocaleString("id-ID");
      } catch {
        return dt;
      }
    },
    statusClass(st) {
      switch ((st || "").toLowerCase()) {
        case "approved":
          return "inline-flex items-center px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full";
        case "rejected":
          return "inline-flex items-center px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full";
        default:
          return "inline-flex items-center px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded-full";
      }
    },
    statusLabel(r) {
      const st = (r.status || "").toLowerCase();
      if (st === "approved") return "Disetujui";
      if (st === "rejected") return "Ditolak";
      return "Pending";
    },

    async fetchProducts() {
      try {
        const res = await fetch(`/cashier/products/data`, {
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
        if (!data || !data.products || !data.categories) {
          this.error = data?.message || "Gagal memuat data produk.";
          setTimeout(() => (this.error = ""), 3000);
          return;
        }
        this.products = Array.isArray(data.products) ? data.products : [];
        this.categories = Array.isArray(data.categories) ? data.categories : [];
      } catch (e) {
        console.error(e);
        this.error = "Terjadi kesalahan memuat produk.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async fetchMyRestocks() {
      try {
        const res = await fetch(`/cashier/restocks/my`, {
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
        if (data && Array.isArray(data.restocks)) {
          this.restocks = data.restocks;
        } else {
          this.restocks = [];
        }
      } catch (e) {
        console.error(e);
      }
    },

    startAuto() {
      this.stopAuto();
      this._autoTimer = setInterval(() => {
        this.fetchProducts();
        this.fetchMyRestocks();
      }, 5000);
    },
    stopAuto() {
      if (this._autoTimer) {
        clearInterval(this._autoTimer);
        this._autoTimer = null;
      }
    },

    openRestock(p) {
      this.selectedProduct = { ...p };
      this.restockQty = "1";
      this.restockNote = "";
      this.openRestockModal = true;
    },
    closeRestock() {
      this.openRestockModal = false;
      this.selectedProduct = null;
    },
    sanitizeQty() {
      const onlyNums = String(this.restockQty || "").replace(/[^0-9]/g, "");
      let val = parseInt(onlyNums || "1", 10);
      if (!Number.isFinite(val) || val <= 0) val = 1;
      this.restockQty = String(val);
    },
    incQty() {
      const v = parseInt(this.restockQty || "1", 10) || 1;
      this.restockQty = String(v + 1);
    },
    decQty() {
      const v = parseInt(this.restockQty || "1", 10) || 1;
      this.restockQty = String(Math.max(1, v - 1));
    },

    async submitRestock() {
      if (!this.selectedProduct?.id) return;
      const payload = {
        product_id: this.selectedProduct.id,
        quantity: parseInt(this.restockQty || "1", 10) || 1,
        note: (this.restockNote || "").trim() || null,
      };
      try {
        const res = await fetch(`/cashier/restocks`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (res.ok) {
          this.message = data?.message || "Permintaan restock dikirim.";
          this.openRestockModal = false;
          this.fetchMyRestocks();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data?.message || "Gagal mengirim permintaan.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },
  };
}
