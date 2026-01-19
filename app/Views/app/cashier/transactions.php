<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Transaksi Kasir
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main>
    <div class="fixed top-0 w-full z-40">
        <?= $this->include('components/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/sidebar'); ?>

        <div class="flex flex-col flex-1 font-secondary overflow-y-auto min-h-screen"
            x-data="cashierTransactions('<?= rtrim(base_url(), '/') ?>')" x-init="init()">
            <div class="flex justify-between items-center pt-22 px-4 pb-4 md:pt-24 md:px-6 md:pb-6 lg:p-8">
                <div>
                    <h1 class="text-xl text-primary font-primary font-bold">Transaksi Kasir</h1>
                    <p class="text-gray-600">Scan atau masukkan barcode untuk menambahkan produk ke keranjang.</p>
                </div>
                <div class="hidden md:block border border-gray-200 rounded-md px-3 py-2">
                    <?= date('l, d M Y'); ?>
                </div>
            </div>

            <div class="px-4 md:px-6 lg:px-8">
                <?= $this->include('components/notifications') ?>

                <div x-show="pendingTransaction" x-transition class="fixed top-20 right-5 left-5 md:left-auto md:w-[400px] z-50">
                    <div class="bg-blue-50 border border-blue-200 text-blue-900 px-4 py-4 rounded-md shadow-lg">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-info-circle mt-1"></i>
                                <div>
                                    <div class="font-bold">Transaksi Tertunda (QRIS)</div>
                                    <div class="text-sm">Menunggu pembayaran untuk transaksi sebelumnya.</div>
                                </div>
                            </div>
                            <div class="flex gap-2 justify-end">
                                <button
                                    @click="cancelPendingTransaction()"
                                    :disabled="isLoading"
                                    class="px-3 py-1.5 text-xs font-medium text-red-600 bg-white border border-red-200 rounded hover:bg-red-50 disabled:opacity-50">
                                    Batalkan
                                </button>
                                <button
                                    @click="continuePendingTransaction()"
                                    :disabled="isLoading"
                                    class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 disabled:opacity-50">
                                    Lanjutkan Bayar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <template x-if="showShiftWarning">
                    <div class="fixed top-20 right-5 left-5 md:left-auto md:w-[360px] bg-yellow-50 border border-yellow-200 text-yellow-900 px-4 py-3 rounded-md shadow z-50">
                        <div class="flex items-start gap-2">
                            <div class="mt-0.5"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="flex-1">
                                <div class="font-semibold">Shift akan berakhir</div>
                                <div class="text-sm">Anda akan logout otomatis dalam <span class="font-mono font-bold" x-text="formatSeconds(shiftCountdownSec)"></span>. Selesaikan transaksi Anda.</div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="isLoading">
                    <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center">
                        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div>
                            <span class="text-gray-700">Memproses transaksi...</span>
                        </div>
                    </div>
                </template>
            </div>

            <hr class="border-gray-200 mx-4 md:mx-6 lg:mx-8">

            <div class="mt-4 md:mt-6 lg:mt-8 px-4 md:px-6 lg:px-8 pb-20">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label class="text-sm">Barcode / Scan</label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-primary">
                                    <i class="fas fa-barcode"></i>
                                </div>
                                <input
                                    x-ref="barcodeInput"
                                    x-model="barcode"
                                    @input="onInput()"
                                    @keydown.enter.prevent="highlightedIndex >= 0 ? selectHighlighted() : scanBarcode()"
                                    @keydown.arrow-down.prevent="moveHighlight(1)"
                                    @keydown.arrow-up.prevent="moveHighlight(-1)"
                                    @keydown.escape.prevent="closeSuggestions()"
                                    :disabled="isLoading"
                                    class="w-full pl-10 pr-3 py-2 rounded-lg border border-transparent bg-primary/5 text-primary text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 disabled:opacity-50 disabled:cursor-not-allowed"
                                    placeholder="Arahkan scanner atau ketik barcode lalu tekan Enter">

                                <div x-show="showSuggestions && !isLoading" x-transition @click.outside="closeSuggestions()" class="absolute left-0 right-0 mt-1 bg-white border rounded-lg shadow-lg z-20 max-h-72 overflow-auto">
                                    <template x-for="(p,idx) in suggestions" :key="p.id">
                                        <div
                                            :id="'sug-' + idx"
                                            @click="selectSuggestion(p)"
                                            @mouseenter="highlightedIndex = idx"
                                            class="px-3 py-2 cursor-pointer"
                                            :class="highlightedIndex === idx ? 'bg-primary/10' : 'hover:bg-gray-50'">
                                            <div class="flex items-center gap-3">
                                                <img :src="getProductImage(p)" @error="$event.target.src = getProductImage({})" alt="thumb" class="w-8 h-8 rounded object-cover border" />
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-sm font-medium truncate" x-text="p.product_name"></div>
                                                    <div class="text-[11px] text-gray-500 truncate" x-text="p.barcode"></div>
                                                </div>
                                                <div class="text-xs font-semibold text-primary" x-text="formatCurrency(p.price || 0)"></div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="suggestions.length === 0">
                                        <div class="px-3 py-2 text-sm text-gray-500">Tidak ada produk</div>
                                    </template>
                                </div>
                            </div>

                            <div class="mt-4 overflow-x-auto max-h-[55vh] overflow-y-auto rounded-lg border border-gray-100">
                                <table class="w-full min-w-max">
                                    <thead class="bg-primary text-white sticky top-0 z-10">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-sm font-semibold first:rounded-tl-lg">Produk</th>
                                            <th class="px-3 py-2 text-right text-sm font-semibold">Harga</th>
                                            <th class="px-3 py-2 text-center text-sm font-semibold">Qty</th>
                                            <th class="px-3 py-2 text-right text-sm font-semibold">Subtotal</th>
                                            <th class="px-3 py-2 text-center text-sm font-semibold last:rounded-tr-lg">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 text-sm">
                                        <template x-if="cart.length === 0">
                                            <tr>
                                                <td colspan="5" class="text-center py-8 text-gray-500">
                                                    <div class="w-full flex flex-col items-center justify-center text-gray-500">
                                                        <img src="<?= base_url('images/illustration/nodata.png') ?>" class="w-24 md:w-36 h-auto mb-2 object-contain" alt="No Data">
                                                        <span>Keranjang kosong</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>

                                        <template x-for="(it, idx) in paginatedCart" :key="it.product_id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 py-2" x-text="it.product_name"></td>
                                                <td class="px-3 py-2 text-right font-medium" x-text="formatCurrency(it.price)"></td>
                                                <td class="px-3 py-2 text-center">
                                                    <div class="inline-flex items-center bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden divide-x divide-gray-200">
                                                        <button @click="adjustQty(it.product_id, -1)" :disabled="isLoading" aria-label="Kurangi"
                                                            class="h-8 w-8 grid place-content-center text-primary hover:bg-primary/5 disabled:opacity-50">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="text" inputmode="numeric" pattern="[0-9]*"
                                                            :value="it.quantity"
                                                            @input="sanitizeQtyInput(it.product_id, $event)"
                                                            @change="updateQtyByProduct(it.product_id)"
                                                            :disabled="isLoading"
                                                            class="w-16 h-8 text-center font-medium outline-none bg-white disabled:opacity-50">
                                                        <button @click="adjustQty(it.product_id, 1)" :disabled="isLoading" aria-label="Tambah"
                                                            class="h-8 w-8 grid place-content-center text-primary hover:bg-primary/5 disabled:opacity-50">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2 text-right font-semibold" x-text="formatCurrency(it.price * it.quantity)"></td>
                                                <td class="px-3 py-2 text-center">
                                                    <button
                                                        @click="removeItem(idx + ((cartPage-1)*cartPageSize))"
                                                        :disabled="isLoading"
                                                        class="px-2 py-1 rounded-md border border-red-200 text-red-600 hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        Hapus
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
                                        <span class="font-semibold" x-text="filteredCart.length === 0 ? 0 : ((cartPage - 1) * cartPageSize) + 1"></span>
                                        hingga
                                        <span class="font-semibold" x-text="Math.min(cartPage * cartPageSize, filteredCart.length)"></span>
                                        dari
                                        <span class="font-semibold" x-text="filteredCart.length"></span>
                                        item
                                    </span>
                                    <span class="sm:hidden">
                                        <span class="font-semibold" x-text="filteredCart.length === 0 ? 0 : ((cartPage - 1) * cartPageSize) + 1"></span>
                                        -
                                        <span class="font-semibold" x-text="Math.min(cartPage * cartPageSize, filteredCart.length)"></span>
                                        dari
                                        <span class="font-semibold" x-text="filteredCart.length"></span>
                                    </span>
                                </div>

                                <div class="flex items-center gap-2" x-show="totalCartPages > 1">
                                    <button
                                        @click="changeCartPage(cartPage - 1)"
                                        :disabled="cartPage === 1"
                                        :class="cartPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 bg-white text-xs sm:text-sm font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getCartPageNumbers()" :key="page">
                                        <button
                                            @click="changeCartPage(page)"
                                            :class="page === cartPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 text-xs sm:text-sm font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button
                                        @click="changeCartPage(cartPage + 1)"
                                        :disabled="cartPage === totalCartPages"
                                        :class="cartPage === totalCartPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 bg-white text-xs sm:text-sm font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="w-full md:w-80">
                            <div class="p-4 border-t md:border-t-0 md:border-l border-gray-100">
                                <div class="mb-3">
                                    <div class="text-sm text-gray-600">Total</div>
                                    <div class="text-2xl font-bold" x-text="formatCurrency(total)"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="text-sm text-gray-700 block mb-1">Metode Pembayaran</label>
                                    <div class="relative flex bg-gray-100 p-1 rounded-lg">
                                        <div class="absolute top-1 bottom-1 left-1 w-[calc((100%-0.5rem)/2)] bg-white rounded-md shadow transition-transform duration-300 ease-out"
                                             :class="{
                                                'translate-x-0': paymentMethod === 'cash',
                                                'translate-x-full': paymentMethod === 'qris'
                                             }">
                                        </div>

                                        <button type="button"
                                            @click="paymentMethod = 'cash'"
                                            :class="paymentMethod === 'cash' ? 'text-primary' : 'text-gray-500 hover:text-gray-700'"
                                            class="relative z-10 flex-1 py-2 px-4 rounded-md text-sm font-medium transition-colors duration-200 text-center">
                                            <i class="fas fa-money-bill-wave mr-2"></i> Tunai
                                        </button>
                                        <button type="button"
                                            @click="paymentMethod = 'qris'"
                                            :class="paymentMethod === 'qris' ? 'text-primary' : 'text-gray-500 hover:text-gray-700'"
                                            class="relative z-10 flex-1 py-2 px-4 rounded-md text-sm font-medium transition-colors duration-200 text-center">
                                            <i class="fas fa-qrcode mr-2"></i> QRIS
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3" x-show="paymentMethod === 'cash'">
                                    <label class="text-sm text-gray-700">Pembayaran</label>
                                    <input
                                        type="number"
                                        x-model.number="payment"
                                        :disabled="isLoading"
                                        class="w-full border rounded px-3 py-2 mt-1 disabled:opacity-50">
                                </div>

                                <div class="mb-3" x-show="paymentMethod === 'cash'">
                                    <div class="text-sm text-gray-600">Kembalian</div>
                                    <div
                                        class="text-lg font-medium"
                                        x-text="formatCurrency(change)"
                                        :class="change < 0 ? 'text-red-600' : 'text-green-600'"></div>
                                </div>

                                <button
                                    @click="submitTransaction()"
                                    :disabled="isLoading || cart.length === 0 || (paymentMethod === 'cash' && payment < total) || pendingTransaction"
                                    class="w-full bg-primary text-white py-2 rounded-md hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                                    <template x-if="isLoading">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </template>
                                    <span x-text="isLoading ? 'Memproses...' : (pendingTransaction ? 'Selesaikan Pending' : (paymentMethod === 'qris' ? 'Generate QRIS' : 'Bayar'))"></span>
                                </button>

                                <template x-if="paymentMethod === 'cash' && payment > 0 && payment < total">
                                    <div class="mt-2 text-xs text-red-600 bg-red-50 p-2 rounded">
                                        Pembayaran kurang dari total
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= $snapUrl ?>" data-client-key="<?= $midtransClientKey ?>"></script>
<script src="<?= base_url('js/main/cashier/transactions.js') ?>"></script>
<?= $this->endSection() ?>