<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Log Transaksi
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main>
    <div class="fixed top-0 w-full z-40">
        <?= $this->include('components/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/sidebar'); ?>

        <div class="flex flex-col flex-1 font-secondary overflow-y-auto min-h-screen"
            x-data="transactionsManagement()" x-init="init()">
            <div class="flex justify-between items-center pt-22 px-4 pb-4 md:pt-24 md:px-6 md:pb-6 lg:p-8">
                <div>
                    <h1 class="text-xl text-primary font-primary font-bold">Daftar Transaksi</h1>
                    <p class="text-gray-600">Lihat log transaksi per tanggal dan shift.</p>
                </div>
                <div class="hidden md:block border border-gray-200 rounded-md px-3 py-2">
                    <?= date('l, d M Y'); ?>
                </div>
            </div>

            <hr class="border-gray-200 mx-4 md:mx-6 lg:mx-8">

            <div class="mt-4 md:mt-6 lg:mt-8 px-4 md:px-6 lg:px-8 pb-20">
                <div class="flex flex-row md:flex-row md:items-center justify-between mb-4 flex-wrap">
                    <div class="flex flex-row md:flex-row md:items-center gap-3 w-full">
                        <div class="w-1/2 md:w-56">
                            <label class="text-sm">Tanggal</label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-primary">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <input x-model="date" type="date" class="w-full pl-10 pr-3 py-2 rounded-lg border border-transparent bg-primary/5 text-primary text-sm focus:outline-none focus:ring-2 focus:ring-primary/30" />
                            </div>
                        </div>

                        <div class="w-1/2 md:w-56">
                            <label class="text-sm">Shift</label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-primary">
                                    <i class="fas fa-user-clock"></i>
                                </div>
                                <select x-model="shiftId" class="w-full pl-10 pr-8 py-2 rounded-lg border border-transparent bg-primary/5 text-primary text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    <option value="">Semua Shift</option>
                                    <template x-for="s in shifts" :key="s.id">
                                        <option :value="s.id" x-text="s.name"></option>
                                    </template>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-primary/70">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-hidden">
                        <div class="overflow-x-auto max-h-[65vh] overflow-y-auto">
                            <table class="w-full min-w-max">
                                <thead class="bg-primary text-white sticky top-0 z-10">
                                    <tr>
                                        <th class="px-2 py-2 text-center text-sm font-semibold">No</th>
                                        <th class="px-2 py-2 text-left text-sm font-semibold">No. Transaksi</th>
                                        <th class="px-2 py-2 text-left text-sm font-semibold">Kasir</th>

                                        <template x-if="!shiftId">
                                            <th class="px-2 py-2 text-left text-sm font-semibold">Shift</th>
                                        </template>
                                        <th class="px-2 py-2 text-center text-sm font-semibold">Total</th>
                                        <th class="px-2 py-2 text-center text-sm font-semibold">Metode</th>
                                        <th class="px-2 py-2 text-center text-sm font-semibold">Tanggal</th>
                                        <th class="px-2 py-2 text-center text-sm font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm">
                                    <template x-if="filteredTransactions.length === 0">
                                        <tr>
                                            <td :colspan="shiftId ? 7 : 8" class="text-center py-6 text-gray-500">
                                                <div class="w-full flex flex-col items-center justify-center text-gray-500">
                                                    <img src="<?= base_url('images/illustration/nodata.png') ?>" class="w-24 md:w-36 h-auto mb-2 object-contain" alt="No Data">
                                                    <span>Tidak ada transaksi</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="(t, idx) in paginatedTransactions" :key="t.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-2 py-2 text-center" x-text="getTransactionRowNumber(idx)"></td>
                                            <td class="px-2 py-2 text-left" x-text="t.no_transaction ?? t.id"></td>
                                            <td class="px-2 py-2 text-left" x-text="t.cashier ?? '-'"></td>
                                            <td class="px-2 py-2 text-left" x-text="t.shift_name || t.shift || '-'" x-show="!shiftId"></td>
                                            <td class="px-2 py-2 text-center font-medium" x-text="formatCurrency(t.total)"></td>
                                            <td class="px-2 py-2 text-center">
                                                <span class="px-2 py-1 rounded text-xs font-semibold"
                                                    :class="t.payment_method === 'qris' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                                    x-text="t.payment_method === 'qris' ? 'QRIS' : 'Tunai'">
                                                </span>
                                            </td>
                                            <td class="px-2 py-2 text-center" x-text="t.transaction_date ?? t.created_at ?? '-'"></td>
                                            <td class="px-2 py-2 flex justify-center">
                                                <button @click="openItems(t.id)" title="Lihat Items" class="flex items-center justify-center p-2 bg-primary text-white rounded-md hover:bg-primary/80 transition-colors duration-300 ease-in-out cursor-pointer">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>


                        <div class="hidden p-3">
                            <div class="w-full mb-3">
                                <div class="grid grid-cols-4 gap-2 text-xs text-gray-500 px-1">
                                    <div>No. Transaksi</div>
                                    <div>Kasir</div>
                                    <div class="text-right">Total</div>
                                    <div class="text-center">Aksi</div>
                                </div>
                            </div>
                            <template x-if="transactions.length === 0">
                                <div class="w-full flex flex-col items-center justify-center text-gray-500 py-6">
                                    <img src="<?= base_url('images/illustration/nodata.png') ?>" class="w-24 md:w-36 h-auto mb-2 object-contain" alt="No Data">
                                    <span>Tidak ada transaksi</span>
                                </div>
                            </template>

                            <template x-for="(t, idx) in transactions" :key="t.id">
                                <div class="bg-white border border-gray-100 rounded-md p-3 mb-3 shadow-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="text-xs text-gray-500">#<span x-text="idx + 1"></span></div>
                                                <div class="text-sm font-medium text-primary" x-text="t.no_transaction ?? t.id"></div>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-700" x-text="t.cashier ?? '-'">Kasir</div>

                                            <div class="mt-1 text-xs text-gray-500" x-show="!shiftId">Shift: <span x-text="t.shift_name || t.shift || '-'"></span></div>
                                            <div class="mt-1 text-sm text-gray-900 font-semibold">Total: <span x-text="formatCurrency(t.total)"></span></div>
                                            <div class="mt-1 text-xs text-gray-500">Tanggal: <span x-text="t.transaction_date ?? t.created_at ?? '-'"></span></div>
                                        </div>
                                        <div class="flex-shrink-0 flex items-start">
                                            <button @click="openItems(t.id)" class="flex items-center justify-center p-2 bg-primary text-white rounded-md hover:bg-primary/80 transition-colors duration-300 ease-in-out cursor-pointer">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-4">
                            <div class="text-xs sm:text-sm text-gray-600">
                                <span class="hidden sm:inline">
                                    Menampilkan
                                    <span class="font-semibold" x-text="filteredTransactions.length === 0 ? 0 : ((dataTransactionsPage - 1) * dataTransactionsPageSize) + 1"></span>
                                    hingga
                                    <span class="font-semibold" x-text="Math.min(dataTransactionsPage * dataTransactionsPageSize, filteredTransactions.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="filteredTransactions.length"></span>
                                    transaksi
                                </span>
                                <span class="sm:hidden">
                                    <span class="font-semibold" x-text="filteredTransactions.length === 0 ? 0 : ((dataTransactionsPage - 1) * dataTransactionsPageSize) + 1"></span>
                                    -
                                    <span class="font-semibold" x-text="Math.min(dataTransactionsPage * dataTransactionsPageSize, filteredTransactions.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="filteredTransactions.length"></span>
                                </span>
                            </div>

                            <div class="flex items-center gap-2" x-show="totalTransactionPages > 1">
                                <button
                                    @click="changeDataTransactionPage(dataTransactionsPage - 1)"
                                    :disabled="dataTransactionsPage === 1"
                                    :class="dataTransactionsPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 bg-white text-xs sm:text-sm font-medium text-gray-700 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </button>

                                <template x-for="page in getDataTransactionsNumber()" :key="page">
                                    <button
                                        @click="changeDataTransactionPage(page)"
                                        :class="page === dataTransactionsPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                        class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 text-xs sm:text-sm font-medium transition"
                                        x-text="page">
                                    </button>
                                </template>

                                <button
                                    @click="changeDataTransactionPage(dataTransactionsPage + 1)"
                                    :disabled="dataTransactionsPage === totalTransactionPages"
                                    :class="dataTransactionsPage === totalTransactionPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 bg-white text-xs sm:text-sm font-medium text-gray-700 transition">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Items modal detail -->
                    <div x-cloak x-show="showItemsModal" @click.self="closeItems()"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">

                        <div x-cloak x-show="showItemsModal"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                            class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all">

                            <div class="bg-primary flex items-center justify-between px-6 py-4">
                                <h3 class="text-lg font-semibold text-white">
                                    Detail Item
                                    <span class="text-white/90 ml-2" x-text="currentTransactionNo ?? currentTransactionId"></span>
                                </h3>
                                <button @click="closeItems()" class="p-2 rounded-md bg-white/10 hover:bg-white/20 text-white">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="px-6 py-5 overflow-auto max-h-[50vh]">
                                <table class="w-full min-w-max">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-sm text-left">#</th>
                                            <th class="px-3 py-2 text-sm text-left">Produk</th>
                                            <th class="px-3 py-2 text-sm text-center">Qty</th>
                                            <th class="px-3 py-2 text-sm text-center">Price</th>
                                            <th class="px-3 py-2 text-sm text-center">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-if="items.length === 0">
                                            <tr>
                                                <td colspan="5" class="px-3 py-6 text-center text-gray-500">Tidak ada item</td>
                                            </tr>
                                        </template>
                                        <template x-for="(it, i) in items" :key="it.id">
                                            <tr>
                                                <td class="px-3 py-2 text-sm" x-text="i + 1"></td>
                                                <td class="px-3 py-2 text-sm" x-text="it.product_name ?? '-'"></td>
                                                <td class="px-3 py-2 text-sm text-center" x-text="it.quantity"></td>
                                                <td class="px-3 py-2 text-sm text-center" x-text="formatCurrency(it.selling_price)"></td>
                                                <td class="px-3 py-2 text-sm text-center" x-text="formatCurrency(it.subtotal)"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/main/admin/transactions.js') ?>"></script>
<?= $this->endSection() ?>