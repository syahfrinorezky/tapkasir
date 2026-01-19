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

        <div class="flex flex-col flex-1 font-secondary overflow-y-auto overflow-x-hidden min-h-screen" x-data="productManagement()"
            x-init="fetchData()">
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

            <?= $this->include('components/notifications'); ?>

            <div class="mt-4 md:mt-6 lg:mt-8 px-4 md:px-6 lg:px-8 pb-20 flex flex-col 2xl:flex-row gap-5">
                <div class="flex flex-col space-y-2 w-full lg:flex-1">
                    <div class="flex justify-between items-center">
                        <h1 class="font-bold text-lg text-gray-700">
                            <i class="fas fa-box text-lg text-primary inline-flex mr-1"></i>
                            <span x-text="showTrash ? 'Sampah Produk' : 'Daftar Produk'"></span>
                        </h1>

                        <div class="flex items-center gap-2">
                            <button @click="toggleTrash()" type="button"
                                :class="showTrash ? 'bg-primary text-white hover:bg-primary/90' : 'bg-white text-primary hover:bg-gray-200'"
                                class="transition-colors duration-300 ease-in-out p-2 rounded-md flex items-center justify-center cursor-pointer"
                                title="Sampah / Restore">
                                <i class="fas fa-trash-restore"></i>
                            </button>
                            <button x-show="!showTrash" @click="openAddProduct()" type="button"
                                class="bg-white hover:bg-gray-200 transition-colors duration-300 ease-in-out p-2 rounded-md flex items-center justify-center cursor-pointer">
                                <i class="fas fa-plus text-primary"></i>
                            </button>
                        </div>
                    </div>

                    <div x-show="!showTrash" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="mt-4 mb-2">
                            <div class="flex gap-3">
                                <!-- Search Bar -->
                                <div class="relative flex-1 group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i
                                            class="fas fa-search text-gray-400 group-focus-within:text-primary transition-colors duration-200"></i>
                                    </div>
                                    <input type="text" x-model.debounce.300ms="searchQuery"
                                        placeholder="Cari nama produk, barcode..."
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm transition-all duration-200 outline-none">
                                </div>

                                <!-- Filter Button -->
                                <button @click="openFilter()"
                                    class="flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-primary/50 text-gray-700 hover:text-primary transition-all duration-200 shadow-sm group"
                                    :class="{'border-primary/50 text-primary bg-primary/5': activeCategoryFilter !== 'all' || activeStockFilter !== 'all' || activeSort !== 'newest'}">
                                    <i
                                        class="fas fa-sliders-h mr-2 group-hover:scale-110 transition-transform duration-200"></i>
                                    <span class="font-medium text-sm hidden sm:inline">Filter & Urutkan</span>
                                    <span class="font-medium text-sm sm:hidden">Filter</span>

                                    <!-- Badge Indicator -->
                                    <template
                                        x-if="activeCategoryFilter !== 'all' || activeStockFilter !== 'all' || activeSort !== 'newest'">
                                        <span class="ml-2 flex h-2 w-2 relative">
                                            <span
                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                                        </span>
                                    </template>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-md overflow-hidden mt-4">
                            <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
                                <table class="w-full min-w-max">
                                    <thead class="bg-primary text-white sticky top-0 z-10">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-semibold hidden md:table-cell">
                                                No</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Foto</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Nama Produk & Barcode</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Harga</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-semibold hidden md:table-cell">
                                                Kategori</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Stok</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-if="filteredProducts.length === 0">
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-gray-500">
                                                    <div
                                                        class="w-full flex flex-col items-center justify-center text-gray-500">
                                                        <img src="<?= base_url('images/illustration/nodata.png') ?>"
                                                            class="w-32 md:w-48 lg:w-64 h-auto mb-2 object-contain"
                                                            alt="No Data">
                                                        <span class="text-center">Tidak ada produk</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>

                                        <template x-for="(product, index) in paginatedProducts" :key="product.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-center hidden md:table-cell"
                                                    x-text="getProductRowNumber(index)"></td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    <template x-if="product.photo">
                                                        <img :src="product.photo" :alt="product.product_name"
                                                            @error="(e) => e.target.src = '<?= base_url('images/illustration/deletemodal-illustration.png') ?>'"
                                                            class="w-10 h-10 object-cover rounded mx-auto">
                                                    </template>
                                                    <template x-if="!product.photo">
                                                        <div
                                                            class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center mx-auto">
                                                            <i class="fas fa-image text-gray-400"></i>
                                                        </div>
                                                    </template>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-left">
                                                    <div class="flex flex-col items-start">
                                                        <span class="font-medium" x-text="product.product_name"></span>
                                                        <span class="text-xs text-gray-500" x-text="product.barcode"></span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-center"
                                                    x-text="formatCurrency(product.price)"></td>
                                                <td class="px-4 py-3 text-sm text-center hidden md:table-cell"
                                                    x-text="product.category_name"></td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    <span :class="stockClass(product.stock)"
                                                        x-text="product.stock"></span>
                                                </td>

                                                <td
                                                    class="px-4 py-3 text-sm space-x-2 flex items-center justify-center">
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
                            <div
                                class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-4">
                                <div class="text-xs sm:text-sm text-gray-600">
                                    <span class="hidden sm:inline">
                                        Menampilkan
                                        <span class="font-semibold"
                                            x-text="filteredProducts.length === 0 ? 0 : ((dataProductPage - 1) * dataProductPageSize) + 1"></span>
                                        hingga
                                        <span class="font-semibold"
                                            x-text="Math.min(dataProductPage * dataProductPageSize, filteredProducts.length)"></span>
                                        dari
                                        <span class="font-semibold" x-text="filteredProducts.length"></span>
                                        produk
                                    </span>
                                    <span class="sm:hidden">
                                        <span class="font-semibold"
                                            x-text="filteredProducts.length === 0 ? 0 : ((dataProductPage - 1) * dataProductPageSize) + 1"></span>
                                        -
                                        <span class="font-semibold"
                                            x-text="Math.min(dataProductPage * dataProductPageSize, filteredProducts.length)"></span>
                                        dari
                                        <span class="font-semibold" x-text="filteredProducts.length"></span>
                                    </span>
                                </div>

                                <div class="flex items-center gap-2" x-show="totalProductPages > 1">
                                    <button @click="changeDataProductPage(dataProductPage - 1)"
                                        :disabled="dataProductPage === 1"
                                        :class="dataProductPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 bg-white text-xs sm:text-sm font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getDataProductsNumber()" :key="page">
                                        <button @click="changeDataProductPage(page)"
                                            :class="page === dataProductPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 text-xs sm:text-sm font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button @click="changeDataProductPage(dataProductPage + 1)"
                                        :disabled="dataProductPage === totalProductPages"
                                        :class="dataProductPage === totalProductPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 bg-white text-xs sm:text-sm font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trash View -->
                    <div x-show="showTrash" x-cloak x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0">

                        <div class="bg-white rounded-lg shadow-md overflow-hidden mt-4">
                            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-red-50">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" @change="toggleSelectAllTrash($event)"
                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="text-sm text-gray-600"
                                        x-text="selectedTrash.length + ' dipilih'"></span>
                                </div>
                                <div class="flex gap-2" x-show="selectedTrash.length > 0">
                                    <button @click="restoreSelected()"
                                        class="px-3 py-1.5 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                                        <i class="fas fa-undo mr-1"></i> Pulihkan
                                    </button>
                                    <button @click="deletePermanentSelected()"
                                        class="px-3 py-1.5 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                        <i class="fas fa-trash-alt mr-1"></i> Hapus Permanen
                                    </button>
                                </div>
                            </div>

                            <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
                                <table class="w-full min-w-max">
                                    <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                                        <tr>
                                            <th class="w-10 px-4 py-3 text-center"></th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Foto</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Nama Produk</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-semibold hidden md:table-cell">
                                                Kategori</th>
                                            <th
                                                class="px-4 py-3 text-center text-sm font-semibold hidden md:table-cell">
                                                Dihapus Pada</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-if="trashProducts.length === 0">
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-gray-500">
                                                    <i class="fas fa-trash-alt text-4xl mb-2 text-gray-300"></i>
                                                    <p>Sampah kosong</p>
                                                </td>
                                            </tr>
                                        </template>

                                        <template x-for="product in paginatedTrashProducts" :key="product.id">
                                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                <td class="px-4 py-3 text-center">
                                                    <input type="checkbox" :value="product.id" x-model="selectedTrash"
                                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                                </td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    <template x-if="product.photo">
                                                        <img :src="product.photo"
                                                            class="w-10 h-10 object-cover rounded-md mx-auto shadow-sm border border-gray-200">
                                                    </template>
                                                    <template x-if="!product.photo">
                                                        <div
                                                            class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center mx-auto border border-gray-200">
                                                            <i class="fas fa-image text-gray-400"></i>
                                                        </div>
                                                    </template>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-center font-medium text-gray-900"
                                                    x-text="product.product_name"></td>
                                                <td class="px-4 py-3 text-sm text-center hidden md:table-cell"
                                                    x-text="product.category_name"></td>
                                                <td class="px-4 py-3 text-sm text-center text-gray-500 hidden md:table-cell"
                                                    x-text="formatDateTime(product.deleted_at)"></td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <button @click="confirmRestore(product.id)"
                                                            class="text-green-600 hover:text-green-800"
                                                            title="Pulihkan">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                        <button @click="confirmDeletePermanent(product.id)"
                                                            class="text-red-600 hover:text-red-800"
                                                            title="Hapus Permanen">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div
                                class="px-4 py-2 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-2">
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold"
                                        x-text="trashProducts.length === 0 ? 0 : ((trashProductPage - 1) * trashProductPageSize) + 1"></span>
                                    -
                                    <span class="font-semibold"
                                        x-text="Math.min(trashProductPage * trashProductPageSize, trashProducts.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="trashProducts.length"></span>
                                </div>

                                <div class="flex items-center gap-1" x-show="totalTrashProductPages > 1">
                                    <button @click="changeTrashProductPage(trashProductPage - 1)"
                                        :disabled="trashProductPage === 1"
                                        :class="trashProductPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getTrashProductPageNumbers()" :key="page">
                                        <button @click="changeTrashProductPage(page)"
                                            :class="page === trashProductPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-2.5 py-1.5 rounded border border-gray-300 text-xs font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button @click="changeTrashProductPage(trashProductPage + 1)"
                                        :disabled="trashProductPage === totalTrashProductPages"
                                        :class="trashProductPage === totalTrashProductPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 w-full 2xl:w-1/3">
                    <?= $this->include('components/notifications') ?>

                    <div class="relative flex bg-gray-100 p-1 rounded-lg">
                        <div class="absolute top-1 bottom-1 left-1 w-[calc((100%-0.5rem)/3)] bg-white rounded-md shadow transition-transform duration-300 ease-out"
                            :class="{
                                'translate-x-0': activeRightTab === 'categories',
                                'translate-x-full': activeRightTab === 'locations',
                                'translate-x-[200%]': activeRightTab === 'restocks'
                             }">
                        </div>

                        <button @click="activeRightTab = 'categories'"
                            :class="activeRightTab === 'categories' ? 'text-primary' : 'text-gray-500 hover:text-gray-700'"
                            class="relative z-10 flex-1 py-2 px-4 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-tags mr-2"></i>Kategori
                        </button>
                        <button @click="activeRightTab = 'locations'"
                            :class="activeRightTab === 'locations' ? 'text-primary' : 'text-gray-500 hover:text-gray-700'"
                            class="relative z-10 flex-1 py-2 px-4 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-warehouse mr-2"></i>Lokasi
                        </button>
                        <button @click="activeRightTab = 'restocks'"
                            :class="activeRightTab === 'restocks' ? 'text-primary' : 'text-gray-500 hover:text-gray-700'"
                            class="relative z-10 flex-1 py-2 px-4 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-truck-loading mr-2"></i>Restock
                            <span x-show="pendingRestockCount > 0" x-text="pendingRestockCount"
                                class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full shadow-sm"></span>
                        </button>
                    </div>

                    <div x-show="activeRightTab === 'categories'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100" class="flex flex-col space-y-2">
                        <div class="flex justify-between items-center">
                            <h1 class="font-bold text-lg text-gray-700"
                                x-text="showTrashCategories ? 'Sampah Kategori' : 'Daftar Kategori'"></h1>

                            <div class="flex items-center gap-2">
                                <button @click="toggleTrashCategories()" type="button"
                                    :class="showTrashCategories ? 'bg-primary text-white hover:bg-primary/90' : 'bg-white text-primary hover:bg-gray-200'"
                                    class="transition-colors duration-300 ease-in-out p-2 rounded-md flex items-center justify-center cursor-pointer"
                                    title="Sampah / Restore">
                                    <i class="fas fa-trash-restore"></i>
                                </button>
                                <button x-show="!showTrashCategories" @click="openAddCategory()" type="button"
                                    class="bg-white hover:bg-gray-200 transition-colors duration-300 ease-in-out p-2 rounded-md flex items-center justify-center cursor-pointer">
                                    <i class="fas fa-plus text-primary"></i>
                                </button>
                            </div>
                        </div>

                        <div x-show="showTrashCategories" x-cloak x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform translate-x-4"
                            x-transition:enter-end="opacity-100 transform translate-x-0"
                            class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-red-50">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" @change="toggleSelectAllTrashCategories($event)"
                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="text-sm text-gray-600"
                                        x-text="selectedTrashCategories.length + ' dipilih'"></span>
                                </div>
                                <div class="flex gap-2" x-show="selectedTrashCategories.length > 0">
                                    <button @click="restoreSelectedCategories()"
                                        class="px-3 py-1.5 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                                        <i class="fas fa-undo mr-1"></i> Pulihkan
                                    </button>
                                    <button @click="deletePermanentSelectedCategories()"
                                        class="px-3 py-1.5 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                        <i class="fas fa-trash-alt mr-1"></i> Hapus Permanen
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                                <table class="w-full min-w-max">
                                    <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                                        <tr>
                                            <th class="w-10 px-4 py-3 text-center"></th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Nama Kategori</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Dihapus Pada</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-if="trashCategories.length === 0">
                                            <tr>
                                                <td colspan="4" class="py-8 text-center text-gray-500">
                                                    <i class="fas fa-trash-alt text-4xl mb-2 text-gray-300"></i>
                                                    <p>Sampah kosong</p>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-for="category in paginatedTrashCategories" :key="category.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-center">
                                                    <input type="checkbox" :value="category.id"
                                                        x-model="selectedTrashCategories"
                                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900"
                                                    x-text="category.category_name"></td>
                                                <td class="px-4 py-3 text-sm text-center text-gray-500"
                                                    x-text="formatDateTime(category.deleted_at)"></td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    <div class="flex justify-center gap-2">
                                                        <button @click="confirmRestoreCategory(category.id)"
                                                            class="text-green-600 hover:text-green-800"
                                                            title="Pulihkan">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                        <button @click="confirmDeletePermanentCategory(category.id)"
                                                            class="text-red-600 hover:text-red-800"
                                                            title="Hapus Permanen">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div
                                class="px-4 py-2 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-2">
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold"
                                        x-text="trashCategories.length === 0 ? 0 : ((trashCategoriesPage - 1) * trashCategoriesPageSize) + 1"></span>
                                    -
                                    <span class="font-semibold"
                                        x-text="Math.min(trashCategoriesPage * trashCategoriesPageSize, trashCategories.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="trashCategories.length"></span>
                                </div>

                                <div class="flex items-center gap-1" x-show="totalTrashCategoriesPages > 1">
                                    <button @click="changeTrashCategoriesPage(trashCategoriesPage - 1)"
                                        :disabled="trashCategoriesPage === 1"
                                        :class="trashCategoriesPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getTrashCategoriesPageNumbers()" :key="page">
                                        <button @click="changeTrashCategoriesPage(page)"
                                            :class="page === trashCategoriesPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-2.5 py-1.5 rounded border border-gray-300 text-xs font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button @click="changeTrashCategoriesPage(trashCategoriesPage + 1)"
                                        :disabled="trashCategoriesPage === totalTrashCategoriesPages"
                                        :class="trashCategoriesPage === totalTrashCategoriesPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div x-show="!showTrashCategories" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform -translate-x-4"
                            x-transition:enter-end="opacity-100 transform translate-x-0"
                            class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
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
                                                    <div
                                                        class="w-full flex flex-col items-center justify-center text-gray-500">
                                                        <img src="<?= base_url('images/illustration/nodata.png') ?>"
                                                            class="w-32 md:w-48 lg:w-64 h-auto mb-2 object-contain"
                                                            alt="No Data">
                                                        <span class="text-center">Tidak ada kategori yang
                                                            tersedia</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>

                                        <template x-for="(category, index) in paginatedCategories" :key="category.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm" x-text="category.category_name"></td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    <span
                                                        class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-blue-700 bg-blue-100 rounded-full"
                                                        x-text="category.product_count || 0">
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <div class="w-full flex flex-row items-center justify-center gap-2">
                                                        <button type="button" @click="openEditCategory(category)"
                                                            title="Edit Kategori" aria-label="Edit Kategori"
                                                            class="w-full md:w-auto flex items-center justify-center p-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition">
                                                            <i class="fas fa-pen"></i>
                                                        </button>

                                                        <button type="button" @click="openDeleteCategory(category)"
                                                            title="Hapus Kategori" aria-label="Hapus Kategori"
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
                            <div
                                class="px-4 py-2 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-2">
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold"
                                        x-text="categories.length === 0 ? 0 : ((dataCategoriesPage - 1) * dataCategoriesPageSize) + 1"></span>
                                    -
                                    <span class="font-semibold"
                                        x-text="Math.min(dataCategoriesPage * dataCategoriesPageSize, categories.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="categories.length"></span>
                                </div>

                                <div class="flex items-center gap-1" x-show="totalCategoriesPages > 1">
                                    <button @click="changeDataCategoriesPage(dataCategoriesPage - 1)"
                                        :disabled="dataCategoriesPage === 1"
                                        :class="dataCategoriesPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getDataCategoriesNumber()" :key="page">
                                        <button @click="changeDataCategoriesPage(page)"
                                            :class="page === dataCategoriesPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-2.5 py-1.5 rounded border border-gray-300 text-xs font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button @click="changeDataCategoriesPage(dataCategoriesPage + 1)"
                                        :disabled="dataCategoriesPage === totalCategoriesPages"
                                        :class="dataCategoriesPage === totalCategoriesPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeRightTab === 'locations'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100" class="flex flex-col space-y-2">
                        <div class="flex justify-between items-center">
                            <h1 class="font-bold text-lg text-gray-700"
                                x-text="showTrashLocations ? 'Sampah Lokasi' : 'Lokasi Penyimpanan'"></h1>

                            <div class="flex items-center gap-2">
                                <button @click="toggleTrashLocations()" type="button"
                                    :class="showTrashLocations ? 'bg-primary text-white hover:bg-primary/90' : 'bg-white text-primary hover:bg-gray-200'"
                                    class="transition-colors duration-300 ease-in-out p-2 rounded-md flex items-center justify-center cursor-pointer"
                                    title="Sampah / Restore">
                                    <i class="fas fa-trash-restore"></i>
                                </button>
                                <button x-show="!showTrashLocations" @click="openAddLocation()" type="button"
                                    class="bg-white hover:bg-gray-200 transition-colors duration-300 ease-in-out p-2 rounded-md flex items-center justify-center cursor-pointer">
                                    <i class="fas fa-plus text-primary"></i>
                                </button>
                            </div>
                        </div>

                        <div x-show="showTrashLocations" x-cloak x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform translate-x-4"
                            x-transition:enter-end="opacity-100 transform translate-x-0"
                            class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-red-50">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" @change="toggleSelectAllTrashLocations($event)"
                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="text-sm text-gray-600"
                                        x-text="selectedTrashLocations.length + ' dipilih'"></span>
                                </div>
                                <div class="flex gap-2" x-show="selectedTrashLocations.length > 0">
                                    <button @click="restoreSelectedLocations()"
                                        class="px-3 py-1.5 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                                        <i class="fas fa-undo mr-1"></i> Pulihkan
                                    </button>
                                    <button @click="deletePermanentSelectedLocations()"
                                        class="px-3 py-1.5 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                        <i class="fas fa-trash-alt mr-1"></i> Hapus Permanen
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                                <table class="w-full min-w-max">
                                    <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                                        <tr>
                                            <th class="w-10 px-4 py-3 text-center"></th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Rak</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Baris</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Deskripsi</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Dihapus Pada</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-if="trashLocations.length === 0">
                                            <tr>
                                                <td colspan="6" class="py-8 text-center text-gray-500">
                                                    <i class="fas fa-trash-alt text-4xl mb-2 text-gray-300"></i>
                                                    <p>Sampah kosong</p>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-for="location in paginatedTrashLocations" :key="location.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-center">
                                                    <input type="checkbox" :value="location.id"
                                                        x-model="selectedTrashLocations"
                                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900"
                                                    x-text="location.rack"></td>
                                                <td class="px-4 py-3 text-sm text-gray-600" x-text="location.row"></td>
                                                <td class="px-4 py-3 text-sm text-gray-600"
                                                    x-text="location.description"></td>
                                                <td class="px-4 py-3 text-sm text-center text-gray-500"
                                                    x-text="formatDateTime(location.deleted_at)"></td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    <div class="flex justify-center gap-2">
                                                        <button @click="confirmRestoreLocation(location.id)"
                                                            class="text-green-600 hover:text-green-800"
                                                            title="Pulihkan">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                        <button @click="confirmDeletePermanentLocation(location.id)"
                                                            class="text-red-600 hover:text-red-800"
                                                            title="Hapus Permanen">
                                                            <i class="fas fa-trash-alt mr-1"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div
                                class="px-4 py-2 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-2">
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold"
                                        x-text="trashLocations.length === 0 ? 0 : ((trashLocationsPage - 1) * trashLocationsPageSize) + 1"></span>
                                    -
                                    <span class="font-semibold"
                                        x-text="Math.min(trashLocationsPage * trashLocationsPageSize, trashLocations.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="trashLocations.length"></span>
                                </div>

                                <div class="flex items-center gap-1" x-show="totalTrashLocationsPages > 1">
                                    <button @click="changeTrashLocationsPage(trashLocationsPage - 1)"
                                        :disabled="trashLocationsPage === 1"
                                        :class="trashLocationsPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getTrashLocationsPageNumbers()" :key="page">
                                        <button @click="changeTrashLocationsPage(page)"
                                            :class="page === trashLocationsPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-2.5 py-1.5 rounded border border-gray-300 text-xs font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button @click="changeTrashLocationsPage(trashLocationsPage + 1)"
                                        :disabled="trashLocationsPage === totalTrashLocationsPages"
                                        :class="trashLocationsPage === totalTrashLocationsPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div x-show="!showTrashLocations" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform -translate-x-4"
                            x-transition:enter-end="opacity-100 transform translate-x-0"
                            class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                                <table class="w-full min-w-max text-sm">
                                    <thead class="bg-primary text-white sticky top-0 z-10">
                                        <tr>
                                            <th class="px-3 py-2 text-center font-semibold">Rak</th>
                                            <th class="px-3 py-2 text-center font-semibold">Baris</th>
                                            <th class="px-3 py-2 text-center font-semibold">Status</th>
                                            <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-if="locations.length === 0">
                                            <tr>
                                                <td colspan="4" class="py-6">
                                                    <div
                                                        class="w-full flex flex-col items-center justify-center text-gray-500">
                                                        <img src="<?= base_url('images/illustration/nodata.png') ?>"
                                                            class="w-32 md:w-48 lg:w-64 h-auto mb-2 object-contain"
                                                            alt="No Data">
                                                        <span class="text-center">Belum ada lokasi penyimpanan</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>

                                        <template x-for="(loc, index) in paginatedLocations" :key="loc.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 py-2 text-center" x-text="loc.rack"></td>
                                                <td class="px-3 py-2 text-center" x-text="loc.row"></td>
                                                <td class="px-3 py-2 text-center">
                                                    <span
                                                        :class="loc.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                                                        x-text="loc.status === 'active' ? 'Active' : 'Inactive'">
                                                    </span>
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <button type="button" @click="openEditLocation(loc)"
                                                            title="Edit Lokasi"
                                                            class="flex items-center justify-center p-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition">
                                                            <i class="fas fa-pen"></i>
                                                        </button>
                                                        <button type="button" @click="openDeleteLocation(loc)"
                                                            title="Hapus Lokasi"
                                                            class="flex items-center justify-center p-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div
                                class="px-4 py-2 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-2">
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold"
                                        x-text="locations.length === 0 ? 0 : ((locationsPage - 1) * locationsPageSize) + 1"></span>
                                    -
                                    <span class="font-semibold"
                                        x-text="Math.min(locationsPage * locationsPageSize, locations.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="locations.length"></span>
                                </div>

                                <div class="flex items-center gap-1" x-show="totalLocationsPages > 1">
                                    <button @click="changeLocationsPage(locationsPage - 1)"
                                        :disabled="locationsPage === 1"
                                        :class="locationsPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getLocationsNumber()" :key="page">
                                        <button @click="changeLocationsPage(page)"
                                            :class="page === locationsPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-2.5 py-1.5 rounded border border-gray-300 text-xs font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button @click="changeLocationsPage(locationsPage + 1)"
                                        :disabled="locationsPage === totalLocationsPages"
                                        :class="locationsPage === totalLocationsPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeRightTab === 'restocks'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100" class="flex flex-col space-y-2">
                        <div class="flex justify-between items-center">
                            <h1 class="font-bold text-lg text-gray-700">
                                Permintaan Restock
                            </h1>
                        </div>

                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                                <table class="w-full min-w-max">
                                    <thead class="bg-primary text-white sticky top-0 z-10">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Peminta</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Produk</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Qty</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 text-sm">
                                        <template x-if="restocks.length === 0">
                                            <tr>
                                                <td colspan="5" class="py-6">
                                                    <div
                                                        class="w-full flex flex-col items-center justify-center text-gray-500">
                                                        <img src="<?= base_url('images/illustration/nodata.png') ?>"
                                                            class="w-32 md:w-48 lg:w-64 h-auto mb-2 object-contain"
                                                            alt="No Data">
                                                        <span class="text-center">Tidak ada permintaan</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>

                                        <template x-for="r in paginatedRestocks" :key="r.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3">
                                                    <div class="flex flex-col">
                                                        <span class="font-medium" x-text="r.requester || '-' "></span>
                                                        <span class="text-xs text-gray-500"
                                                            x-text="formatDateTime(r.created_at)"></span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex flex-col">
                                                        <span x-text="r.product_name"></span>
                                                        <span class="text-xs text-gray-500" x-text="r.barcode"></span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center" x-text="r.quantity"></td>
                                                <td class="px-4 py-3">
                                                    <span :class="statusClass(r.status)" x-text="statusLabel(r)"></span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <template x-if="r.receipt_image || r.user_note">
                                                            <button @click="openViewProof(r)"
                                                                class="flex items-center justify-center p-2 rounded-md bg-blue-500 hover:bg-blue-600 text-white transition-colors duration-300 ease-in-out cursor-pointer"
                                                                title="Lihat Bukti">
                                                                <i class="fas fa-file-image"></i>
                                                            </button>
                                                        </template>
                                                        
                                                        <template x-if="(r.status || '').toLowerCase() === 'pending'">
                                                            <div class="flex items-center justify-center gap-2">
                                                                <button @click="openApproveRestock(r)"
                                                                    :disabled="approvingRestockId === r.id || rejectingRestockId === r.id"
                                                                    class="flex items-center justify-center p-2 rounded-md bg-green-600 hover:bg-green-700 text-white transition-colors duration-300 ease-in-out disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
                                                                    title="Setujui">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                                <button @click="rejectRestock(r.id)"
                                                                    :disabled="approvingRestockId === r.id || rejectingRestockId === r.id"
                                                                    class="flex items-center justify-center p-2 rounded-md bg-red-600 hover:bg-red-700 text-white transition-colors duration-300 ease-in-out disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
                                                                    title="Tolak">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div
                                class="px-4 py-2 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-2">
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold"
                                        x-text="restocks.length === 0 ? 0 : ((restocksPage - 1) * restocksPageSize) + 1"></span>
                                    -
                                    <span class="font-semibold"
                                        x-text="Math.min(restocksPage * restocksPageSize, restocks.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="restocks.length"></span>
                                </div>

                                <div class="flex items-center gap-1" x-show="totalRestockPages > 1">
                                    <button @click="changeRestocksPage(restocksPage - 1)" :disabled="restocksPage === 1"
                                        :class="restocksPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getRestocksNumber()" :key="page">
                                        <button @click="changeRestocksPage(page)"
                                            :class="page === restocksPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-2.5 py-1.5 rounded border border-gray-300 text-xs font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button @click="changeRestocksPage(restocksPage + 1)"
                                        :disabled="restocksPage === totalRestockPages"
                                        :class="restocksPage === totalRestockPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div x-show="openFilterModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true" style="display: none;">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="openFilterModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"
                        @click="openFilterModal = false" aria-hidden="true">
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="openFilterModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                    Filter Produk
                                </h3>
                                <button @click="openFilterModal = false"
                                    class="text-gray-400 hover:text-gray-500 transition-colors">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>

                            <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-3">Kategori</label>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" @click="tempFilters.category = 'all'"
                                            class="px-4 py-2 rounded-full text-sm font-medium border transition-all duration-200"
                                            :class="tempFilters.category === 'all' ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-primary/50 hover:text-primary hover:bg-gray-50'">
                                            Semua
                                        </button>

                                        <template x-for="c in visibleCategories" :key="c.id">
                                            <button type="button" @click="tempFilters.category = c.id"
                                                class="px-4 py-2 rounded-full text-sm font-medium border transition-all duration-200"
                                                :class="String(tempFilters.category) === String(c.id) ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-primary/50 hover:text-primary hover:bg-gray-50'"
                                                x-text="c.category_name">
                                            </button>
                                        </template>

                                        <button x-show="categories.length > 8"
                                            @click="showAllCategories = !showAllCategories" type="button"
                                            class="px-4 py-2 rounded-full text-sm font-medium border border-dashed border-gray-300 text-gray-500 hover:text-primary hover:border-primary hover:bg-primary/5 transition-all duration-200 flex items-center gap-1">
                                            <span x-text="showAllCategories ? 'Sembunyikan' : 'Lihat Lainnya'"></span>
                                            <i class="fas"
                                                :class="showAllCategories ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-3">Status Stok</label>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="option in [
                                            {id: 'all', label: 'Semua'},
                                            {id: 'available', label: 'Tersedia'},
                                            {id: 'low', label: 'Menipis (<15)'},
                                            {id: 'empty', label: 'Habis'}
                                        ]">
                                            <button type="button" @click="tempFilters.stock = option.id"
                                                class="px-4 py-2 rounded-full text-sm font-medium border transition-all duration-200"
                                                :class="tempFilters.stock === option.id ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-primary/50 hover:text-primary hover:bg-gray-50'"
                                                x-text="option.label">
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <template x-for="sortOption in [
                                            {id: 'newest', label: 'Terbaru Ditambahkan', icon: 'fa-clock'},
                                            {id: 'price_high', label: 'Harga Tertinggi', icon: 'fa-arrow-up'},
                                            {id: 'price_low', label: 'Harga Terendah', icon: 'fa-arrow-down'},
                                            {id: 'stock_high', label: 'Stok Terbanyak', icon: 'fa-layer-group'},
                                            {id: 'stock_low', label: 'Stok Sedikit', icon: 'fa-exclamation-triangle'}
                                        ]">
                                            <div @click="tempFilters.sort = sortOption.id"
                                                class="cursor-pointer flex items-center justify-between p-3 rounded-xl border transition-all duration-200 group"
                                                :class="tempFilters.sort === sortOption.id ? 'border-primary bg-primary/5 ring-1 ring-primary' : 'border-gray-200 hover:border-primary/50 hover:bg-gray-50'">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center transition-colors"
                                                        :class="tempFilters.sort === sortOption.id ? 'bg-primary text-white' : 'bg-gray-100 text-gray-500 group-hover:bg-white group-hover:text-primary'">
                                                        <i class="fas" :class="sortOption.icon"></i>
                                                    </div>
                                                    <span class="text-sm font-medium"
                                                        :class="tempFilters.sort === sortOption.id ? 'text-primary' : 'text-gray-700'"
                                                        x-text="sortOption.label"></span>
                                                </div>
                                                <div x-show="tempFilters.sort === sortOption.id" class="text-primary">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" @click="applyFilters()"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                                Terapkan Filter
                            </button>
                            <button type="button" @click="resetFilters()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <?= $this->include('components/admin/modals/product-modals'); ?>
            <?= $this->include('components/admin/modals/trash-modals'); ?>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/main/admin/master-data/product.js') ?>"></script>
<?= $this->endSection() ?>