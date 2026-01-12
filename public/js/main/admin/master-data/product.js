function productManagement() {
  return {
    products: [],
    categories: [],
    locations: [],
    activeRightTab: 'categories',
    showTrash: false,
    trashProducts: [],
    selectedTrash: [],
    openRestoreModal: false,
    openDeletePermanentModal: false,
    openProofModal: false,
    restoreMode: 'single',
    deletePermanentMode: 'single',
    selectedTrashItem: null,
    selectedRestock: null,
    isRestoring: false,
    isDeletingPermanent: false,
    trashType: 'product',

    showTrashCategories: false,
    trashCategories: [],
    selectedTrashCategories: [],
    selectedTrashCategoryItem: null,
    isRestoringCategory: false,
    isDeletingPermanentCategory: false,

    showTrashLocations: false,
    trashLocations: [],
    selectedTrashLocations: [],
    selectedTrashLocationItem: null,
    isRestoringLocation: false,
    isDeletingPermanentLocation: false,

    isRestoringRole: false,
    isDeletingPermanentRole: false,
    isRestoringShift: false,
    isDeletingPermanentShift: false,

    getTrashLabel() {
      if (this.trashType === 'category') return 'Kategori';
      if (this.trashType === 'location') return 'Lokasi';
      return 'Produk';
    },

    getTrashItemName() {
      if (this.trashType === 'category') return this.selectedTrashCategoryItem?.category_name;
      if (this.trashType === 'location') return `${this.selectedTrashLocationItem?.rack} - ${this.selectedTrashLocationItem?.row}`;
      return this.selectedTrashItem?.product_name;
    },

    getTrashSelectedCount() {
      if (this.trashType === 'category') return this.selectedTrashCategories.length;
      if (this.trashType === 'location') return this.selectedTrashLocations.length;
      return this.selectedTrash.length;
    },

    isLoading: false,
    isSavingProduct: false,
    isDeletingProduct: false,
    isSavingCategory: false,
    isDeletingCategory: false,
    isSavingLocation: false,
    isDeletingLocation: false,
    approvingRestockId: null,
    rejectingRestockId: null,
    selectedProduct: {
      id: null,
      product_name: "",
      price: "",
      category_id: "",
      photo: null,
    },
    stockClass(v) {
      const n = Number(v || 0);
      if (n < 15) return "text-red-600 font-semibold";
      if (n < 30) return "text-yellow-600 font-semibold";
      return "text-gray-700";
    },
    selectedCategory: {
      id: null,
      category_name: "",
    },
    selectedLocation: {
      id: null,
      rack: "",
      row: "",
      description: "",
      status: "active",
    },
    message: "",
    error: "",
    searchQuery: "",
    activeCategoryFilter: "all",
    activeStockFilter: "all",
    activeSort: "newest",
    openFilterModal: false,
    tempFilters: {
      category: 'all',
      stock: 'all',
      sort: 'newest'
    },
    showAllCategories: false,
    validationErrors: {
      product_name: "",
      price: "",
      category_id: "",
      category_name: "",
    },
    openAddProductModal: false,
    openEditProductModal: false,
    openDeleteProductModal: false,
    openViewProductModal: false,
    openAddCategoryModal: false,
    openEditCategoryModal: false,
    openDeleteCategoryModal: false,
    openAddLocationModal: false,
    openEditLocationModal: false,
    openDeleteLocationModal: false,
    barcodeImageUrl: null,
    dataProductPage: 1,
    dataProductPageSize: 10,
    dataCategoriesPage: 1,
    dataCategoriesPageSize: 5,
    locationsPage: 1,
    locationsPageSize: 5,
    trashProductPage: 1,
    trashProductPageSize: 10,
    trashCategoriesPage: 1,
    trashCategoriesPageSize: 5,
    trashLocationsPage: 1,
    trashLocationsPageSize: 5,
    restocks: [],
    restocksPage: 1,
    restocksPageSize: 5,
    _previewUrls: new Set(),
    approveModalOpen: false,
    approving: false,
    selectedRestock: null,
    restockDetails: {},

    init() {
      this.fetchData();
      this.fetchRestocks();
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
      this.openFilterModal = false;
      this.dataProductPage = 1;
    },

    resetFilters() {
      this.tempFilters = {
        category: 'all',
        stock: 'all',
        sort: 'newest'
      };
    },

    toggleTrash() {
      this.showTrash = !this.showTrash;
      if (this.showTrash) {
        this.fetchTrashData();
      }
    },
    async fetchTrashData() {
      try {
        const response = await fetch('/admin/products/trash/data');
        const data = await response.json();
        this.trashProducts = data.products;
      } catch (error) {
        console.error('Error fetching trash data:', error);
      }
    },
    toggleSelectAllTrash(e) {
      if (e.target.checked) {
        this.selectedTrash = this.trashProducts.map(p => p.id);
      } else {
        this.selectedTrash = [];
      }
    },
    restoreSelected() {
      this.trashType = 'product';
      this.restoreMode = 'multiple';
      this.openRestoreModal = true;
    },
    deletePermanentSelected() {
      this.trashType = 'product';
      this.deletePermanentMode = 'multiple';
      this.openDeletePermanentModal = true;
    },
    confirmRestore(id) {
      this.trashType = 'product';
      this.selectedTrashItem = this.trashProducts.find(p => p.id === id);
      this.restoreMode = 'single';
      this.openRestoreModal = true;
    },
    confirmDeletePermanent(id) {
      this.trashType = 'product';
      this.selectedTrashItem = this.trashProducts.find(p => p.id === id);
      this.deletePermanentMode = 'single';
      this.openDeletePermanentModal = true;
    },
    async processRestore() {
      if (this.trashType === 'category') return this.processRestoreCategory();
      if (this.trashType === 'location') return this.processRestoreLocation();

      this.isRestoring = true;
      try {
        let url = '/admin/products/restore';
        let body = {};

        if (this.restoreMode === 'single') {
          url += `/${this.selectedTrashItem.id}`;
        } else {
          body = { ids: this.selectedTrash };
        }

        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: this.restoreMode === 'multiple' ? JSON.stringify(body) : null
        });

        if (response.ok) {
          await this.fetchTrashData();
          await this.fetchData();
          this.selectedTrash = [];
          this.openRestoreModal = false;
          this.message = "Produk berhasil dipulihkan";
          setTimeout(() => (this.message = ""), 3000);
        }
      } catch (error) {
        console.error('Error restoring data:', error);
      } finally {
        this.isRestoring = false;
      }
    },
    async processDeletePermanent() {
      if (this.trashType === 'category') return this.processDeletePermanentCategory();
      if (this.trashType === 'location') return this.processDeletePermanentLocation();

      this.isDeletingPermanent = true;
      try {
        let url = '/admin/products/deletePermanent';
        let body = {};

        if (this.deletePermanentMode === 'single') {
          url += `/${this.selectedTrashItem.id}`;
        } else {
          body = { ids: this.selectedTrash };
        }

        const response = await fetch(url, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: this.deletePermanentMode === 'multiple' ? JSON.stringify(body) : null
        });

        if (response.ok) {
          await this.fetchTrashData();
          this.selectedTrash = [];
          this.openDeletePermanentModal = false;
          this.message = "Produk berhasil dihapus permanen";
          setTimeout(() => (this.message = ""), 3000);
        }
      } catch (error) {
        console.error('Error deleting data:', error);
      } finally {
        this.isDeletingPermanent = false;
      }
    },

    toggleTrashCategories() {
      this.showTrashCategories = !this.showTrashCategories;
      if (this.showTrashCategories) {
        this.fetchTrashCategories();
      }
    },
    async fetchTrashCategories() {
      try {
        const response = await fetch('/admin/products/categories/trash/data');
        const data = await response.json();
        this.trashCategories = data.categories;
      } catch (error) {
        console.error('Error fetching category trash data:', error);
      }
    },
    toggleSelectAllTrashCategories(e) {
      if (e.target.checked) {
        this.selectedTrashCategories = this.trashCategories.map(c => c.id);
      } else {
        this.selectedTrashCategories = [];
      }
    },
    restoreSelectedCategories() {
      this.trashType = 'category';
      this.restoreMode = 'multiple';
      this.openRestoreModal = true;
    },
    deletePermanentSelectedCategories() {
      this.trashType = 'category';
      this.deletePermanentMode = 'multiple';
      this.openDeletePermanentModal = true;
    },
    confirmRestoreCategory(id) {
      this.trashType = 'category';
      this.selectedTrashCategoryItem = this.trashCategories.find(c => c.id === id);
      this.restoreMode = 'single';
      this.openRestoreModal = true;
    },
    confirmDeletePermanentCategory(id) {
      this.trashType = 'category';
      this.selectedTrashCategoryItem = this.trashCategories.find(c => c.id === id);
      this.deletePermanentMode = 'single';
      this.openDeletePermanentModal = true;
    },
    async processRestoreCategory() {
      this.isRestoringCategory = true;
      try {
        let url = '/admin/products/categories/restore';
        let body = {};

        if (this.restoreMode === 'single') {
          url += `/${this.selectedTrashCategoryItem.id}`;
        } else {
          body = { ids: this.selectedTrashCategories };
        }

        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: this.restoreMode === 'multiple' ? JSON.stringify(body) : null
        });

        if (response.ok) {
          await this.fetchTrashCategories();
          await this.fetchData();
          this.selectedTrashCategories = [];
          this.openRestoreModal = false;
          this.message = "Kategori berhasil dipulihkan";
          setTimeout(() => (this.message = ""), 3000);
        }
      } catch (error) {
        console.error('Error restoring category:', error);
      } finally {
        this.isRestoringCategory = false;
      }
    },
    async processDeletePermanentCategory() {
      this.isDeletingPermanentCategory = true;
      try {
        let url = '/admin/products/categories/deletePermanent';
        let body = {};

        if (this.deletePermanentMode === 'single') {
          url += `/${this.selectedTrashCategoryItem.id}`;
        } else {
          body = { ids: this.selectedTrashCategories };
        }

        const response = await fetch(url, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: this.deletePermanentMode === 'multiple' ? JSON.stringify(body) : null
        });

        if (response.ok) {
          await this.fetchTrashCategories();
          this.selectedTrashCategories = [];
          this.openDeletePermanentModal = false;
          this.message = "Kategori berhasil dihapus permanen";
          setTimeout(() => (this.message = ""), 3000);
        }
      } catch (error) {
        console.error('Error deleting category:', error);
      } finally {
        this.isDeletingPermanentCategory = false;
      }
    },

    toggleTrashLocations() {
      this.showTrashLocations = !this.showTrashLocations;
      if (this.showTrashLocations) {
        this.fetchTrashLocations();
      }
    },
    async fetchTrashLocations() {
      try {
        const response = await fetch('/admin/products/locations/trash/data');
        const data = await response.json();
        this.trashLocations = data.locations;
      } catch (error) {
        console.error('Error fetching location trash data:', error);
      }
    },
    toggleSelectAllTrashLocations(e) {
      if (e.target.checked) {
        this.selectedTrashLocations = this.trashLocations.map(l => l.id);
      } else {
        this.selectedTrashLocations = [];
      }
    },
    restoreSelectedLocations() {
      this.trashType = 'location';
      this.restoreMode = 'multiple';
      this.openRestoreModal = true;
    },
    deletePermanentSelectedLocations() {
      this.trashType = 'location';
      this.deletePermanentMode = 'multiple';
      this.openDeletePermanentModal = true;
    },
    confirmRestoreLocation(id) {
      this.trashType = 'location';
      this.selectedTrashLocationItem = this.trashLocations.find(l => l.id === id);
      this.restoreMode = 'single';
      this.openRestoreModal = true;
    },
    confirmDeletePermanentLocation(id) {
      this.trashType = 'location';
      this.selectedTrashLocationItem = this.trashLocations.find(l => l.id === id);
      this.deletePermanentMode = 'single';
      this.openDeletePermanentModal = true;
    },
    async processRestoreLocation() {
      this.isRestoringLocation = true;
      try {
        let url = '/admin/products/locations/restore';
        let body = {};

        if (this.restoreMode === 'single') {
          url += `/${this.selectedTrashLocationItem.id}`;
        } else {
          body = { ids: this.selectedTrashLocations };
        }

        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: this.restoreMode === 'multiple' ? JSON.stringify(body) : null
        });

        if (response.ok) {
          await this.fetchTrashLocations();
          await this.fetchData();
          this.selectedTrashLocations = [];
          this.openRestoreModal = false;
          this.message = "Lokasi berhasil dipulihkan";
          setTimeout(() => (this.message = ""), 3000);
        }
      } catch (error) {
        console.error('Error restoring location:', error);
      } finally {
        this.isRestoringLocation = false;
      }
    },
    async processDeletePermanentLocation() {
      this.isDeletingPermanentLocation = true;
      try {
        let url = '/admin/products/locations/deletePermanent';
        let body = {};

        if (this.deletePermanentMode === 'single') {
          url += `/${this.selectedTrashLocationItem.id}`;
        } else {
          body = { ids: this.selectedTrashLocations };
        }

        const response = await fetch(url, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: this.deletePermanentMode === 'multiple' ? JSON.stringify(body) : null
        });

        if (response.ok) {
          await this.fetchTrashLocations();
          this.selectedTrashLocations = [];
          this.openDeletePermanentModal = false;
          this.message = "Lokasi berhasil dihapus permanen";
          setTimeout(() => (this.message = ""), 3000);
        }
      } catch (error) {
        console.error('Error deleting location:', error);
      } finally {
        this.isDeletingPermanentLocation = false;
      }
    },

    parseDetails(note) {
      if (!note) return {};
      try {
        const d = JSON.parse(note);
        if (d && typeof d === "object") return d;
        return {};
      } catch {
        return {};
      }
    },
    previewUrl(details) {
      const path = details?.receipt_image || details?.receipt_temp;
      if (!path) return "#";
      if (String(path).startsWith("http")) return path;
      return "/" + String(path).replace(/^\/+/, "");
    },
    openViewProof(restock) {
      this.selectedRestock = restock;
      this.openProofModal = true;
    },

    openApproveRestock(r) {
      this.selectedRestock = r;
      this.restockDetails = this.parseDetails(r.note);
      this.approveModalOpen = true;
      this.approving = false;
    },
    closeApproveRestock() {
      this.approveModalOpen = false;
      this.approving = false;
      this.selectedRestock = null;
      this.restockDetails = {};
    },
    async confirmApproveRestock() {
      if (!this.selectedRestock?.id) return;
      try {
        this.approving = true;
        const res = await fetch(
          `/admin/restocks/approve/${this.selectedRestock.id}`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({}),
          }
        );
        const data = await res.json();

        this.approving = false;

        if (res.ok) {
          this.closeApproveRestock();
          this.message = data?.message || "Permintaan disetujui";
          await this.fetchRestocks();
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data?.message || "Gagal menyetujui";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.approving = false;
        console.error(e);
        this.error = "Terjadi kesalahan";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    get visibleCategories() {
      const list = Array.isArray(this.categories) ? this.categories : [];
      return this.showAllCategories ? list : list.slice(0, 8);
    },

    get filteredProducts() {
      let list = Array.isArray(this.products) ? this.products : [];

      if (this.activeCategoryFilter && this.activeCategoryFilter !== "all") {
        list = list.filter(
          (p) => String(p.category_id) === String(this.activeCategoryFilter)
        );
      }

      if (this.activeStockFilter && this.activeStockFilter !== "all") {
        list = list.filter((p) => {
          const stock = Number(p.stock || 0);
          if (this.activeStockFilter === "available") return stock > 0;
          if (this.activeStockFilter === "low") return stock > 0 && stock < 15;
          if (this.activeStockFilter === "empty") return stock === 0;
          return true;
        });
      }

      if (this.searchQuery && this.searchQuery.trim() !== "") {
        const q = this.searchQuery.toLowerCase();
        list = list.filter(
          (p) =>
            (p.product_name || "").toLowerCase().includes(q) ||
            (p.barcode || "").toLowerCase().includes(q)
        );
      }

      list = list.sort((a, b) => {
        const priceA = Number(a.price || 0);
        const priceB = Number(b.price || 0);
        const stockA = Number(a.stock || 0);
        const stockB = Number(b.stock || 0);
        const idA = Number(a.id || 0);
        const idB = Number(b.id || 0);

        switch (this.activeSort) {
          case "price_high": return priceB - priceA;
          case "price_low": return priceA - priceB;
          case "stock_high": return stockB - stockA;
          case "stock_low": return stockA - stockB;
          case "newest": default: return idB - idA;
        }
      });

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
    get paginatedCategories() {
      const start = (this.dataCategoriesPage - 1) * this.dataCategoriesPageSize;
      const end = start + this.dataCategoriesPageSize;
      return this.categories.slice(start, end);
    },
    get totalCategoriesPages() {
      return (
        Math.ceil(this.categories.length / this.dataCategoriesPageSize) || 1
      );
    },
    get paginatedLocations() {
      const start = (this.locationsPage - 1) * this.locationsPageSize;
      const end = start + this.locationsPageSize;
      return this.locations.slice(start, end);
    },
    get totalLocationsPages() {
      return Math.ceil(this.locations.length / this.locationsPageSize) || 1;
    },
    get paginatedTrashProducts() {
      const start = (this.trashProductPage - 1) * this.trashProductPageSize;
      const end = start + this.trashProductPageSize;
      return this.trashProducts.slice(start, end);
    },
    get totalTrashProductPages() {
      return Math.ceil(this.trashProducts.length / this.trashProductPageSize) || 1;
    },
    get paginatedTrashCategories() {
      const start = (this.trashCategoriesPage - 1) * this.trashCategoriesPageSize;
      const end = start + this.trashCategoriesPageSize;
      return this.trashCategories.slice(start, end);
    },
    changeTrashProductPage(page) {
      if (page >= 1 && page <= this.totalTrashProductPages)
        this.trashProductPage = page;
    },
    changeTrashCategoriesPage(page) {
      if (page >= 1 && page <= this.totalTrashCategoriesPages)
        this.trashCategoriesPage = page;
    },
    changeTrashLocationsPage(page) {
      if (page >= 1 && page <= this.totalTrashLocationsPages)
        this.trashLocationsPage = page;
    },
    get totalTrashCategoriesPages() {
      return Math.ceil(this.trashCategories.length / this.trashCategoriesPageSize) || 1;
    },
    get paginatedTrashLocations() {
      const start = (this.trashLocationsPage - 1) * this.trashLocationsPageSize;
      const end = start + this.trashLocationsPageSize;
      return this.trashLocations.slice(start, end);
    },
    get totalTrashLocationsPages() {
      return Math.ceil(this.trashLocations.length / this.trashLocationsPageSize) || 1;
    },

    changeDataProductPage(page) {
      if (page >= 1 && page <= this.totalProductPages)
        this.dataProductPage = page;
    },
    changeDataCategoriesPage(page) {
      if (page >= 1 && page <= this.totalCategoriesPages)
        this.dataCategoriesPage = page;
    },
    changeLocationsPage(page) {
      if (page >= 1 && page <= this.totalLocationsPages)
        this.locationsPage = page;
    },
    getTrashProductPageNumbers() {
      return Array.from({ length: this.totalTrashProductPages }, (_, i) => i + 1);
    },
    getTrashCategoriesPageNumbers() {
      return Array.from({ length: this.totalTrashCategoriesPages }, (_, i) => i + 1);
    },
    getTrashLocationsPageNumbers() {
      return Array.from({ length: this.totalTrashLocationsPages }, (_, i) => i + 1);
    },
    getDataProductsNumber() {
      return Array.from({ length: this.totalProductPages }, (_, i) => i + 1);
    },
    getDataCategoriesNumber() {
      return Array.from({ length: this.totalCategoriesPages }, (_, i) => i + 1);
    },
    getLocationsNumber() {
      return Array.from({ length: this.totalLocationsPages }, (_, i) => i + 1);
    },
    getProductRowNumber(i) {
      return (this.dataProductPage - 1) * this.dataProductPageSize + i + 1;
    },
    getCategoryRowNumber(i) {
      return (
        (this.dataCategoriesPage - 1) * this.dataCategoriesPageSize + i + 1
      );
    },
    formatCurrency(a) {
      return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
      }).format(a);
    },

    async fetchData() {
      try {
        if (!this.products.length && !this.categories.length)
          this.isLoading = true;
        const res = await fetch(`/admin/products/data`, {
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
        if (!data || !data.products || !data.categories) {
          this.error = data.message || "Response tidak valid.";
          return;
        }
        this.products = Array.isArray(data.products) ? data.products : [];
        this.categories = Array.isArray(data.categories) ? data.categories : [];
        this.locations = Array.isArray(data.locations) ? data.locations : [];
        this.calculateProductCount();
        this.activeCategoryFilter = "all";
      } catch (e) {
        console.error(e);
        this.error = "Gagal memuat data.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.isLoading = false;
      }
    },

    calculateProductCount() {
      this.categories.forEach((c) => {
        c.product_count = this.products.filter(
          (p) => String(p.category_id) === String(c.id)
        ).length;
      });
    },

    setCategoryFilter(id) {
      this.activeCategoryFilter = id === "all" ? "all" : id;
      this.dataProductPage = 1;
    },

    openAddProduct() {
      this.clearSelectedProduct();
      this.openAddProductModal = true;
    },
    openEditProduct(p) {
      this.clearSelectedProduct();
      this.selectedProduct = { ...p };
      this.openEditProductModal = true;
    },
    openDeleteProduct(p) {
      this.selectedProduct = p;
      this.openDeleteProductModal = true;
    },
    openAddCategory() {
      this.selectedCategory = { id: null, category_name: "" };
      this.validationErrors = {};
      this.openAddCategoryModal = true;
    },
    openEditCategory(c) {
      this.selectedCategory = { ...c };
      this.validationErrors = {};
      this.openEditCategoryModal = true;
    },
    openDeleteCategory(c) {
      this.selectedCategory = c;
      this.openDeleteCategoryModal = true;
    },

    async openViewProduct(p) {
      this.clearSelectedProduct();
      this.selectedProduct = { ...p, batches: [] };
      this.barcodeImageUrl = null;
      this.openViewProductModal = true;

      try {
        const res = await fetch(
          `/admin/products/barcode/save/${encodeURIComponent(
            this.selectedProduct.barcode
          )}`
        );
        if (res.ok) {
          const data = await res.json();
          if (data && data.url) this.barcodeImageUrl = data.url;
        }
      } catch (e) {
        console.error("Gagal generate barcode:", e);
      }

      try {
        const rb = await fetch(`/admin/products/batches/${p.id}`, {
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const bj = await rb.json();
        if (bj && Array.isArray(bj.batches)) {
          this.selectedProduct.batches = bj.batches;
        }
      } catch (e) {
        console.error(e);
      }
    },
    get visibleBatches() {
      const list =
        this.selectedProduct && Array.isArray(this.selectedProduct.batches)
          ? this.selectedProduct.batches
          : [];
      const now = new Date();
      return list.filter((b) => {
        if (!b) return false;
        if (!b.expired_date) return Number(b.current_stock || 0) > 0;
        const d = new Date(String(b.expired_date));
        const diff = (d - now) / (1000 * 60 * 60 * 24);
        return d > now && diff > 7 && Number(b.current_stock || 0) > 0;
      });
    },

    async addProduct() {
      try {
        if (!this.selectedProduct.category_id) {
          this.error = "Silakan pilih kategori.";
          setTimeout(() => (this.error = ""), 3000);
          return;
        }
        this.isSavingProduct = true;
        const fd = new FormData();
        Object.entries(this.selectedProduct).forEach(([k, v]) => {
          if (k === "photo" && v instanceof File) fd.append("photo", v);
          else if (k !== "photo") fd.append(k, v || "");
        });
        const res = await fetch(`/admin/products/add`, {
          method: "POST",
          headers: { "X-Requested-With": "XMLHttpRequest" },
          body: fd,
        });
        const data = await res.json();

        this.isSavingProduct = false;

        if (data.status || res.ok) {
          this.validationErrors = {};
          this.openAddProductModal = false;
          this.clearSelectedProduct();
          this.message = data.message || "Produk berhasil ditambahkan";
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else if (data.validation) {
          this.validationErrors = data.validation;
        } else {
          this.error = data.message || "Gagal menambahkan produk.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.isSavingProduct = false;
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async editProduct() {
      try {
        if (!this.selectedProduct.id) {
          this.error = "Data produk tidak valid";
          setTimeout(() => (this.error = ""), 3000);
          return;
        }
        this.isSavingProduct = true;
        const fd = new FormData();
        Object.entries(this.selectedProduct).forEach(([k, v]) => {
          if (k === "photo" && v instanceof File) fd.append("photo", v);
          else if (k !== "photo") fd.append(k, v || "");
        });
        const res = await fetch(
          `/admin/products/edit/${this.selectedProduct.id}`,
          {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: fd,
          }
        );
        const data = await res.json();

        this.isSavingProduct = false;

        if (data.status || res.ok) {
          this.validationErrors = {};
          this.openEditProductModal = false;
          this.clearSelectedProduct();
          this.message = data.message || "Produk berhasil diperbarui";
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else if (data.validation) {
          this.validationErrors = data.validation;
        } else {
          this.error = data.message || "Gagal memperbarui produk.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.isSavingProduct = false;
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async deleteProduct(id) {
      try {
        this.isDeletingProduct = true;
        const res = await fetch(`/admin/products/delete/${id}`, {
          method: "DELETE",
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();

        this.isDeletingProduct = false;

        if (data.status || res.ok) {
          this.openDeleteProductModal = false;
          this.message = data.message || "Produk berhasil dihapus";
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal menghapus produk.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.isDeletingProduct = false;
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async addCategory() {
      try {
        this.isSavingCategory = true;
        const fd = new FormData();
        fd.append("category_name", this.selectedCategory.category_name || "");
        const res = await fetch(`/admin/products/addCategory`, {
          method: "POST",
          headers: { "X-Requested-With": "XMLHttpRequest" },
          body: fd,
        });
        const data = await res.json();

        this.isSavingCategory = false;

        if (data.status || res.ok) {
          this.validationErrors = {};
          this.openAddCategoryModal = false;
          this.message = data.message || "Kategori berhasil ditambahkan";
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else if (data.validation) {
          this.validationErrors = data.validation;
        } else {
          this.error = data.message || "Gagal menambahkan kategori.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.isSavingCategory = false;
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async editCategory() {
      try {
        this.isSavingCategory = true;
        const fd = new FormData();
        fd.append("category_name", this.selectedCategory.category_name || "");
        const res = await fetch(
          `/admin/products/editCategory/${this.selectedCategory.id}`,
          {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: fd,
          }
        );
        const data = await res.json();

        this.isSavingCategory = false;

        if (data.status || res.ok) {
          this.validationErrors = {};
          this.openEditCategoryModal = false;
          this.message = data.message || "Kategori berhasil diperbarui";
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else if (data.validation) {
          this.validationErrors = data.validation;
        } else {
          this.error = data.message || "Gagal memperbarui kategori.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.isSavingCategory = false;
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async deleteCategory(id) {
      try {
        this.isDeletingCategory = true;
        const res = await fetch(`/admin/products/deleteCategory/${id}`, {
          method: "DELETE",
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();

        this.isDeletingCategory = false;

        if (data.status || res.ok) {
          this.openDeleteCategoryModal = false;
          this.message = data.message || "Kategori berhasil dihapus";
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal menghapus kategori.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.isDeletingCategory = false;
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    openAddLocation() {
      this.selectedLocation = {
        id: null,
        rack: "",
        row: "",
        description: "",
        status: "active",
      };
      this.openAddLocationModal = true;
    },
    openEditLocation(l) {
      this.selectedLocation = { ...l };
      this.openEditLocationModal = true;
    },
    openDeleteLocation(l) {
      this.selectedLocation = l;
      this.openDeleteLocationModal = true;
    },

    async addLocation() {
      try {
        this.isSavingLocation = true;
        const fd = new FormData();
        Object.entries(this.selectedLocation).forEach(([k, v]) =>
          fd.append(k, v || "")
        );
        const res = await fetch(`/admin/products/addLocation`, {
          method: "POST",
          headers: { "X-Requested-With": "XMLHttpRequest" },
          body: fd,
        });
        const data = await res.json();

        this.isSavingLocation = false;

        if (res.ok) {
          this.openAddLocationModal = false;
          this.message = data.message || "Lokasi berhasil ditambahkan";
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal menambah lokasi";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.isSavingLocation = false;
        console.error(e);
        this.error = "Terjadi kesalahan";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async editLocation() {
      try {
        this.isSavingLocation = true;
        const fd = new FormData();
        Object.entries(this.selectedLocation).forEach(([k, v]) =>
          fd.append(k, v || "")
        );
        const res = await fetch(
          `/admin/products/editLocation/${this.selectedLocation.id}`,
          {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: fd,
          }
        );
        const data = await res.json();

        this.isSavingLocation = false;

        if (res.ok) {
          this.openEditLocationModal = false;
          this.message = data.message || "Lokasi berhasil diperbarui";
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal memperbarui lokasi";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.isSavingLocation = false;
        console.error(e);
        this.error = "Terjadi kesalahan";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async deleteLocation(id) {
      try {
        this.isDeletingLocation = true;
        const res = await fetch(`/admin/products/deleteLocation/${id}`, {
          method: "DELETE",
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();

        this.isDeletingLocation = false;

        if (res.ok) {
          this.openDeleteLocationModal = false;
          this.message = data.message || "Lokasi berhasil dihapus";
          await this.fetchData();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal menghapus lokasi";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        this.isDeletingLocation = false;
        console.error(e);
        this.error = "Terjadi kesalahan";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    handlePhotoUpload(e) {
      const f = e.target.files?.[0] || null;
      if (!f) return;
      this.revokePreviewIfNeeded(this.selectedProduct.photo);
      this.selectedProduct.photo = f;
      this.selectedProduct = { ...this.selectedProduct };
    },

    downloadBarcode() {
      const url =
        this.barcodeImageUrl ||
        `/admin/products/barcode/image/${encodeURIComponent(
          this.selectedProduct.barcode || ""
        )}`;

      let ext = ".png";
      if (url.endsWith(".svg")) {
        ext = ".svg";
      }

      const a = document.createElement("a");
      a.href = url;
      a.download = (this.selectedProduct.barcode || "barcode") + ext;
      document.body.appendChild(a);
      a.click();
      a.remove();
    },

    getPhotoPreview(p) {
      if (!p) return null;
      if (p instanceof File) {
        const url = URL.createObjectURL(p);
        this._previewUrls.add(url);
        return url;
      }
      return p;
    },

    revokePreviewIfNeeded(p) {
      if (!p) return;
      if (typeof p === "string" && p.startsWith("blob:")) {
        try {
          URL.revokeObjectURL(p);
        } catch { }
      } else if (p instanceof File) {
        for (const url of this._previewUrls) {
          try {
            URL.revokeObjectURL(url);
          } catch { }
        }
        this._previewUrls.clear();
      }
    },

    clearSelectedProduct() {
      this.revokePreviewIfNeeded(this.selectedProduct.photo);
      this.selectedProduct = {
        id: null,
        product_name: "",
        price: "",
        category_id: "",
        photo: null,
      };
      this.validationErrors = {};
    },

    async fetchRestocks() {
      try {
        const res = await fetch(`/admin/restocks/data`, {
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
        if (data && Array.isArray(data.restocks)) this.restocks = data.restocks;
        else this.restocks = [];
      } catch (e) {
        console.error(e);
      }
    },
    get pendingRestockCount() {
      return (this.restocks || []).filter(r => (r.status || '').toLowerCase() === 'pending').length;
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
    async approveRestock(id) {
      this.openApproveRestock({ id });
    },
    async rejectRestock(id) {
      try {
        if (this.approvingRestockId === id || this.rejectingRestockId === id)
          return;
        this.rejectingRestockId = id;
        const res = await fetch(`/admin/restocks/reject/${id}`, {
          method: "POST",
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
        if (res.ok) {
          this.message = data?.message || "Permintaan ditolak";
          await this.fetchRestocks();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data?.message || "Gagal menolak";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        console.error(e);
        this.error = "Terjadi kesalahan";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        if (this.rejectingRestockId === id) this.rejectingRestockId = null;
      }
    },
  };
}
