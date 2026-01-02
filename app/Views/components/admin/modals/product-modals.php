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
            <button @click="deleteProduct(selectedProduct.id)" :disabled="isDeletingProduct" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-spinner fa-spin" x-show="isDeletingProduct"></i>
                <span x-text="isDeletingProduct ? 'Menghapus…' : 'Hapus'"></span>
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
            <button @click="deleteCategory(selectedCategory.id)" :disabled="isDeletingCategory" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-spinner fa-spin" x-show="isDeletingCategory"></i>
                <span x-text="isDeletingCategory ? 'Menghapus…' : 'Hapus'"></span>
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
                :disabled="isSavingProduct || !(selectedProduct.product_name && selectedProduct.product_name.trim()) || selectedProduct.price === '' || selectedProduct.price === null || selectedProduct.category_id === '' || selectedProduct.category_id === null"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-spinner fa-spin" x-show="isSavingProduct"></i>
                <span x-text="isSavingProduct ? 'Menyimpan…' : 'Simpan'"></span>
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
                :disabled="isSavingProduct || !(selectedProduct.product_name && selectedProduct.product_name.trim()) || selectedProduct.price === '' || selectedProduct.price === null || selectedProduct.category_id === '' || selectedProduct.category_id === null"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-spinner fa-spin" x-show="isSavingProduct"></i>
                <span x-text="isSavingProduct ? 'Menyimpan…' : 'Simpan'"></span>
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
                :disabled="isSavingCategory || !(selectedCategory.category_name && selectedCategory.category_name.trim())"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-spinner fa-spin" x-show="isSavingCategory"></i>
                <span x-text="isSavingCategory ? 'Menyimpan…' : 'Simpan'"></span>
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
                :disabled="isSavingCategory || !(selectedCategory.category_name && selectedCategory.category_name.trim())"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-spinner fa-spin" x-show="isSavingCategory"></i>
                <span x-text="isSavingCategory ? 'Menyimpan…' : 'Simpan'"></span>
            </button>
        </div>
    </form>
</div>

<!-- View Product Detail Modal -->
<div x-cloak x-show="openViewProductModal" @click.self="openViewProductModal = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center bg-black/40 bg-opacity-50 backdrop-blur-xs z-50"></div>

<div
    x-cloak
    x-show="openViewProductModal"
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
            <h3 class="text-lg font-semibold text-white">Detail Produk</h3>
            <button @click="openViewProductModal = false" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>

        <div class="px-5 py-4">
            <div class="flex items-center gap-4">
                <div class="w-20 h-20 bg-gray-100 rounded flex items-center justify-center overflow-hidden">
                    <template x-if="selectedProduct?.photo">
                        <img :src="selectedProduct.photo" alt="Photo" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!selectedProduct?.photo">
                        <img src="<?= base_url('images/placeholder-product.svg') ?>" alt="No photo" class="w-full h-full object-cover">
                    </template>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800" x-text="selectedProduct?.product_name"></h4>
                    <p class="text-sm text-gray-600" x-text="formatCurrency(selectedProduct?.price || 0)"></p>
                    <p class="text-sm text-gray-500">Kategori: <span x-text="selectedProduct?.category_name || '-'"></span></p>
                    <p class="text-sm text-gray-500">Stok: <span x-text="selectedProduct?.stock || 0"></span></p>
                </div>
            </div>

            <div class="mt-4">
                <h4 class="font-semibold text-gray-800 mb-2">Daftar Batch Aktif</h4>
                <div class="bg-white rounded-lg border">
                    <div class="overflow-x-auto max-h-[40vh] overflow-y-auto">
                        <table class="w-full min-w-max text-sm">
                            <thead class="bg-gray-100 sticky top-0 z-10">
                                <tr>
                                    <th class="px-3 py-2 text-left">Expired</th>
                                    <th class="px-3 py-2 text-right">Stock</th>
                                    <th class="px-3 py-2 text-right">Harga Beli</th>
                                    <th class="px-3 py-2 text-left">Rak</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-if="!(selectedProduct && selectedProduct.batches && selectedProduct.batches.length) ||
                                                     (selectedProduct && selectedProduct.batches && selectedProduct.batches.filter(b => {
                                                        const exp = b.expired_date ? new Date(b.expired_date) : null;
                                                        const today = new Date();
                                                        const diffDays = exp ? Math.ceil((exp - today) / (1000*60*60*24)) : null;
                                                        return (!exp || diffDays > 7) && (b.current_stock ?? 0) >= 0;
                                                     }).length === 0)">
                                    <tr>
                                        <td colspan="4" class="px-3 py-4 text-center text-gray-500">Tidak ada batch aktif yang ditampilkan</td>
                                    </tr>
                                </template>

                                <template x-for="b in (selectedProduct?.batches || []).filter(b => {
                                        const exp = b.expired_date ? new Date(b.expired_date) : null;
                                        const today = new Date();
                                        const diffDays = exp ? Math.ceil((exp - today) / (1000*60*60*24)) : null;
                                        return (!exp || diffDays > 7) && (b.current_stock ?? 0) >= 0;
                                    })" :key="b.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2" x-text="b.expired_date || '-' "></td>
                                        <td class="px-3 py-2 text-right" x-text="b.current_stock"></td>
                                        <td class="px-3 py-2 text-right" x-text="formatCurrency(b.purchase_price || 0)"></td>
                                        <td class="px-3 py-2">
                                            <span x-text="[b.rack, b.row].filter(Boolean).join(' / ') || '-' "></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="text-xs text-gray-500 mt-1">Catatan: batch yang expired atau near-expired (≤ 7 hari) tidak ditampilkan.</div>
            </div>

            <div class="mt-4 flex flex-col items-center">
                <div class="bg-white p-4 rounded-md shadow-inner min-h-[100px] flex items-center justify-center">
                    <template x-if="selectedProduct?.barcode">
                        <img :src="barcodeImageUrl || '/admin/products/barcode/image/' + encodeURIComponent(selectedProduct.barcode)" alt="Barcode" class="w-56 h-auto">
                    </template>
                    <template x-if="!selectedProduct?.barcode">
                        <span class="text-gray-400 italic">Barcode tidak tersedia</span>
                    </template>
                </div>
                <p class="text-sm text-gray-600 mt-2">Kode: <span class="font-mono" x-text="selectedProduct?.barcode || '-'"></span></p>
            </div>
        </div>

        <div class="px-5 py-4 bg-gray-50 flex flex-col sm:flex-row items-center justify-center gap-3">
            <template x-if="selectedProduct?.barcode">
                <a :href="barcodeImageUrl || '/admin/products/barcode/image/' + encodeURIComponent(selectedProduct.barcode)" target="_blank" class="w-full sm:w-auto px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 text-center">Buka Gambar</a>
            </template>
            <button @click="downloadBarcode()" :disabled="!selectedProduct?.barcode" class="w-full sm:w-auto px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed">Unduh Barcode</button>
            <button @click="openViewProductModal = false" class="w-full sm:w-auto px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Persetujuan Restock -->
<div
    x-cloak
    x-show="approveModalOpen"
    @click.self="closeApproveRestock()"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
</div>

<div
    x-cloak
    x-show="approveModalOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true">

    <div class="w-full max-w-xl bg-white rounded-xl shadow-2xl overflow-hidden">

        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Konfirmasi Persetujuan Restock</h3>
            <button @click="closeApproveRestock()" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white text-lg"></i>
            </button>
        </div>

        <div class="px-5 py-5 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">

            <div class="space-y-1">
                <p class="text-xs text-gray-500">Produk</p>
                <p class="font-medium text-gray-800" x-text="selectedRestock?.product_name"></p>
            </div>

            <div class="space-y-1">
                <p class="text-xs text-gray-500">Qty</p>
                <p class="font-medium text-gray-800" x-text="selectedRestock?.quantity"></p>
            </div>

            <div class="space-y-1">
                <p class="text-xs text-gray-500">Expired Date</p>
                <p class="font-medium text-gray-800" x-text="restockDetails.expired_date || '-'"></p>
            </div>

            <div class="space-y-1">
                <p class="text-xs text-gray-500">Harga Pembelian</p>
                <p class="font-medium text-gray-800" x-text="formatCurrency(restockDetails.purchase_price || 0)"></p>
            </div>

            <div class="space-y-1 col-span-2">
                <p class="text-xs text-gray-500">Lokasi</p>
                <p class="font-medium text-gray-800 break-all"
                    x-text="[restockDetails.rack, restockDetails.row].filter(Boolean).join(' / ') || '-'">
                </p>
            </div>

            <div class="space-y-1 col-span-2">
                <p class="text-xs text-gray-500">Catatan</p>
                <p class="font-medium text-gray-800 break-words"
                    x-text="restockDetails.note || '-'"></p>
            </div>

            <div class="space-y-1 col-span-2">
                <p class="text-xs text-gray-500">Bukti</p>

                <template x-if="restockDetails.receipt_temp || restockDetails.receipt_image">
                    <a :href="previewUrl(restockDetails)" target="_blank"
                        class="text-primary underline hover:text-primary/80 text-sm">
                        Lihat bukti
                    </a>
                </template>

                <template x-if="!(restockDetails.receipt_temp || restockDetails.receipt_image)">
                    <div class="text-gray-400 text-sm">Tidak ada bukti.</div>
                </template>
            </div>

        </div>

        <div class="px-5 py-4 bg-gray-50 flex flex-col sm:flex-row items-center justify-end gap-2">
            <button
                @click="closeApproveRestock()"
                class="px-4 py-2 w-full sm:w-auto rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                Batal
            </button>

            <button
                @click="confirmApproveRestock()"
                :disabled="approving"
                :class="approving ? 'opacity-60 cursor-not-allowed' : ''"
                class="px-4 py-2 w-full sm:w-auto rounded-lg bg-green-600 text-white hover:bg-green-700 transition flex items-center justify-center gap-2">

                <i x-show="approving" class="fas fa-circle-notch fa-spin"></i>
                <span>Konfirmasi Setuju</span>
            </button>
        </div>

    </div>
</div>

<!-- Delete Location Modal -->
<div x-cloak x-show="openDeleteLocationModal" @click.self="openDeleteLocationModal = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center bg-black/40 bg-opacity-50 backdrop-blur-xs z-50"></div>

<div
    x-cloak
    x-show="openDeleteLocationModal"
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
            <h3 class="text-lg font-semibold text-white">Hapus Lokasi</h3>
            <button @click="openDeleteLocationModal = false" class="p-2 rounded hover:bg-gray-100">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>
        <div class="px-5 py-4 flex flex-col items-center justify-center space-y-3">
            <img src="<?= base_url('images/illustration/deletemodal-illustration.png') ?>" alt="Delete Illustration" class="w-1/2 mb-2">
            <p class="text-gray-700 text-center">
                Apakah Anda yakin ingin menghapus lokasi
                <span class="font-semibold" x-text="(selectedLocation.rack || '') + ' - ' + (selectedLocation.row || '')"></span>?
            </p>
            <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="px-5 py-4 bg-gray-50 flex items-center justify-center gap-4">
            <button @click="openDeleteLocationModal = false" class="px-4 py-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-100">
                Batal
            </button>
            <button @click="deleteLocation(selectedLocation.id)" :disabled="isDeletingLocation" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-spinner fa-spin" x-show="isDeletingLocation"></i>
                <span x-text="isDeletingLocation ? 'Menghapus…' : 'Hapus'"></span>
            </button>
        </div>
    </div>
</div>

<!-- Add Location Modal -->
<div
    x-cloak
    x-show="openAddLocationModal"
    @click.self="openAddLocationModal = false"
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
    x-show="openAddLocationModal"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true">
    <form @submit.prevent="addLocation" class="w-full max-w-xl bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Tambah Lokasi</h3>
            <button type="button" @click="openAddLocationModal = false" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>

        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rak</label>
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Contoh: A, B, R1"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedLocation.rack"
                        required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Baris/Level</label>
                <div class="relative">
                     <input
                        type="text"
                        placeholder="Contoh: 1, 2, L1"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedLocation.row"
                        required>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                <textarea
                    rows="2"
                    placeholder="Keterangan tambahan..."
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                    x-model="selectedLocation.description"></textarea>
            </div>
             <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                    x-model="selectedLocation.status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
             <button
                type="button"
                @click="openAddLocationModal = false"
                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                Batal
            </button>
            <button
                type="submit"
                :disabled="isSavingLocation || !selectedLocation.rack || !selectedLocation.row"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-spinner fa-spin" x-show="isSavingLocation"></i>
                <span x-text="isSavingLocation ? 'Menyimpan…' : 'Simpan'"></span>
            </button>
        </div>
    </form>
</div>

<!-- Edit Location Modal -->
<div
    x-cloak
    x-show="openEditLocationModal"
    @click.self="openEditLocationModal = false"
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
    x-show="openEditLocationModal"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true">
    <form @submit.prevent="editLocation" class="w-full max-w-xl bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Edit Lokasi</h3>
            <button type="button" @click="openEditLocationModal = false" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>

        <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rak</label>
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Contoh: A, B, R1"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedLocation.rack"
                        required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Baris/Level</label>
                <div class="relative">
                     <input
                        type="text"
                        placeholder="Contoh: 1, 2, L1"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedLocation.row"
                        required>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                <textarea
                    rows="2"
                    placeholder="Keterangan tambahan..."
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                    x-model="selectedLocation.description"></textarea>
            </div>
             <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                    x-model="selectedLocation.status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
             <button
                type="button"
                @click="openEditLocationModal = false"
                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                Batal
            </button>
            <button
                type="submit"
                :disabled="isSavingLocation || !selectedLocation.rack || !selectedLocation.row"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-spinner fa-spin" x-show="isSavingLocation"></i>
                <span x-text="isSavingLocation ? 'Menyimpan…' : 'Simpan'"></span>
            </button>
        </div>
    </form>
</div>