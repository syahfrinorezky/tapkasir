function cashierProducts() {
  return {
    products: [],
    categories: [],
    locations: [],
    message: "",
    error: "",
    isLoading: false,
    isSubmittingRestock: false,
    searchQuery: "",
    activeCategoryFilter: "all",
    activeStockFilter: "all",
    activeSort: "newest",
    openFilterModal: false,
    showAllCategories: false,
    tempFilters: {
      category: "all",
      stock: "all",
      sort: "newest",
    },
    dataProductPage: 1,
    dataProductPageSize: 10,

    restocks: [],
    restocksPage: 1,
    restocksPageSize: 5,

    openRestockModal: false,
    openDetailModal: false,
    selectedProduct: null,
    openProofModal: false,
    selectedRestock: null,
    restockQty: "1",
    restockNote: "",
    restockExpiredDate: "",
    restockPurchasePrice: "",
    restockRack: "",
    restockRow: "",

    restockLocationId: "",
    receiptTempPath: "",
    receiptFileName: "",
    receiptUploading: false,

    init() {
      this.fetchProducts();
      this.fetchMyRestocks();
    },

    get filteredProducts() {
      let list = Array.isArray(this.products) ? [...this.products] : [];

      const getStock = (p) => {
        const s = parseInt(p.stock);
        return isNaN(s) ? 0 : s;
      };
      const getPrice = (p) => {
        const pr = parseFloat(p.price);
        return isNaN(pr) ? 0 : pr;
      };

      if (this.activeCategoryFilter && this.activeCategoryFilter !== "all") {
        list = list.filter(
          (p) => String(p.category_id) === String(this.activeCategoryFilter)
        );
      }

      if (this.activeStockFilter !== 'all') {
        if (this.activeStockFilter === 'available') {
          list = list.filter(p => getStock(p) > 0);
        } else if (this.activeStockFilter === 'low') {
          list = list.filter(p => getStock(p) > 0 && getStock(p) < 15);
        } else if (this.activeStockFilter === 'empty') {
          list = list.filter(p => getStock(p) <= 0);
        }
      }

      if (this.searchQuery && this.searchQuery.trim() !== "") {
        const q = this.searchQuery.toLowerCase();
        list = list.filter(
          (p) =>
            (p.product_name || "").toLowerCase().includes(q) ||
            (p.barcode || "").toLowerCase().includes(q)
        );
      }

      list.sort((a, b) => {
        switch (this.activeSort) {
          case 'price_high':
            return getPrice(b) - getPrice(a);
          case 'price_low':
            return getPrice(a) - getPrice(b);
          case 'stock_low':
            return getStock(a) - getStock(b);
          case 'newest':
          default:
            return (b.id || 0) - (a.id || 0);
        }
      });

      return list;
    },
    get visibleCategories() {
      if (this.showAllCategories) return this.categories;
      return this.categories.slice(0, 8);
    },
    openFilter() {
      this.tempFilters = {
        category: this.activeCategoryFilter,
        stock: this.activeStockFilter,
        sort: this.activeSort
      };
      this.openFilterModal = true;
    },
    applyFilters() {
      this.activeCategoryFilter = this.tempFilters.category;
      this.activeStockFilter = this.tempFilters.stock;
      this.activeSort = this.tempFilters.sort;
      this.dataProductPage = 1;
      this.openFilterModal = false;
    },
    resetFilters() {
      this.tempFilters = {
        category: 'all',
        stock: 'all',
        sort: 'newest'
      };
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

    stockClass(v) {
      const n = Number(v || 0);
      if (n < 15) return "text-red-600 font-semibold";
      if (n < 30) return "text-yellow-600 font-semibold";
      return "text-gray-700";
    },

    async fetchProducts() {
      try {
        this.isLoading = true;
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
        this.locations = Array.isArray(data.locations) ? data.locations : [];
      } catch (e) {
        console.error(e);
        this.error = "Terjadi kesalahan memuat produk.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.isLoading = false;
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

    openViewProof(restock) {
      this.selectedRestock = restock;
      this.openProofModal = true;
    },

    openRestock(p) {
      this.selectedProduct = { ...p };
      this.restockQty = "1";
      this.restockNote = "";
      this.restockExpiredDate = "";
      this.restockPurchasePrice = "";
      this.restockPurchasePrice = "";
      this.restockRack = "";
      this.restockRow = "";

      this.restockLocationId = "";
      this.receiptTempPath = "";
      this.receiptFileName = "";
      this.receiptUploading = false;
      this.openRestockModal = true;
    },
    closeRestock() {
      this.openRestockModal = false;
      this.selectedProduct = null;
    },
    receiptPick() {
      this.$refs?.receiptInput?.click?.();
    },
    async receiptChange(e) {
      const file = e?.target?.files?.[0];
      if (file) await this.uploadReceipt(file);
    },
    async handleReceiptDrop(e) {
      const dt = e.dataTransfer;
      if (!dt || !dt.files || !dt.files.length) return;
      const file = dt.files[0];
      await this.uploadReceipt(file);
    },
    async uploadReceipt(file) {
      try {
        this.receiptUploading = true;
        const form = new FormData();
        form.append("receipt", file);
        const res = await fetch(`/cashier/restocks/uploadReceipt`, {
          method: "POST",
          body: form,
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
        if (res.ok && data?.path) {
          this.receiptTempPath = data.path;
          this.receiptFileName = data.name || file.name;
        } else {
          this.error = data?.message || "Gagal mengunggah bukti.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        console.error(e);
        this.error = "Terjadi kesalahan saat unggah.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.receiptUploading = false;
      }
    },
    async openDetail(p) {
      this.selectedProduct = { ...p, batches: [] };
      try {
        const res = await fetch(`/cashier/products/batches/${p.id}`, {
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
        if (data && Array.isArray(data.batches)) {
          this.selectedProduct.batches = data.batches;
        } else {
          this.selectedProduct.batches = [];
        }
      } catch (e) {
        console.error(e);
        this.selectedProduct.batches = [];
      }
      this.openDetailModal = true;
    },
    closeDetail() {
      this.openDetailModal = false;
      this.selectedProduct = null;
    },
    get visibleBatches() {
      const list =
        this.selectedProduct && Array.isArray(this.selectedProduct.batches)
          ? this.selectedProduct.batches
          : [];
      const now = new Date();
      return list.filter((b) => {
        if (!b) return false;
        if (!b.expired_date) {
          return Number(b.current_stock || 0) > 0;
        }
        const d = new Date(String(b.expired_date));
        const diff = (d - now) / (1000 * 60 * 60 * 24);
        return d > now && diff > 7 && Number(b.current_stock || 0) > 0;
      });
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
        expired_date: this.restockExpiredDate || null,
        purchase_price: this.restockPurchasePrice
          ? Number(this.restockPurchasePrice)
          : null,
        rack: (this.restockRack || "").trim() || null,
        row: (this.restockRow || "").trim() || null,

        location_id: this.restockLocationId || null,
        receipt_temp: this.receiptTempPath || null,
      };
      try {
        this.isSubmittingRestock = true;
        const res = await fetch(`/cashier/restocks`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify(payload),
        });
        const data = await res.json();

        this.isSubmittingRestock = false;

        if (res.ok) {
          this.openRestockModal = false;
          this.message = data?.message || "Permintaan restock dikirim.";
          this.fetchMyRestocks();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data?.message || "Gagal mengirim permintaan.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.isSubmittingRestock = false;
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },
    onRestockLocationChange() {
      if (!this.restockLocationId) {
        this.restockRack = "";
        this.restockRow = "";
        return;
      }
      const loc = this.locations.find(
        (l) => String(l.id) === String(this.restockLocationId)
      );
      if (loc) {
        this.restockRack = loc.rack;
        this.restockRow = loc.row;
      }
    },
  };
}
