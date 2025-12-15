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

            <div class="mt-4 md:mt-6 lg:mt-8 px-4 md:px-6 lg:px-8 lg:pb-8 flex flex-col 2xl:flex-row gap-5">
                <div class="flex flex-col space-y-2 w-full lg:flex-1">
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
                    <template x-if="message">
                        <div x-text="message" class="fixed top-10 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-md z-50"></div>
                    </template>

                    <template x-if="error">
                        <div x-text="error" class="fixed top-10 right-5 bg-red-500 text-white px-4 py-2 rounded shadow-md z-50"></div>
                    </template>

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
                                                        <video src="<?= base_url('videos/nodata.mp4') ?>" class="w-64 h-36 mb-2" autoplay muted loop></video>
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
                            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Menampilkan
                                    <span class="font-semibold" x-text="restocks.length === 0 ? 0 : ((restocksPage - 1) * restocksPageSize) + 1"></span>
                                    hingga
                                    <span class="font-semibold" x-text="Math.min(restocksPage * restocksPageSize, restocks.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="restocks.length"></span>
                                    permintaan
                                </div>

                                <div class="flex items-center gap-2" x-show="totalRestockPages > 1">
                                    <button
                                        @click="changeRestocksPage(restocksPage - 1)"
                                        :disabled="restocksPage === 1"
                                        :class="restocksPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getRestocksNumber()" :key="page">
                                        <button
                                            @click="changeRestocksPage(page)"
                                            :class="page === restocksPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-3 py-1.5 rounded-md border border-gray-300 text-sm font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button
                                        @click="changeRestocksPage(restocksPage + 1)"
                                        :disabled="restocksPage === totalRestockPages"
                                        :class="restocksPage === totalRestockPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
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
                                        <div>
                                            <label class="text-sm text-gray-600">Harga Beli</label>
                                            <input type="number" x-model="restockPurchasePrice"
                                                class="mt-1 w-full px-3 py-2 rounded-md border focus:ring-primary focus:border-primary text-sm">
                                        </div>
                                        <div>
                                            <label class="text-sm text-gray-600">Slot (Opsional)</label>
                                            <input type="text" x-model="restockSlot"
                                                class="mt-1 w-full px-3 py-2 rounded-md border focus:ring-primary focus:border-primary text-sm"
                                                placeholder="Contoh: A-1">
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
                                                                    <span x-text="[b.rack, b.row, b.slot].filter(Boolean).join(' / ') || '-' "></span>
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
                </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/main/cashier/products.js') ?>"></script>
<?= $this->endSection() ?>