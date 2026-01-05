<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Produk & Restock
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main>
    <div class="fixed top-0 w-full z-40">
        <?= $this->include('components/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/sidebar'); ?>

        <div class="flex flex-col flex-1 font-secondary overflow-y-auto min-h-screen"
            x-data="cashierProducts()" x-init="init()">
            <div class="flex justify-between items-center pt-22 px-4 pb-4 md:pt-24 md:px-6 md:pb-6 lg:p-8">
                <div>
                    <h1 class="text-xl text-primary font-primary font-bold">Produk</h1>
                    <p class="text-gray-600">Lihat produk dan ajukan permintaan restock.</p>
                </div>
                <div class="hidden md:block border border-gray-200 rounded-md px-3 py-2">
                    <?= date('l, d M Y'); ?>
                </div>
            </div>

            <hr class="border-gray-200 mx-4 md:mx-6 lg:mx-8">

            <div class="mt-4 md:mt-6 lg:mt-8 px-4 md:px-6 lg:px-8 pb-20 flex flex-col 2xl:flex-row gap-5">
                <div class="flex flex-col space-y-2 w-full lg:flex-1">
                    <div class="px-1">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div class="flex w-full gap-3">
                                <div class="relative flex-1 group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400 group-focus-within:text-primary transition-colors duration-200"></i>
                                    </div>
                                    <input type="text" 
                                        x-model.debounce.300ms="searchQuery" 
                                        placeholder="Cari produk atau barcode..." 
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm transition-all duration-200 outline-none"
                                    >
                                </div>

                                <button @click="openFilter()" 
                                    class="flex items-center justify-center px-4 py-2.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-primary/50 text-gray-700 hover:text-primary transition-all duration-200 shadow-sm group"
                                    :class="{'border-primary/50 text-primary bg-primary/5': activeCategoryFilter !== 'all' || activeStockFilter !== 'all' || activeSort !== 'newest'}"
                                >
                                    <i class="fas fa-sliders-h mr-2 group-hover:scale-110 transition-transform duration-200"></i>
                                    <span class="font-medium text-sm hidden sm:inline">Filter & Urutkan</span>
                                    <span class="font-medium text-sm sm:hidden">Filter</span>
                                    
                                    <template x-if="activeCategoryFilter !== 'all' || activeStockFilter !== 'all' || activeSort !== 'newest'">
                                        <span class="ml-2 flex h-2 w-2 relative">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                                        </span>
                                    </template>
                                </button>
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
                                                    <img src="<?= base_url('images/illustration/nodata.png') ?>" class="w-32 md:w-48 lg:w-64 h-auto mb-2 object-contain" alt="No Data">
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
                                            <td class="px-4 py-3 text-sm text-center">
                                                <span :class="stockClass(product.stock)" x-text="product.stock"></span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-center" x-text="product.barcode"></td>
                                            <td class="px-4 py-3 text-sm space-x-2 flex items-center justify-center">
                                                <button type="button" @click="openDetail(product)"
                                                    title="Detail"
                                                    class="flex items-center justify-center p-2 bg-primary hover:bg-primary/80 text-white rounded-md transition-colors duration-300 ease-in-out cursor-pointer">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" @click="openRestock(product)"
                                                    title="Ajukan Restock"
                                                    class="flex items-center justify-center px-3 py-1.5 bg-primary hover:bg-primary/80 text-white rounded-md transition-colors duration-300 ease-in-out cursor-pointer text-sm">
                                                    <i class="fas fa-warehouse mr-1"></i> Restock
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-4">
                            <div class="text-xs sm:text-sm text-gray-600">
                                <span class="hidden sm:inline">
                                    Menampilkan
                                    <span class="font-semibold" x-text="filteredProducts.length === 0 ? 0 : ((dataProductPage - 1) * dataProductPageSize) + 1"></span>
                                    hingga
                                    <span class="font-semibold" x-text="Math.min(dataProductPage * dataProductPageSize, filteredProducts.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="filteredProducts.length"></span>
                                    produk
                                </span>
                                <span class="sm:hidden">
                                    <span class="font-semibold" x-text="filteredProducts.length === 0 ? 0 : ((dataProductPage - 1) * dataProductPageSize) + 1"></span>
                                    -
                                    <span class="font-semibold" x-text="Math.min(dataProductPage * dataProductPageSize, filteredProducts.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="filteredProducts.length"></span>
                                </span>
                            </div>

                            <div class="flex items-center gap-2" x-show="totalProductPages > 1">
                                <button
                                    @click="changeDataProductPage(dataProductPage - 1)"
                                    :disabled="dataProductPage === 1"
                                    :class="dataProductPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 bg-white text-xs sm:text-sm font-medium text-gray-700 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </button>

                                <template x-for="page in getDataProductsNumber()" :key="page">
                                    <button
                                        @click="changeDataProductPage(page)"
                                        :class="page === dataProductPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                        class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 text-xs sm:text-sm font-medium transition"
                                        x-text="page">
                                    </button>
                                </template>

                                <button
                                    @click="changeDataProductPage(dataProductPage + 1)"
                                    :disabled="dataProductPage === totalProductPages"
                                    :class="dataProductPage === totalProductPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 bg-white text-xs sm:text-sm font-medium text-gray-700 transition">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 w-full 2xl:w-1/3">
                    <?= $this->include('components/notifications') ?>

                    <div class="flex flex-col space-y-2">
                        <h1 class="font-bold text-lg text-gray-700">
                            <i class="fas fa-history text-lg text-primary inline-flex mr-1"></i>
                            Riwayat Permintaan Restock
                        </h1>

                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="overflow-x-auto max-h-[40vh] overflow-y-auto">
                                <table class="w-full min-w-max">
                                    <thead class="bg-primary text-white sticky top-0 z-10">
                                        <tr>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Waktu</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Produk</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Qty</th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 text-sm">
                                        <template x-if="restocks.length === 0">
                                            <tr>
                                                <td colspan="4" class="py-6">
                                                    <div class="w-full flex flex-col items-center justify-center text-gray-500">
                                                        <img src="<?= base_url('images/illustration/nodata.png') ?>" class="w-32 md:w-48 lg:w-64 h-auto mb-2 object-contain" alt="No Data">
                                                        <span class="text-center">Belum ada permintaan</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>

                                        <template x-for="r in paginatedRestocks" :key="r.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-center" x-text="formatDateTime(r.created_at)"></td>
                                                <td class="px-4 py-3" x-text="r.product_name"></td>
                                                <td class="px-4 py-3 text-center" x-text="r.quantity"></td>
                                                <td class="px-4 py-3">
                                                    <span :class="statusClass(r.status)" x-text="statusLabel(r)"></span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-4 py-2 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-2">
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold" x-text="restocks.length === 0 ? 0 : ((restocksPage - 1) * restocksPageSize) + 1"></span>
                                    -
                                    <span class="font-semibold" x-text="Math.min(restocksPage * restocksPageSize, restocks.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="restocks.length"></span>
                                </div>

                                <div class="flex items-center gap-1" x-show="totalRestockPages > 1">
                                    <button
                                        @click="changeRestocksPage(restocksPage - 1)"
                                        :disabled="restocksPage === 1"
                                        :class="restocksPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getRestocksNumber()" :key="page">
                                        <button
                                            @click="changeRestocksPage(page)"
                                            :class="page === restocksPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-2.5 py-1.5 rounded border border-gray-300 text-xs font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button
                                        @click="changeRestocksPage(restocksPage + 1)"
                                        :disabled="restocksPage === totalRestockPages"
                                        :class="restocksPage === totalRestockPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Restock Modal -->
                        <div
                            x-cloak
                            x-show="openRestockModal"
                            @click.self="closeRestock()"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                        </div>

                        <div
                            x-cloak
                            x-show="openRestockModal"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                            class="fixed inset-0 z-50 flex items-center justify-center p-4">

                            <div class="w-full max-w-sm sm:max-w-md lg:max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden">

                                <!-- Permintaan Restock Modal -->
                                <div class="bg-primary flex items-center justify-between px-5 py-4">
                                    <h3 class="text-lg font-semibold text-white">Permintaan Restock</h3>
                                    <button @click="closeRestock()" class="p-2 rounded-md bg-white/10 hover:bg-white/20 text-white">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <div class="px-5 py-4 space-y-3 max-h-[70vh] overflow-y-auto">

                                    <div>
                                        <label class="text-sm text-gray-600">Produk</label>
                                        <div class="mt-1 px-3 py-2 rounded-md border bg-gray-50 text-sm"
                                            x-text="selectedProduct?.product_name || '-'"></div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-sm text-gray-600">Jumlah</label>
                                            <div class="mt-1 flex items-center gap-1">
                                                <button @click="decQty()" class="px-2 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="text" x-model="restockQty" @input="sanitizeQty()"
                                                    class="w-full text-center px-2 py-2 rounded-md border focus:ring-primary focus:border-primary text-sm" />
                                                <button @click="incQty()" class="px-2 py-2 bg-gray-100 rounded hover:bg-gray-200">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-sm text-gray-600">Expired</label>
                                            <input type="date" x-model="restockExpiredDate"
                                                class="mt-1 w-full px-3 py-2 rounded-md border focus:ring-primary focus:border-primary text-sm">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="col-span-2">
                                            <label class="text-sm text-gray-600">Harga Beli (Per Item)</label>
                                            <input type="number" x-model="restockPurchasePrice"
                                                class="mt-1 w-full px-3 py-2 rounded-md border focus:ring-primary focus:border-primary text-sm">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="text-sm text-gray-600">Lokasi Penyimpanan</label>
                                        <select x-model="restockLocationId" @change="onRestockLocationChange()"
                                            class="mt-1 w-full px-3 py-2 rounded-md border focus:ring-primary focus:border-primary text-sm bg-white">
                                            <option value="">-- Pilih Lokasi --</option>
                                            <template x-for="loc in locations.filter(l => l.status === 'active').sort((a,b) => (a.rack || '').localeCompare(b.rack || '') || (a.row || '').localeCompare(b.row || ''))" :key="loc.id">
                                                <option :value="loc.id" x-text="`${loc.rack} - ${loc.row}`"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="text-sm text-gray-600">Bukti Struk</label>
                                        <div
                                            class="mt-1 p-4 border-2 border-dashed rounded-md text-center text-gray-500 hover:border-primary/60 hover:text-primary/80 cursor-pointer"
                                            @click="receiptPick()"
                                            @dragover.prevent
                                            @drop.prevent="handleReceiptDrop($event)">
                                            <i class="fas fa-cloud-upload-alt text-xl mb-1"></i>
                                            <p class="text-sm" x-text="receiptFileName ? 'File: ' + receiptFileName : 'Upload file'"></p>
                                            <input type="file" x-ref="receiptInput" class="hidden" @change="receiptChange($event)" accept="image/*,.pdf">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="text-sm text-gray-600">Catatan</label>
                                        <textarea x-model="restockNote" rows="2"
                                            class="mt-1 w-full px-3 py-2 rounded-md border focus:ring-primary focus:border-primary text-sm"
                                            placeholder="Contoh: stok akhir pekan"></textarea>
                                    </div>

                                </div>

                                <div class="px-5 py-4 bg-gray-50 flex flex-row justify-end items-center gap-2">
                                    <button @click="closeRestock()"
                                        class="px-4 py-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-100">Batal</button>

                                    <button @click="submitRestock()"
                                        :disabled="isSubmittingRestock || !restockQty || parseInt(restockQty)<=0"
                                        :class="(isSubmittingRestock || !restockQty || parseInt(restockQty)<=0) ? 'opacity-60 cursor-not-allowed' : ''"
                                        class="px-4 py-2 rounded-md bg-primary text-white hover:bg-primary/90 flex items-center justify-center gap-2">
                                        <i x-show="isSubmittingRestock" class="fas fa-circle-notch fa-spin"></i>
                                        <span x-text="isSubmittingRestock ? 'Mengirim...' : 'Kirim'"></span>
                                    </button>
                                </div>

                            </div>
                        </div>


                        <!-- Product Detail Modal -->
                        <div x-cloak x-show="openDetailModal" @click.self="closeDetail()"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50"></div>

                        <div
                            x-cloak
                            x-show="openDetailModal"
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
                                    <button @click="closeDetail()" class="p-2 rounded hover:bg-white/10">
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
                                            <p class="text-sm text-gray-500">Stok: <span :class="stockClass(selectedProduct?.stock || 0)" x-text="selectedProduct?.stock || 0"></span></p>
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
                                                        <template x-if="!selectedProduct || visibleBatches.length === 0">
                                                            <tr>
                                                                <td colspan="4" class="px-3 py-4 text-center text-gray-500">Tidak ada batch aktif yang ditampilkan</td>
                                                            </tr>
                                                        </template>
                                                        <template x-for="b in visibleBatches" :key="b.id">
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
                                        <div class="bg-white p-4 rounded-md shadow-inner">
                                            <img :src="'<?= base_url('cashier/products/barcode/image/') ?>' + encodeURIComponent(selectedProduct?.barcode || '')" alt="Barcode" class="w-56 h-auto">
                                        </div>
                                        <p class="text-sm text-gray-600 mt-2">Kode: <span class="font-mono" x-text="selectedProduct?.barcode || '-'"></span></p>
                                    </div>
                                </div>

                                <div class="px-5 py-4 bg-gray-50 flex items-center justify-center gap-3">
                                    <button @click="closeDetail()" class="w-full sm:w-auto px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>

            <!-- Filter Modal -->
            <div x-show="openFilterModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="openFilterModal" 
                        x-transition:enter="ease-out duration-300" 
                        x-transition:enter-start="opacity-0" 
                        x-transition:enter-end="opacity-100" 
                        x-transition:leave="ease-in duration-200" 
                        x-transition:leave-start="opacity-100" 
                        x-transition:leave-end="opacity-0" 
                        class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" 
                        @click="openFilterModal = false" aria-hidden="true">
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="openFilterModal" 
                        x-transition:enter="ease-out duration-300" 
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
                                <button @click="openFilterModal = false" class="text-gray-400 hover:text-gray-500 transition-colors">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>

                            <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-3">Kategori</label>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" 
                                            @click="tempFilters.category = 'all'"
                                            class="px-4 py-2 rounded-full text-sm font-medium border transition-all duration-200"
                                            :class="tempFilters.category === 'all' ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-primary/50 hover:text-primary hover:bg-gray-50'">
                                            Semua
                                        </button>
                                        
                                        <template x-for="c in visibleCategories" :key="c.id">
                                            <button type="button" 
                                                @click="tempFilters.category = c.id"
                                                class="px-4 py-2 rounded-full text-sm font-medium border transition-all duration-200"
                                                :class="String(tempFilters.category) === String(c.id) ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-primary/50 hover:text-primary hover:bg-gray-50'"
                                                x-text="c.category_name">
                                            </button>
                                        </template>

                                        <button x-show="categories.length > 8" 
                                            @click="showAllCategories = !showAllCategories"
                                            type="button"
                                            class="px-4 py-2 rounded-full text-sm font-medium border border-dashed border-gray-300 text-gray-500 hover:text-primary hover:border-primary hover:bg-primary/5 transition-all duration-200 flex items-center gap-1">
                                            <span x-text="showAllCategories ? 'Sembunyikan' : 'Lihat Lainnya'"></span>
                                            <i class="fas" :class="showAllCategories ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-3">Status Stok</label>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="option in [
                                            {id: 'all', label: 'Semua'},
                                            {id: 'available', label: 'Tersedia'},
                                            {id: 'low', label: 'Menipis (Restock)'},
                                            {id: 'empty', label: 'Habis'}
                                        ]">
                                            <button type="button" 
                                                @click="tempFilters.stock = option.id"
                                                class="px-4 py-2 rounded-full text-sm font-medium border transition-all duration-200"
                                                :class="tempFilters.stock === option.id ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:border-primary/50 hover:text-primary hover:bg-gray-50'"
                                                x-text="option.label">
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-3">Urutkan</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <template x-for="sortOption in [
                                            {id: 'newest', label: 'Terbaru Ditambahkan', icon: 'fa-clock'},
                                            {id: 'price_low', label: 'Harga Terendah', icon: 'fa-arrow-down'},
                                            {id: 'price_high', label: 'Harga Tertinggi', icon: 'fa-arrow-up'},
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
                                                    <span class="text-sm font-medium" :class="tempFilters.sort === sortOption.id ? 'text-primary' : 'text-gray-700'" x-text="sortOption.label"></span>
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
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="button" 
                                @click="applyFilters()"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:w-auto sm:text-sm transition-all duration-200">
                                Terapkan Filter
                            </button>
                            <button type="button" 
                                @click="resetFilters()"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:w-auto sm:text-sm transition-all duration-200">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
                </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/main/cashier/products.js') ?>"></script>
<?= $this->endSection() ?>