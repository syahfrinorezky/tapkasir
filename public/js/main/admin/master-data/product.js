function productManagement() {
  return {
    products: [],
    categories: [],
    isLoading: false,
    isSavingProduct: false,
    isDeletingProduct: false,
    isSavingCategory: false,
    isDeletingCategory: false,
    approvingRestockId: null,
    rejectingRestockId: null,
    selectedProduct: {
      id: null,
      product_name: "",
      price: "",
      category_id: "",
      stock: "",
      barcode: "",
      photo: null,
    },
    selectedCategory: {
      id: null,
      category_name: "",
    },
    message: "",
    error: "",
    searchQuery: "",
    activeCategoryFilter: "all",
    validationErrors: {
      product_name: "",
      price: "",
      category_id: "",
      stock: "",
      barcode: "",
      category_name: "",
    },
    openAddProductModal: false,
    openEditProductModal: false,
    openDeleteProductModal: false,
    openViewProductModal: false,
    openAddCategoryModal: false,
    openEditCategoryModal: false,
    openDeleteCategoryModal: false,
    barcodeImageUrl: null,
    dataProductPage: 1,
    dataProductPageSize: 10,
    dataCategoriesPage: 1,
    dataCategoriesPageSize: 5,
    restocks: [],
    restocksPage: 1,
    restocksPageSize: 5,
    _previewUrls: new Set(),

    init() {
      // initial load only (no auto-refresh)
      this.fetchData();
      this.fetchRestocks();
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

    changeDataProductPage(page) {
      if (page >= 1 && page <= this.totalProductPages)
        this.dataProductPage = page;
    },
    changeDataCategoriesPage(page) {
      if (page >= 1 && page <= this.totalCategoriesPages)
        this.dataCategoriesPage = page;
    },
    getDataProductsNumber() {
      return Array.from({ length: this.totalProductPages }, (_, i) => i + 1);
    },
    getDataCategoriesNumber() {
      return Array.from({ length: this.totalCategoriesPages }, (_, i) => i + 1);
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
      this.selectedProduct = { ...p };
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
        if (data.status || res.ok) {
          this.validationErrors = {};
          this.message = data.message || "Produk berhasil ditambahkan";
          await this.fetchData();
          this.openAddProductModal = false;
          this.clearSelectedProduct();
          setTimeout(() => (this.message = ""), 3000);
        } else if (data.validation) {
          this.validationErrors = data.validation;
        } else {
          this.error = data.message || "Gagal menambahkan produk.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.isSavingProduct = false;
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
        if (data.status || res.ok) {
          this.validationErrors = {};
          this.message = data.message || "Produk berhasil diperbarui";
          await this.fetchData();
          this.openEditProductModal = false;
          this.clearSelectedProduct();
          setTimeout(() => (this.message = ""), 3000);
        } else if (data.validation) {
          this.validationErrors = data.validation;
        } else {
          this.error = data.message || "Gagal memperbarui produk.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.isSavingProduct = false;
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
        if (data.status || res.ok) {
          this.message = data.message || "Produk berhasil dihapus";
          await this.fetchData();
          this.openDeleteProductModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal menghapus produk.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.isDeletingProduct = false;
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
        if (data.status || res.ok) {
          this.validationErrors = {};
          this.message = data.message || "Kategori berhasil ditambahkan";
          await this.fetchData();
          this.openAddCategoryModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else if (data.validation) {
          this.validationErrors = data.validation;
        } else {
          this.error = data.message || "Gagal menambahkan kategori.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.isSavingCategory = false;
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
        if (data.status || res.ok) {
          this.validationErrors = {};
          this.message = data.message || "Kategori berhasil diperbarui";
          await this.fetchData();
          this.openEditCategoryModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else if (data.validation) {
          this.validationErrors = data.validation;
        } else {
          this.error = data.message || "Gagal memperbarui kategori.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.isSavingCategory = false;
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
        if (data.status || res.ok) {
          this.message = data.message || "Kategori berhasil dihapus";
          await this.fetchData();
          this.openDeleteCategoryModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal menghapus kategori.";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        console.error(e);
        this.error = "Terjadi kesalahan.";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        this.isDeletingCategory = false;
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
      const a = document.createElement("a");
      a.href = url;
      a.download = (this.selectedProduct.barcode || "barcode") + ".png";
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
        } catch {}
      } else if (p instanceof File) {
        for (const url of this._previewUrls) {
          try {
            URL.revokeObjectURL(url);
          } catch {}
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
        stock: "",
        barcode: "",
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
      try {
        if (this.approvingRestockId === id || this.rejectingRestockId === id)
          return;
        this.approvingRestockId = id;
        const res = await fetch(`/admin/restocks/approve/${id}`, {
          method: "POST",
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
        if (res.ok) {
          this.message = data?.message || "Permintaan disetujui";
          await this.fetchRestocks();
          await this.fetchData(); // refresh products stock
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data?.message || "Gagal menyetujui";
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (e) {
        console.error(e);
        this.error = "Terjadi kesalahan";
        setTimeout(() => (this.error = ""), 3000);
      } finally {
        if (this.approvingRestockId === id) this.approvingRestockId = null;
      }
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
