<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Dashboard Admin
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="">
    <div class="fixed top-0 w-full z-40">
        <?= $this->include('components/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/sidebar'); ?>

        <div x-data="dashboard()" x-init="init()" class="flex flex-col flex-1 font-secondary overflow-y-auto min-h-screen">
            <div class="flex justify-between items-center pt-22 px-4 pb-4 md:pt-24 md:px-6 md:pb-6 lg:p-8">
                <div class="">
                    <h1 class="text-xl text-primary font-primary font-bold">Dashboard Admin</h1>
                    <p class="text-sm md:text-base text-gray-500">Selamat datang, <span class="font-semibold text-shadow-primary"><?= session()->get('nama_lengkap') ?></span></p>
                </div>
                <div class="hidden md:block border border-gray-200 rounded-md px-3 py-2">
                    <?= date('l, d M Y'); ?>
                </div>
            </div>

            <hr class="border-gray-200 mx-4 md:mx-6 lg:mx-8">

            <div class="mt-4 md:mt-6 lg:mt-8 px-4 md:px-6 lg:px-8 pb-20">
                <div class="flex flex-col space-y-2">
                    <h1 class="font-bold text-lg text-gray-700">
                        <i class="fas fa-calculator text-lg text-primary inline-flex mr-1"></i>
                        Statistik Cepat
                    </h1>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-md shadow-md flex justify-between items-center p-4 lg:p-6 overflow-hidden">
                            <div class="flex flex-col space-y-1">
                                <span class="text-sm text-gray-500">Total Penjualan</span>
                                <span class="text-base lg:text-lg font-bold text-primary" x-text="formatRupiah(data.todaySales)"></span>
                            </div>
                            <div class="hidden lg:flex items-center justify-center aspect-square w-12 rounded-md bg-gradient-to-br from-primary to-accent-2 shadow-secondary shadow-md">
                                <i class="fas fa-shopping-cart text-2xl text-white"></i>
                            </div>
                            <div class="lg:hidden flex items-center relative flex-shrink-0">
                                <i class="fas fa-shopping-cart text-5xl text-primary/10 absolute right-0"></i>
                            </div>
                        </div>
                        <div class="bg-white rounded-md shadow-md flex justify-between items-center p-4 lg:p-6">
                            <div class="flex flex-col space-y-1">
                                <span class="text-sm text-gray-500">Total Transaksi</span>
                                <span class="text-lg md:text-xl font-bold text-primary" x-text="data.todayTransactions"></span>
                            </div>
                            <div class="hidden lg:flex items-center justify-center aspect-square w-12 rounded-md bg-gradient-to-br from-primary to-accent-2 shadow-secondary shadow-md">
                                <i class="fas fa-receipt text-2xl text-white"></i>
                            </div>
                            <div class="lg:hidden flex items-center relative">
                                <i class="fas fa-receipt text-5xl text-primary/10 absolute right-0"></i>
                            </div>
                        </div>
                        <div class="bg-white rounded-md shadow-md flex justify-between p-4 lg:p-6">
                            <div class="flex flex-col space-y-1">
                                <span class="text-sm text-gray-500">Item Terjual</span>
                                <span class="text-lg md:text-xl font-bold text-primary" x-text="data.todayItemsSold"></span>
                            </div>
                            <div class="hidden lg:flex items-center justify-center aspect-square w-12 rounded-md bg-gradient-to-br from-primary to-accent-2 shadow-secondary shadow-md">
                                <i class="fas fa-cubes text-2xl text-white"></i>
                            </div>
                            <div class="lg:hidden flex items-center relative">
                                <i class="fas fa-cubes text-5xl text-primary/10 absolute right-0"></i>
                            </div>
                        </div>
                        <div class="bg-white rounded-md shadow-md flex justify-between p-4 lg:p-6">
                            <div class="flex flex-col space-y-1">
                                <span class="text-sm text-gray-500">Perlu Restock</span>
                                <span class="text-lg md:text-xl font-bold text-primary" x-text="data.productNeedRestock"></span>
                            </div>
                            <div class="hidden lg:flex flex-shrink-0 items-center justify-center aspect-square w-12 rounded-md bg-gradient-to-br from-primary to-accent-2 shadow-secondary shadow-md">
                                <i class="fas fa-box text-2xl text-white"></i>
                            </div>
                            <div class="lg:hidden flex items-center relative">
                                <i class="fas fa-box text-5xl text-primary/10 absolute right-0"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 md:mt-8 lg:mt-10 flex flex-col space-y-2">
                    <h1 class="font-bold text-lg text-gray-700">
                        <i class="fas fa-chart-simple text-lg text-primary inline-flex mr-1"></i>
                        Grafik Data
                    </h1>
                    
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="bg-white rounded-lg shadow-md p-4 lg:p-6 lg:w-2/3">
                            <h2 class="font-bold text-gray-700 mb-4">Tren Penjualan 7 Hari Terakhir</h2>
                            <canvas id="salesChart"></canvas>
                        </div>

                        <div class="bg-white rounded-lg shadow-md p-4 lg:p-6 lg:w-1/3 flex flex-col">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="font-bold text-gray-700">Top Produk Hari Ini</h2>
                                <a href="<?= base_url('admin/products') ?>" class="text-xs text-primary hover:underline">Lihat Semua</a>
                            </div>
                            <div class="flex-1 overflow-y-auto flex flex-col pr-1">
                                <template x-if="data.topProducts.length === 0">
                                    <div class="flex flex-col items-center justify-center flex-1 h-full py-4 min-h-[200px]">
                                        <img src="<?= base_url('images/illustration/nodata.png') ?>" class="w-24 md:w-36 lg:w-48 h-auto opacity-80 object-contain" alt="No Data">
                                        <p class="text-gray-500 text-center text-sm mt-2">Belum ada penjualan hari ini.</p>
                                    </div>
                                </template>
                                <div class="grid grid-cols-2 gap-3" x-show="data.topProducts.length > 0">
                                    <template x-for="(product, index) in data.topProducts.slice(0, 4)" :key="index">
                                        <div class="bg-white border border-gray-100 rounded-lg p-3 hover:shadow-md transition-shadow duration-200 flex flex-col h-full">
                                            <!-- Product Image -->
                                            <div class="aspect-square w-full bg-gray-50 rounded-md overflow-hidden border border-gray-100 mb-2 relative group">
                                            <div class="aspect-square w-full bg-gray-50 rounded-md overflow-hidden border border-gray-100 mb-1.5 relative group">
                                                <template x-if="product.photo">
                                                    <img :src="product.photo" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                                </template>
                                                <template x-if="!product.photo">
                                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                        <i class="fas fa-image text-lg"></i>
                                                    </div>
                                                </template>
                                                <!-- Rank Number -->
                                                <div class="absolute top-1 left-1 bg-black/50 backdrop-blur-[1px] text-white text-[8px] font-bold px-1 py-0.5 rounded flex items-center justify-center min-w-[14px]">
                                                    <span x-text="'#' + (index + 1)"></span>
                                                </div>
                                            </div>
                                            
                                            <!-- Product Info -->
                                            <div class="flex flex-col flex-1 justify-between min-w-0">
                                                <div class="mb-1">
                                                    <div class="text-[9px] uppercase tracking-wider text-gray-500 font-medium truncate" x-text="product.category_name || 'Uncategorized'"></div>
                                                    <h3 class="text-[11px] font-bold text-gray-800 leading-tight truncate mt-0.5" :title="product.name" x-text="product.name"></h3>
                                                </div>
                                                
                                                <div class="flex items-end justify-between">
                                                    <span class="text-[11px] font-bold text-primary" x-text="formatRupiah(product.price)"></span>
                                                    <div class="text-[9px] font-medium text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded-full" x-text="product.total_sold + ' Sold'"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row gap-4 mt-4">
                        <div class="bg-white rounded-lg shadow-md p-4 lg:p-6 lg:w-1/2">
                            <h2 class="font-bold text-gray-700 mb-4">Statistik Per Jam (Hari Ini)</h2>
                            <canvas id="hourlySalesChart"></canvas>
                        </div>

                        <div class="bg-white rounded-lg shadow-md p-4 lg:p-6 lg:w-1/2 flex flex-col">
                            <h2 class="font-bold text-gray-700 mb-4">Transaksi Terakhir</h2>
                            <div class="overflow-x-auto flex-1 flex flex-col">
                                <table class="w-full text-sm text-left text-gray-500 flex-1">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2">Waktu</th>
                                            <th class="px-4 py-2">Kasir</th>
                                            <th class="px-4 py-2 text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="h-full">
                                        <template x-for="trx in data.recentTransactions" :key="trx.id">
                                            <tr class="bg-white border-b hover:bg-gray-50">
                                                <td class="px-4 py-2" x-text="new Date(trx.transaction_date).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})"></td>
                                                <td class="px-4 py-2" x-text="trx.cashier_name"></td>
                                                <td class="px-4 py-2 text-right font-medium text-gray-900" x-text="formatRupiah(trx.total)"></td>
                                            </tr>
                                        </template>
                                        <template x-if="data.recentTransactions.length === 0">
                                            <tr class="h-full">
                                                <td colspan="3" class="text-center py-4 align-middle h-full">
                                                    <div class="flex flex-col items-center justify-center h-full min-h-[200px]">
                                                        <img src="<?= base_url('images/illustration/nodata.png') ?>" class="w-24 md:w-36 lg:w-48 h-auto opacity-80 object-contain" alt="No Data">
                                                        <p class="text-gray-500 mt-2">Belum ada transaksi.</p>
                                                    </div>
                                                </td>
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
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= base_url('js/main/admin/dashboard.js') ?>"></script>
<?= $this->endSection() ?>