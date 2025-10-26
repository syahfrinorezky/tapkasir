<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Dashboard Admin
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="">
    <div class="fixed top-0 w-full z-40">
        <?= $this->include('components/admin/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/admin/sidebar'); ?>

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

            <div class="mt-4 md:mt-6 lg:mt-8 px-4 md:px-6 lg:px-8 lg:pb-8">
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
                                <span class="text-sm text-gray-500">Kasir Aktif</span>
                                <span class="text-lg md:text-xl font-bold text-primary" x-text="data.activeCashiers"></span>
                            </div>
                            <div class="hidden lg:flex items-center justify-center aspect-square w-12 rounded-md bg-gradient-to-br from-primary to-accent-2 shadow-secondary shadow-md">
                                <i class="fas fa-user-check text-2xl text-white"></i>
                            </div>
                            <div class="lg:hidden flex items-center relative">
                                <i class="fas fa-user-check text-5xl text-primary/10 absolute right-0"></i>
                            </div>
                        </div>
                        <div class="bg-white rounded-md shadow-md flex justify-between p-4 lg:p-6">
                            <div class="flex flex-col space-y-1">
                                <span class="text-sm text-gray-500">Akun Pending</span>
                                <span class="text-lg md:text-xl font-bold text-primary" x-text="data.pendingUser"></span>
                            </div>
                            <div class="hidden lg:flex items-center justify-center aspect-square w-12 rounded-md bg-gradient-to-br from-primary to-accent-2 shadow-secondary shadow-md">
                                <i class="fas fa-user-clock text-2xl text-white"></i>
                            </div>
                            <div class="lg:hidden flex items-center relative">
                                <i class="fas fa-user-clock text-5xl text-primary/10 absolute right-0"></i>
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
                        <div class="bg-white rounded-lg flex items-center justify-center shadow-md p-4 lg:p-6 lg:w-2/3">
                            <canvas id="salesChart"></canvas>
                        </div>

                        <div class="flex flex-col md:flex-row lg:flex-col gap-4 md:w-full lg:w-1/3">
                            <div class="bg-white rounded-lg shadow-md w-full p-4 lg:p-6">
                                <canvas id="morningShiftChart"></canvas>
                            </div>
                            <div class="bg-white rounded-lg shadow-md w-full p-4 lg:p-6">
                                <canvas id="nightShiftChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboard', () => ({
            loaded: false,
            data: {
                todaySales: 0,
                activeCashiers: 0,
                pendingUser: 0,
                productNeedRestock: 0,
                labels: [],
                totals: [],
                morningHours: [],
                morningTotals: [],
                nightHours: [],
                nightTotals: []
            },
            async init() {
                try {
                    const res = await fetch('/admin/dashboard/data');
                    const json = await res.json();

                    this.data = {
                        todaySales: json.todaySales,
                        activeCashiers: json.activeCashiers,
                        pendingUser: json.pendingUser,
                        productNeedRestock: json.productNeedRestock,
                        labels: json.labels,
                        totals: json.totals,
                        morningHours: json.morningHours,
                        morningTotals: json.morningTotals,
                        nightHours: json.nightHours,
                        nightTotals: json.nightTotals
                    };

                    this.renderCharts();
                    this.loaded = true;
                } catch (err) {
                    console.error('Gagal memuat data dashboard:', err);
                }
            },
            formatRupiah(v) {
                return 'Rp ' + (parseFloat(v ?? 0)).toLocaleString('id-ID');
            },
            renderCharts() {
                const data = this.data;

                const salesCtx = document.getElementById('salesChart').getContext('2d');
                new Chart(salesCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                                type: 'bar',
                                label: 'Total Penjualan (Rp)',
                                data: data.totals,
                                backgroundColor: 'rgba(99,102,241,0.3)',
                                borderColor: 'rgba(99,102,241,1)',
                                borderWidth: 1,
                                borderRadius: 6
                            },
                            {
                                type: 'line',
                                label: 'Tren Penjualan',
                                data: data.totals,
                                borderColor: 'rgba(16,185,129,1)',
                                backgroundColor: 'rgba(16,185,129,0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                pointRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true
                    }
                });

                const morningCtx = document.getElementById('morningShiftChart').getContext('2d');
                new Chart(morningCtx, {
                    type: 'line',
                    data: {
                        labels: data.morningHours,
                        datasets: [{
                            label: 'Shift Pagi (Rp)',
                            data: data.morningTotals,
                            borderColor: 'rgba(234,179,8,1)',
                            backgroundColor: 'rgba(234,179,8,0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true
                    }
                });

                const nightCtx = document.getElementById('nightShiftChart').getContext('2d');
                new Chart(nightCtx, {
                    type: 'line',
                    data: {
                        labels: data.nightHours,
                        datasets: [{
                            label: 'Shift Malam (Rp)',
                            data: data.nightTotals,
                            borderColor: 'rgba(37,99,235,1)',
                            backgroundColor: 'rgba(37,99,235,0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true
                    }
                });
            }
        }));
    });
</script>


<?= $this->endSection() ?>