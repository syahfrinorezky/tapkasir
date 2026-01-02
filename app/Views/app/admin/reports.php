<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Laporan Bisnis
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="">
    <div class="fixed top-0 w-full z-40">
        <?= $this->include('components/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/sidebar'); ?>

        <div x-data="reports()" class="flex flex-col flex-1 font-secondary overflow-y-auto min-h-screen">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center pt-24 px-5 pb-4 md:pt-28 md:px-8 md:pb-6 lg:p-8 gap-4">
                <div class="w-full md:w-auto">
                    <h1 class="text-xl md:text-2xl text-primary font-primary font-bold">Laporan Bisnis</h1>
                    <p class="text-gray-600 text-sm md:text-base">Analisis mendalam performa bisnis Anda.</p>
                </div>
                
                <div class="flex flex-row items-center gap-1 md:gap-2 w-full md:w-auto">
                    <div class="relative flex-1 md:w-auto min-w-0">
                        <div class="absolute inset-y-0 left-0 pl-2 md:pl-3 flex items-center pointer-events-none text-primary">
                            <i class="fas fa-calendar-alt text-xs md:text-sm"></i>
                        </div>
                        <input type="date" x-model="startDate" class="w-full md:w-auto pl-7 md:pl-10 pr-1 py-2 rounded-lg border border-transparent bg-primary/5 text-primary text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                    </div>
                    <span class="text-gray-400 text-xs md:text-base">-</span>
                    <div class="relative flex-1 md:w-auto min-w-0">
                        <div class="absolute inset-y-0 left-0 pl-2 md:pl-3 flex items-center pointer-events-none text-primary">
                            <i class="fas fa-calendar-alt text-xs md:text-sm"></i>
                        </div>
                        <input type="date" x-model="endDate" class="w-full md:w-auto pl-7 md:pl-10 pr-1 py-2 rounded-lg border border-transparent bg-primary/5 text-primary text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                    </div>
                    <button @click="fetchData()" class="bg-primary hover:bg-primary/90 text-white px-3 py-2 rounded-lg text-sm font-semibold shadow-md transition-all flex items-center justify-center gap-2 shrink-0">
                        <i class="fas fa-filter"></i> <span class="hidden md:inline">Terapkan</span>
                    </button>
                </div>
            </div>

            <hr class="border-gray-200 mx-5 md:mx-8 lg:mx-8">

            <div class="px-5 md:px-8 lg:px-8 mt-4 w-full overflow-x-auto no-scrollbar flex md:justify-center">
                <div class="relative inline-flex bg-primary/5 p-1 rounded-xl min-w-max">
                    <div x-ref="tabIndicator" class="absolute top-1 bottom-1 bg-white rounded-lg shadow-sm transition-all duration-300 ease-out" style="opacity: 0;"></div>

                    <button @click="activeTab = 'summary'" x-ref="tab_summary"
                        :class="activeTab === 'summary' ? 'text-primary font-semibold' : 'text-gray-500 font-medium hover:text-gray-700'"
                        class="relative z-10 px-4 py-2 rounded-md text-sm transition-colors duration-200 flex items-center whitespace-nowrap">
                        <i class="fas fa-chart-pie mr-2"></i>Ringkasan
                    </button>
                    <button @click="activeTab = 'products'" x-ref="tab_products"
                        :class="activeTab === 'products' ? 'text-primary font-semibold' : 'text-gray-500 font-medium hover:text-gray-700'"
                        class="relative z-10 px-4 py-2 rounded-md text-sm transition-colors duration-200 flex items-center whitespace-nowrap">
                        <i class="fas fa-box mr-2"></i>Produk & Kategori
                    </button>
                    <button @click="activeTab = 'cashiers'" x-ref="tab_cashiers"
                        :class="activeTab === 'cashiers' ? 'text-primary font-semibold' : 'text-gray-500 font-medium hover:text-gray-700'"
                        class="relative z-10 px-4 py-2 rounded-md text-sm transition-colors duration-200 flex items-center whitespace-nowrap">
                        <i class="fas fa-users mr-2"></i>Kinerja Kasir
                    </button>
                </div>
            </div>

            <div class="p-5 md:p-8 lg:p-8 space-y-6">
                
                <div x-show="activeTab === 'summary'" class="space-y-6">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                        <div class="bg-white p-4 md:p-5 rounded-lg shadow-md flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                            <div class="w-full">
                                <p class="text-xs md:text-sm text-gray-500">Total Pendapatan</p>
                                <h3 class="text-base md:text-xl font-bold text-primary mt-1 truncate" x-text="formatRupiah(data.summary.total_sales)"></h3>
                            </div>
                            <div class="flex items-center justify-center aspect-square w-10 h-10 md:w-12 md:h-12 rounded-md bg-gradient-to-br from-primary to-accent-2 shadow-secondary shadow-md self-end md:self-center">
                                <i class="fas fa-wallet text-lg md:text-2xl text-white"></i>
                            </div>
                        </div>
                        <div class="bg-white p-4 md:p-5 rounded-lg shadow-md flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                            <div class="w-full">
                                <p class="text-xs md:text-sm text-gray-500">Total Keuntungan</p>
                                <h3 class="text-base md:text-xl font-bold text-emerald-600 mt-1 truncate" x-text="formatRupiah(data.summary.total_profit)"></h3>
                            </div>
                            <div class="flex items-center justify-center aspect-square w-10 h-10 md:w-12 md:h-12 rounded-md bg-gradient-to-br from-emerald-500 to-emerald-400 shadow-md self-end md:self-center">
                                <i class="fas fa-hand-holding-dollar text-lg md:text-2xl text-white"></i>
                            </div>
                        </div>
                        <div class="bg-white p-4 md:p-5 rounded-lg shadow-md flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                            <div class="w-full">
                                <p class="text-xs md:text-sm text-gray-500">Total Transaksi</p>
                                <h3 class="text-base md:text-xl font-bold text-primary mt-1 truncate" x-text="data.summary.total_transactions"></h3>
                            </div>
                            <div class="flex items-center justify-center aspect-square w-10 h-10 md:w-12 md:h-12 rounded-md bg-gradient-to-br from-primary to-accent-2 shadow-secondary shadow-md self-end md:self-center">
                                <i class="fas fa-receipt text-lg md:text-2xl text-white"></i>
                            </div>
                        </div>
                        <div class="bg-white p-4 md:p-5 rounded-lg shadow-md flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                            <div class="w-full">
                                <p class="text-xs md:text-sm text-gray-500">Item Terjual</p>
                                <h3 class="text-base md:text-xl font-bold text-primary mt-1 truncate" x-text="data.summary.total_items"></h3>
                            </div>
                            <div class="flex items-center justify-center aspect-square w-10 h-10 md:w-12 md:h-12 rounded-md bg-gradient-to-br from-primary to-accent-2 shadow-secondary shadow-md self-end md:self-center">
                                <i class="fas fa-cubes text-lg md:text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="bg-white p-4 md:p-6 rounded-lg shadow-md lg:col-span-2">
                            <h3 class="font-bold text-gray-700 mb-4 text-sm md:text-base">Tren Pendapatan & Keuntungan</h3>
                            <div class="h-64 md:h-80 relative">
                                <canvas id="dailyChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white p-4 md:p-6 rounded-lg shadow-md">
                            <h3 class="font-bold text-gray-700 mb-4 text-sm md:text-base">Jam Tersibuk</h3>
                            <div class="h-64 md:h-80 relative">
                                <canvas id="hourlyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'products'" class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="bg-white rounded-lg shadow-md lg:col-span-2 overflow-hidden">
                            <div class="p-4 md:p-6 border-b border-gray-100">
                                <h3 class="font-bold text-gray-700 text-sm md:text-base">Top 10 Produk Terlaris (Berdasarkan Pendapatan)</h3>
                            </div>
                            <div class="overflow-x-auto p-3">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-primary text-white whitespace-nowrap">
                                            <th class="px-3 py-2 md:px-6 md:py-3 text-xs md:text-sm font-semibold rounded-l-lg">Produk</th>
                                            <th class="px-3 py-2 md:px-6 md:py-3 text-xs md:text-sm font-semibold">Kategori</th>
                                            <th class="px-3 py-2 md:px-6 md:py-3 text-center text-xs md:text-sm font-semibold">Terjual</th>
                                            <th class="px-3 py-2 md:px-6 md:py-3 text-right text-xs md:text-sm font-semibold">Pendapatan</th>
                                            <th class="px-3 py-2 md:px-6 md:py-3 text-right text-xs md:text-sm font-semibold rounded-r-lg">Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="prod in data.tables.top_products" :key="prod.product_name">
                                            <tr class="hover:bg-gray-50 whitespace-nowrap">
                                                <td class="px-3 py-2 md:px-6 md:py-4 font-medium text-gray-800 text-xs md:text-base" x-text="prod.product_name"></td>
                                                <td class="px-3 py-2 md:px-6 md:py-4 text-xs md:text-sm text-gray-500">
                                                    <span class="px-2 py-1 bg-gray-100 rounded text-[10px] md:text-xs" x-text="prod.category_name"></span>
                                                </td>
                                                <td class="px-3 py-2 md:px-6 md:py-4 text-center font-bold text-gray-700 text-xs md:text-base" x-text="prod.qty"></td>
                                                <td class="px-3 py-2 md:px-6 md:py-4 text-right text-xs md:text-sm font-medium text-primary" x-text="formatRupiah(prod.revenue)"></td>
                                                <td class="px-3 py-2 md:px-6 md:py-4 text-right text-xs md:text-sm font-medium text-emerald-600" x-text="formatRupiah(prod.profit)"></td>
                                            </tr>
                                        </template>
                                        <tr x-show="data.tables.top_products.length === 0">
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                                <div class="w-full flex flex-col items-center justify-center text-gray-500 min-h-[200px]">
                                                    <img src="<?= base_url('images/illustration/nodata.png') ?>" class="w-32 md:w-48 lg:w-64 h-auto mb-2 object-contain" alt="No Data">
                                                    <span class="text-center">Belum ada data penjualan.</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-white p-4 md:p-6 rounded-lg shadow-md flex flex-col">
                            <h3 class="font-bold text-gray-700 mb-4 text-sm md:text-base">Kontribusi Kategori</h3>
                            
                            <div x-show="data.charts.categories.length > 0">
                                <div class="h-56 md:h-64 relative flex items-center justify-center">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                                <div class="mt-4 space-y-2">
                                    <template x-for="(cat, index) in data.charts.categories" :key="index">
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600" x-text="cat.category_name || 'Lainnya'"></span>
                                            <span class="font-medium text-gray-800" x-text="formatRupiah(cat.total_sales)"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div x-show="data.charts.categories.length === 0" class="w-full flex-1 min-h-[256px] flex flex-col items-center justify-center text-gray-400 text-center">
                                <img src="<?= base_url('images/illustration/nodata.png') ?>" class="w-32 md:w-40 lg:w-48 h-auto mb-2 object-contain" alt="No Data">
                                <p class="text-sm">Belum ada data kategori untuk ditampilkan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'cashiers'">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-4 md:p-6 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="font-bold text-gray-700 text-sm md:text-base">Kinerja Kasir</h3>
                        </div>
                        <div class="overflow-x-auto p-3">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-primary text-white whitespace-nowrap">
                                        <th class="px-3 py-2 md:px-6 md:py-3 text-xs md:text-sm font-semibold rounded-l-lg">Nama Kasir</th>
                                        <th class="px-3 py-2 md:px-6 md:py-3 text-center text-xs md:text-sm font-semibold">Total Transaksi</th>
                                        <th class="px-3 py-2 md:px-6 md:py-3 text-right text-xs md:text-sm font-semibold">Total Penjualan</th>
                                        <th class="px-3 py-2 md:px-6 md:py-3 text-right text-xs md:text-sm font-semibold rounded-r-lg">Rata-rata per Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="cashier in data.tables.cashiers" :key="cashier.cashier_name">
                                        <tr class="hover:bg-gray-50 whitespace-nowrap">
                                            <td class="px-3 py-2 md:px-6 md:py-4">
                                                <div class="flex items-center gap-2 md:gap-3">
                                                    <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs md:text-base">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <span class="font-medium text-gray-800 text-xs md:text-base" x-text="cashier.cashier_name || 'Unknown'"></span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 md:px-6 md:py-4 text-center text-gray-700 text-xs md:text-base" x-text="cashier.total_transactions"></td>
                                            <td class="px-3 py-2 md:px-6 md:py-4 text-right font-bold text-gray-800 text-xs md:text-base" x-text="formatRupiah(cashier.total_sales)"></td>
                                            <td class="px-3 py-2 md:px-6 md:py-4 text-right text-gray-600 text-xs md:text-base" x-text="formatRupiah(cashier.total_sales / cashier.total_transactions)"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="data.tables.cashiers.length === 0">
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            <div class="w-full flex flex-col items-center justify-center text-gray-500 min-h-[200px]">
                                                <img src="<?= base_url('images/illustration/nodata.png') ?>" class="w-32 md:w-48 lg:w-64 h-auto mb-2 object-contain" alt="No Data">
                                                <span class="text-center">Belum ada data kinerja kasir.</span>
                                            </div>
                                        </td>
                                    </tr>
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
<script src="<?= base_url('js/main/admin/reports.js') ?>"></script>
<?= $this->endSection() ?>
