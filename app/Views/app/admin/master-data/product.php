<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Manajemen Produk
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main>
    <div class="fixed top-0 w-full z-40">
        <?= $this->include('components/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/sidebar'); ?>

        <div class="flex flex-col flex-1 font-secondary overflow-y-auto min-h-screen"
            x-data="productManagement()" x-init="fetchData()">
            <div class="flex justify-between items-center pt-22 px-4 pb-4 md:pt-24 md:px-6 md:pb-6 lg:p-8">
                <div>
                    <h1 class="text-xl text-primary font-primary font-bold">Manajemen Produk</h1>
                    <p class="text-gray-600">Kelola produk di sistem kasir.</p>
                </div>
                <div class="hidden md:block border border-gray-200 rounded-md px-3 py-2">
                    <?= date('l, d M Y'); ?>
                </div>
            </div>

            <hr class="border-gray-200 mx-4 md:mx-6 lg:mx-8">

            <div class="mt-4 md:mt-6 lg:mt-8 px-4 md:px-6 lg:px-8 lg:pb-8 flex flex-col 2xl:flex-row gap-5">
                <div class="flex flex-col space-y-2 w-full lg:flex-1">
                    <div class="flex justify-between items-center">
                        <h1 class="font-bold text-lg text-gray-700">
                            <i class="fas fa-box text-lg text-primary inline-flex mr-1"></i>
                            Daftar Produk
                        </h1>

                        <div class="flex items-center gap-2">
                            <button @click="openAddProduct()" type="button"
                                class="bg-white hover:bg-gray-200 transition-colors duration-300 ease-in-out p-2 rounded-md flex items-center justify-center cursor-pointer">
                                <i class="fas fa-plus text-primary"></i>
                            </button>
                        </div>
                    </div>

                    <div class="px-1">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div class="flex w-full md:w-2/3">
                                <input type="text" x-model.debounce.300ms="searchQuery" placeholder="Cari produk atau barcode..." class="flex-1 px-3 py-2 rounded-l-md border border-primary/30 focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                                <button @click="dataProductPage = 1" type="button" class="px-4 py-2 bg-primary text-white rounded-r-md text-sm border border-primary/30 border-l-0 hover:bg-primary/90">Search</button>
                            </div>

                            <div class="w-full md:w-1/3">
                                <select x-model="activeCategoryFilter" @change="setCategoryFilter($event.target.value)" class="w-full px-3 py-2 rounded-md border border-primary/30 focus:border-primary focus:ring-1 focus:ring-primary text-sm">
                                    <option value="all">All</option>
                                    <template x-for="c in categories" :key="c.id">
                                        <option :value="c.id" x-text="c.category_name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
                            <table class="w-full min-w-max">
                                <thead class="bg-primary text-white sticky top-0 z-10">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">No</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Foto</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Nama Produk</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Harga</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Kategori</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Stok</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Barcode</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-if="filteredProducts.length === 0">
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-gray-500">
                                                <div class="w-full flex flex-col items-center justify-center text-gray-500">
                                                    <video src="<?= base_url('videos/nodata.mp4') ?>" class="w-64 h-36 mb-2" autoplay muted loop></video>
                                                    <span class="text-center">Tidak ada produk</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    <template x-for="(product, index) in paginatedProducts" :key="product.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-center" x-text="getProductRowNumber(index)"></td>
                                            <td class="px-4 py-3 text-sm text-center">
                                                <template x-if="product.photo">
                                                    <img :src="product.photo" :alt="product.product_name" @error="(e) => e.target.src = '<?= base_url('images/illustration/deletemodal-illustration.png') ?>'" class="w-10 h-10 object-cover rounded mx-auto">
                                                </template>
                                                <template x-if="!product.photo">
                                                    <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center mx-auto">
                                                        <i class="fas fa-image text-gray-400"></i>
                                                    </div>
                                                </template>
                                            </td>
                                            <td class="px-4 py-3 text-sm" x-text="product.product_name"></td>
                                            <td class="px-4 py-3 text-sm text-center" x-text="formatCurrency(product.price)"></td>
                                            <td class="px-4 py-3 text-sm text-center" x-text="product.category_name"></td>
                                            <td class="px-4 py-3 text-sm text-center" x-text="product.stock"></td>
                                            <td class="px-4 py-3 text-sm text-center" x-text="product.barcode"></td>
                                            <td class="px-4 py-3 text-sm space-x-2 flex items-center justify-center">
                                                <button type="button" @click="openViewProduct(product)"
                                                    title="Detail"
                                                    class="flex items-center justify-center p-2 bg-primary hover:bg-primary/80 text-white rounded-md transition-colors duration-300 ease-in-out cursor-pointer">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" @click="openEditProduct(product)"
                                                    class="flex items-center justify-center p-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition-colors duration-300 ease-in-out cursor-pointer">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <button type="button" @click="openDeleteProduct(product)"
                                                    class="flex items-center justify-center p-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors duration-300 ease-in-out cursor-pointer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                Menampilkan
                                <span class="font-semibold" x-text="filteredProducts.length === 0 ? 0 : ((dataProductPage - 1) * dataProductPageSize) + 1"></span>
                                hingga
                                <span class="font-semibold" x-text="Math.min(dataProductPage * dataProductPageSize, filteredProducts.length)"></span>
                                dari
                                <span class="font-semibold" x-text="filteredProducts.length"></span>
                                produk
                            </div>

                            <div class="flex items-center gap-2" x-show="totalProductPages > 1">
                                <button
                                    @click="changeDataProductPage(dataProductPage - 1)"
                                    :disabled="dataProductPage === 1"
                                    :class="dataProductPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </button>

                                <template x-for="page in getDataProductsNumber()" :key="page">
                                    <button
                                        @click="changeDataProductPage(page)"
                                        :class="page === dataProductPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 text-sm font-medium transition"
                                        x-text="page">
                                    </button>
                                </template>

                                <button
                                    @click="changeDataProductPage(dataProductPage + 1)"
                                    :disabled="dataProductPage === totalProductPages"
                                    :class="dataProductPage === totalProductPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 w-full 2xl:w-1/3">
                    <div class="flex flex-col space-y-2">
                        <template x-if="message">
                            <div x-text="message" class="fixed top-10 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-md z-50"></div>
                        </template>

                        <template x-if="error">
                            <div x-text="error" class="fixed top-10 right-5 bg-red-500 text-white px-4 py-2 rounded shadow-md z-50"></div>
                        </template>

                        <div class="flex justify-between items-center">
                            <h1 class="font-bold text-lg text-gray-700">
                                <i class="fas fa-tags text-lg text-primary inline-flex mr-1"></i>
                                Daftar Kategori
                            </h1>

                            <button @click="openAddCategory()" type="button"
                                class="bg-white hover:bg-gray-200 transition-colors duration-300 ease-in-out p-2 rounded-md flex items-center justify-center cursor-pointer">
                                <i class="fas fa-plus text-primary"></i>
                            </button>
                        </div>

                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="overflow-x-auto max-h-[40vh] overflow-y-auto">
                                <table class="w-full min-w-max">
                                    <thead class="bg-primary text-white sticky top-0 z-10">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Nama Kategori</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Jumlah Produk</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-if="categories.length === 0">
                                            <tr>
                                                <td colspan="3" class="py-6">
                                                    <div class="w-full flex flex-col items-center justify-center text-gray-500">
                                                        <video src="<?= base_url('videos/nodata.mp4') ?>" class="w-64 h-36 mb-2" autoplay muted loop></video>
                                                        <span class="text-center">Tidak ada kategori yang tersedia</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>

                                        <template x-for="(category, index) in paginatedCategories" :key="category.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm" x-text="category.category_name"></td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-blue-700 bg-blue-100 rounded-full"
                                                        x-text="category.product_count || 0">
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <div class="w-full flex flex-row items-center justify-center gap-2">
                                                        <button
                                                            type="button"
                                                            @click="openEditCategory(category)"
                                                            title="Edit Kategori"
                                                            aria-label="Edit Kategori"
                                                            class="w-full md:w-auto flex items-center justify-center p-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition">
                                                            <i class="fas fa-pen"></i>
                                                        </button>

                                                        <button
                                                            type="button"
                                                            @click="openDeleteCategory(category)"
                                                            title="Hapus Kategori"
                                                            aria-label="Hapus Kategori"
                                                            class="w-full md:w-auto flex items-center justify-center p-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Menampilkan
                                    <span class="font-semibold" x-text="categories.length === 0 ? 0 : ((dataCategoriesPage - 1) * dataCategoriesPageSize) + 1"></span>
                                    hingga
                                    <span class="font-semibold" x-text="Math.min(dataCategoriesPage * dataCategoriesPageSize, categories.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="categories.length"></span>
                                    kategori
                                </div>

                                <div class="flex items-center gap-2" x-show="totalCategoriesPages > 1">
                                    <button
                                        @click="changeDataCategoriesPage(dataCategoriesPage - 1)"
                                        :disabled="dataCategoriesPage === 1"
                                        :class="dataCategoriesPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getDataCategoriesNumber()" :key="page">
                                        <button
                                            @click="changeDataCategoriesPage(page)"
                                            :class="page === dataCategoriesPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-3 py-1.5 rounded-md border border-gray-300 text-sm font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button
                                        @click="changeDataCategoriesPage(dataCategoriesPage + 1)"
                                        :disabled="dataCategoriesPage === totalCategoriesPages"
                                        :class="dataCategoriesPage === totalCategoriesPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->include('components/admin/modals/product-modals'); ?>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/main/admin/master-data/product.js') ?>"></script>
<?= $this->endSection() ?>