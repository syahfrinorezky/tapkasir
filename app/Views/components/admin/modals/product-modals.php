<!-- Delete Product Modal -->
<div x-cloak x-show="openDeleteProductModal" @click.self="openDeleteProductModal = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center bg-black/40 bg-opacity-50 backdrop-blur-xs z-50"></div>

<div
    x-cloak
    x-show="openDeleteProductModal"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true">
    <div class="w-full max-w-md bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Hapus Produk</h3>
            <button @click="openDeleteProductModal = false" class="p-2 rounded hover:bg-gray-100">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>
        <div class="px-5 py-4 flex flex-col items-center justify-center space-y-3">
            <img src="<?= base_url('images/illustration/deletemodal-illustration.png') ?>" alt="Delete Illustration" class="w-1/2 mb-2">
            <p class="text-gray-700 text-center">
                Apakah Anda yakin ingin menghapus produk
                <span class="font-semibold" x-text="selectedProduct?.product_name || 'ini'"></span>?
            </p>
            <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="px-5 py-4 bg-gray-50 flex items-center justify-center gap-4">
            <button @click="openDeleteProductModal = false" class="px-4 py-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-100">
                Batal
            </button>
            <button @click="deleteProduct(selectedProduct.id)" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">
                Hapus
            </button>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div x-cloak x-show="openDeleteCategoryModal" @click.self="openDeleteCategoryModal = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center bg-black/40 bg-opacity-50 backdrop-blur-xs z-50"></div>

<div
    x-cloak
    x-show="openDeleteCategoryModal"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true">
    <div class="w-full max-w-md bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Hapus Kategori</h3>
            <button @click="openDeleteCategoryModal = false" class="p-2 rounded hover:bg-gray-100">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>
        <div class="px-5 py-4 flex flex-col items-center justify-center space-y-3">
            <img src="<?= base_url('images/illustration/deletemodal-illustration.png') ?>" alt="Delete Illustration" class="w-1/2 mb-2">
            <p class="text-gray-700 text-center">
                Apakah Anda yakin ingin menghapus kategori
                <span class="font-semibold" x-text="selectedCategory?.category_name || 'ini'"></span>?
            </p>
            <p class="text-sm text-gray-500" x-show="selectedCategory?.product_count > 0">
                Terdapat <span x-text="selectedCategory?.product_count"></span> produk dalam kategori ini.
            </p>
            <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="px-5 py-4 bg-gray-50 flex items-center justify-center gap-4">
            <button @click="openDeleteCategoryModal = false" class="px-4 py-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-100">
                Batal
            </button>
            <button @click="deleteCategory(selectedCategory.id)" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">
                Hapus
            </button>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div
    x-cloak
    x-show="openAddProductModal"
    @click.self="openAddProductModal = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50"
    aria-hidden="true"></div>
<div
    x-cloak
    x-show="openAddProductModal"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true">
    <form @submit.prevent="addProduct" class="w-full max-w-xl bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Tambah Produk</h3>
            <button type="button" @click="openAddProductModal = false" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>

        <div class="px-5 py-5 grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                <div class="relative">
                    <i class="fas fa-box absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        placeholder="Masukkan nama produk"
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedProduct.product_name"
                        required>
                    <p class="text-red-500 text-xs italic mt-1" x-show="validationErrors.product_name" x-text="validationErrors.product_name"></p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                <div class="relative">
                    <i class="fas fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="number"
                        placeholder="Masukkan harga"
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedProduct.price"
                        min="0"
                        required>
                    <p class="text-red-500 text-xs italic mt-1" x-show="validationErrors.price" x-text="validationErrors.price"></p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <div class="relative">
                    <i class="fas fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <select
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedProduct.category_id"
                        required>
                        <option value="" disabled>Pilih kategori</option>
                        <template x-for="category in categories" :key="category.id">
                            <option :value="category.id" x-text="category.category_name"></option>
                        </template>
                    </select>
                    <p class="text-red-500 text-xs italic mt-1" x-show="validationErrors.category_id" x-text="validationErrors.category_id"></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                    <div class="relative">
                        <i class="fas fa-boxes-stacked absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input
                            type="number"
                            placeholder="Jumlah stok"
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                            x-model="selectedProduct.stock"
                            min="0"
                            required>
                    </div>
                    <p class="text-red-500 text-xs italic mt-1" x-show="validationErrors.stock" x-text="validationErrors.stock"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                    <div class="relative">
                        <i class="fas fa-barcode absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input
                            type="text"
                            placeholder="Kode barcode"
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                            x-model="selectedProduct.barcode">
                    </div>
                    <p class="text-red-500 text-xs italic mt-1" x-show="validationErrors.barcode" x-text="validationErrors.barcode"></p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Produk</label>
                <div class="relative">
                    <input
                        type="file"
                        accept="image/*"
                        @change="handlePhotoUpload($event, true)"
                        class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
                </div>
                <template x-if="selectedProduct.photo">
                    <div class="mt-2">
                        <img :src="getPhotoPreview(selectedProduct.photo)" alt="Preview" @error="(e) => e.target.src = '<?= base_url('images/illustration/deletemodal-illustration.png') ?>'" class="w-20 h-20 object-cover rounded">
                    </div>
                </template>
            </div>
        </div>

        <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
            <button
                type="button"
                @click="openAddProductModal = false"
                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                Batal
            </button>
            <button
                type="submit"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition">
                Simpan
            </button>
        </div>
    </form>
</div>

<!-- Edit Product Modal -->
<div
    x-cloak
    x-show="openEditProductModal"
    @click.self="openEditProductModal = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50"
    aria-hidden="true"></div>
<div
    x-cloak
    x-show="openEditProductModal"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true">
    <form @submit.prevent="editProduct" class="w-full max-w-xl bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Edit Produk</h3>
            <button type="button" @click="openEditProductModal = false" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>

        <div class="px-5 py-5 grid grid-cols-1 gap-4">
            <!-- Form fields sama seperti Add Product Modal -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                <div class="relative">
                    <i class="fas fa-box absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        placeholder="Masukkan nama produk"
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedProduct.product_name"
                        required>
                    <p class="text-red-500 text-xs italic mt-1" x-show="validationErrors.product_name" x-text="validationErrors.product_name"></p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                <div class="relative">
                    <i class="fas fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="number"
                        placeholder="Masukkan harga"
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedProduct.price"
                        min="0"
                        required>
                    <p class="text-red-500 text-xs italic mt-1" x-show="validationErrors.price" x-text="validationErrors.price"></p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <div class="relative">
                    <i class="fas fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <select
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedProduct.category_id"
                        required>
                        <option value="" disabled>Pilih kategori</option>
                        <template x-for="category in categories" :key="category.id">
                            <option :value="category.id" x-text="category.category_name"></option>
                        </template>
                    </select>
                    <p class="text-red-500 text-xs italic mt-1" x-show="validationErrors.category_id" x-text="validationErrors.category_id"></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                    <div class="relative">
                        <i class="fas fa-boxes-stacked absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input
                            type="number"
                            placeholder="Jumlah stok"
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                            x-model="selectedProduct.stock"
                            min="0"
                            required>
                    </div>
                    <p class="text-red-500 text-xs italic mt-1" x-show="validationErrors.stock" x-text="validationErrors.stock"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                    <div class="relative">
                        <i class="fas fa-barcode absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input
                            type="text"
                            placeholder="Kode barcode"
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                            x-model="selectedProduct.barcode">
                    </div>
                    <p class="text-red-500 text-xs italic mt-1" x-show="validationErrors.barcode" x-text="validationErrors.barcode"></p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Produk</label>
                <div class="flex items-center gap-4">
                    <template x-if="selectedProduct?.photo">
                        <div>
                            <img :src="selectedProduct.photo" alt="Current Photo" @error="(e) => e.target.src = '<?= base_url('images/illustration/deletemodal-illustration.png') ?>'" class="w-20 h-20 object-cover rounded">
                        </div>
                    </template>
                    <div class="flex-1">
                        <input
                            type="file"
                            accept="image/*"
                            @change="handlePhotoUpload($event, false)"
                            class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
                    </div>
                </div>
            </div>
        </div>

        <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
            <button
                type="button"
                @click="openEditProductModal = false"
                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                Batal
            </button>
            <button
                type="submit"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition">
                Simpan
            </button>
        </div>
    </form>
</div>

<!-- Add Category Modal -->
<div
    x-cloak
    x-show="openAddCategoryModal"
    @click.self="openAddCategoryModal = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50"
    aria-hidden="true"></div>
<div
    x-cloak
    x-show="openAddCategoryModal"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true">
    <form @submit.prevent="addCategory" class="w-full max-w-xl bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Tambah Kategori</h3>
            <button type="button" @click="openAddCategoryModal = false" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>

        <div class="px-5 py-5 grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                <div class="relative">
                    <i class="fas fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        placeholder="Masukkan nama kategori"
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedCategory.category_name"
                        required>
                    <p class="text-red-500 text-xs italic mt-1" x-show="validationErrors.category_name" x-text="validationErrors.category_name"></p>
                </div>
            </div>
        </div>

        <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
            <button
                type="button"
                @click="openAddCategoryModal = false"
                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                Batal
            </button>
            <button
                type="submit"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition">
                Simpan
            </button>
        </div>
    </form>
</div>

<!-- Edit Category Modal -->
<div
    x-cloak
    x-show="openEditCategoryModal"
    @click.self="openEditCategoryModal = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50"
    aria-hidden="true"></div>
<div
    x-cloak
    x-show="openEditCategoryModal"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true">
    <form @submit.prevent="editCategory" class="w-full max-w-xl bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Edit Kategori</h3>
            <button type="button" @click="openEditCategoryModal = false" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>

        <div class="px-5 py-5 grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                <div class="relative">
                    <i class="fas fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        placeholder="Masukkan nama kategori"
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedCategory.category_name"
                        required>
                    <p class="text-red-500 text-xs italic mt-1" x-show="validationErrors.category_name" x-text="validationErrors.category_name"></p>
                </div>
            </div>
        </div>

        <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
            <button
                type="button"
                @click="openEditCategoryModal = false"
                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                Batal
            </button>
            <button
                type="submit"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition">
                Simpan
            </button>
        </div>
    </form>
</div>