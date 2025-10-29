function productManagement() {
  return {
    products: [],
    categories: [],

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

    openAddProductModal: false,
    openEditProductModal: false,
    openDeleteProductModal: false,

    openAddCategoryModal: false,
    openEditCategoryModal: false,
    openDeleteCategoryModal: false,

    dataProductPage: 1,
    dataProductPageSize: 10,

    dataCategoriesPage: 1,
    dataCategoriesPageSize: 5,

    _previewUrls: new Set(),

    get paginatedProducts() {
      const start = (this.dataProductPage - 1) * this.dataProductPageSize;
      const end = start + this.dataProductPageSize;
      return this.products.slice(start, end);
    },

    get totalProductPages() {
      return Math.ceil(this.products.length / this.dataProductPageSize) || 1;
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
      if (page >= 1 && page <= this.totalProductPages) {
        this.dataProductPage = page;
      }
    },

    changeDataCategoriesPage(page) {
      if (page >= 1 && page <= this.totalCategoriesPages) {
        this.dataCategoriesPage = page;
      }
    },

    getDataProductsNumber() {
      const pages = [];
      for (let i = 1; i <= this.totalProductPages; i++) pages.push(i);
      return pages;
    },

    getDataCategoriesNumber() {
      const pages = [];
      for (let i = 1; i <= this.totalCategoriesPages; i++) pages.push(i);
      return pages;
    },

    getProductRowNumber(index) {
      return (this.dataProductPage - 1) * this.dataProductPageSize + index + 1;
    },

    getCategoryRowNumber(index) {
      return (
        (this.dataCategoriesPage - 1) * this.dataCategoriesPageSize + index + 1
      );
    },

    formatCurrency(amount) {
      return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
      }).format(amount);
    },

    async fetchData() {
      try {
        const res = await fetch(`/admin/products/data`, {
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();

        this.products = Array.isArray(data.products) ? data.products : [];
        this.categories = Array.isArray(data.categories) ? data.categories : [];
        this.calculateProductCount();
      } catch (e) {
        console.error(e);
        this.error = "Gagal memuat data produk & kategori.";
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    calculateProductCount() {
      this.categories.forEach((category) => {
        category.product_count = this.products.filter(
          (product) => String(product.category_id) === String(category.id)
        ).length;
      });
    },

    openAddProduct() {
      this.clearSelectedProduct();
      this.openAddProductModal = true;
    },

    openEditProduct(product) {
      this.clearSelectedProduct();
      this.selectedProduct = {
        id: product.id,
        product_name: product.product_name || "",
        price: product.price || "",
        category_id: product.category_id || "",
        stock: product.stock || "",
        barcode: product.barcode || "",
        photo: product.photo || null,
      };
      this.openEditProductModal = true;
    },

    openDeleteProduct(product) {
      this.selectedProduct = product;
      this.openDeleteProductModal = true;
    },

    openAddCategory() {
      this.selectedCategory = { id: null, category_name: "" };
      this.openAddCategoryModal = true;
    },

    openEditCategory(category) {
      this.selectedCategory = {
        id: category.id,
        category_name: category.category_name || "",
      };
      this.openEditCategoryModal = true;
    },

    openDeleteCategory(category) {
      this.selectedCategory = category;
      this.openDeleteCategoryModal = true;
    },

    async addProduct() {
      try {
        if (!this.selectedProduct || !this.selectedProduct.category_id) {
          this.error = "Silakan pilih kategori untuk produk.";
          setTimeout(() => (this.error = ""), 3000);
          return;
        }

        const formData = new FormData();
        formData.append(
          "product_name",
          this.selectedProduct.product_name || ""
        );
        formData.append("price", this.selectedProduct.price || "");
        formData.append("category_id", this.selectedProduct.category_id || "");
        formData.append("stock", this.selectedProduct.stock || "");
        formData.append("barcode", this.selectedProduct.barcode || "");
        if (this.selectedProduct.photo instanceof File) {
          formData.append("photo", this.selectedProduct.photo);
        }

        const res = await fetch(`/admin/products/add`, {
          method: "POST",
          headers: { "X-Requested-With": "XMLHttpRequest" },
          body: formData,
        });

        const data = await res.json();
<<<<<<< HEAD
        if (res.ok) {
=======
        if (data.status === "success") {
>>>>>>> 8015ab5f701c9ea2ed57bd32028f94fcff137c3a
          this.message = data.message || "Produk berhasil ditambahkan";
          await this.fetchData();
          this.openAddProductModal = false;
          this.clearSelectedProduct();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal menambahkan produk.";
          this.openAddProductModal = false;
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error(error);
        this.error = "Terjadi kesalahan saat menambahkan produk.";
        this.openAddProductModal = false;
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async editProduct() {
      try {
        if (!this.selectedProduct || !this.selectedProduct.id) {
          this.error = "Data produk tidak valid";
          setTimeout(() => (this.error = ""), 3000);
          return;
        }

        const formData = new FormData();
        formData.append(
          "product_name",
          this.selectedProduct.product_name || ""
        );
        formData.append("price", this.selectedProduct.price || "");
        formData.append("category_id", this.selectedProduct.category_id || "");
        formData.append("stock", this.selectedProduct.stock || "");
        formData.append("barcode", this.selectedProduct.barcode || "");
        if (this.selectedProduct.photo instanceof File) {
          formData.append("photo", this.selectedProduct.photo);
        }

        const res = await fetch(
          `/admin/products/edit/${this.selectedProduct.id}`,
          {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: formData,
          }
        );

        const data = await res.json();
<<<<<<< HEAD
        if (res.ok) {
=======
        if (data.status === "success") {
>>>>>>> 8015ab5f701c9ea2ed57bd32028f94fcff137c3a
          this.message = data.message || "Produk berhasil diperbarui";
          await this.fetchData();
          this.openEditProductModal = false;
          this.clearSelectedProduct();
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal memperbarui produk.";
          this.openEditProductModal = false;
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error(error);
        this.error = "Terjadi kesalahan saat memperbarui produk.";
        this.openEditProductModal = false;
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async deleteProduct(id) {
      try {
        const res = await fetch(`/admin/products/delete/${id}`, {
          method: "DELETE",
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
<<<<<<< HEAD
        if (res.ok) {
=======
        if (data.status === "success") {
>>>>>>> 8015ab5f701c9ea2ed57bd32028f94fcff137c3a
          this.message = data.message || "Produk berhasil dihapus";
          await this.fetchData();
          this.openDeleteProductModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal menghapus produk.";
          this.openDeleteProductModal = false;
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error(error);
        this.error = "Terjadi kesalahan saat menghapus produk.";
        this.openDeleteProductModal = false;
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async addCategory() {
      try {
        const formData = new FormData();
        formData.append(
          "category_name",
          this.selectedCategory.category_name || ""
        );

        const res = await fetch(`/admin/products/addCategory`, {
          method: "POST",
          headers: { "X-Requested-With": "XMLHttpRequest" },
          body: formData,
        });

        const data = await res.json();
<<<<<<< HEAD
        if (res.ok) {
=======
        if (data.status === "success") {
>>>>>>> 8015ab5f701c9ea2ed57bd32028f94fcff137c3a
          this.message = data.message || "Kategori berhasil ditambahkan";
          await this.fetchData();
          this.openAddCategoryModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal menambahkan kategori.";
          this.openAddCategoryModal = false;
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error(error);
        this.error = "Terjadi kesalahan saat menambahkan kategori.";
        this.openAddCategoryModal = false;
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async editCategory() {
      try {
        const formData = new FormData();
        formData.append(
          "category_name",
          this.selectedCategory.category_name || ""
        );

        const res = await fetch(
          `/admin/products/editCategory/${this.selectedCategory.id}`,
          {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: formData,
          }
        );

        const data = await res.json();
<<<<<<< HEAD
        if (res.ok) {
=======
        if (data.status === "success") {
>>>>>>> 8015ab5f701c9ea2ed57bd32028f94fcff137c3a
          this.message = data.message || "Kategori berhasil diperbarui";
          await this.fetchData();
          this.openEditCategoryModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal memperbarui kategori.";
          this.openEditCategoryModal = false;
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error(error);
        this.error = "Terjadi kesalahan saat memperbarui kategori.";
        this.openEditCategoryModal = false;
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    async deleteCategory(id) {
      try {
        const res = await fetch(`/admin/products/deleteCategory/${id}`, {
          method: "DELETE",
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
<<<<<<< HEAD
        if (res.ok) {
=======
        if (data.status === "success") {
>>>>>>> 8015ab5f701c9ea2ed57bd32028f94fcff137c3a
          this.message = data.message || "Kategori berhasil dihapus";
          await this.fetchData();
          this.openDeleteCategoryModal = false;
          setTimeout(() => (this.message = ""), 3000);
        } else {
          this.error = data.message || "Gagal menghapus kategori.";
          this.openDeleteCategoryModal = false;
          setTimeout(() => (this.error = ""), 3000);
        }
      } catch (error) {
        console.error(error);
        this.error = "Terjadi kesalahan saat menghapus kategori.";
        this.openDeleteCategoryModal = false;
        setTimeout(() => (this.error = ""), 3000);
      }
    },

    handlePhotoUpload(event, isNewProduct = true) {
      const file =
        event.target.files && event.target.files[0]
          ? event.target.files[0]
          : null;
      if (!file) return;
      this.revokePreviewIfNeeded(this.selectedProduct.photo);
      this.selectedProduct.photo = file;
    },

    getPhotoPreview(photo) {
      if (!photo) return null;
      if (photo instanceof File) {
        const url = URL.createObjectURL(photo);
        this._previewUrls.add(url);
        return url;
      }
      return photo;
    },

    revokePreviewIfNeeded(photo) {
      if (!photo) return;
      if (photo instanceof File) {
        for (const url of this._previewUrls) {
          try {
            URL.revokeObjectURL(url);
          } catch (e) {}
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
    },
  };
}
