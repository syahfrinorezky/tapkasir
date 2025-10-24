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

        <div class="flex flex-col flex-1 font-secondary overflow-y-auto min-h-screen">
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
                                <span class="text-base lg:text-lg font-bold text-primary <?= strlen(number_format($todaySales, 0, ',', '.')) > 6 ? 'text-sm lg:text-base' : '' ?>">Rp. <?= number_format($todaySales, 0, ',', '.') ?></span>
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
                                <span class="text-lg md:text-xl font-bold text-primary"><?= $activeCashiers ?></span>
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
                                <span class="text-lg md:text-xl font-bold text-primary"><?= $pendingUser ?></span>
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
                                <span class="text-lg md:text-xl font-bold text-primary"><?= $productNeedRestock ?></span>
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
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');

        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= $labels ?>,
                datasets: [{
                        type: 'bar',
                        label: 'Total Penjualan (Rp)',
                        data: <?= $totals ?>,
                        backgroundColor: 'rgba(99, 102, 241, 0.3)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 1,
                        borderRadius: 6
                    },
                    {
                        type: 'line',
                        label: 'Tren Penjualan',
                        data: <?= $totals ?>,
                        borderColor: 'rgba(16, 185, 129, 1)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(16, 185, 129, 1)'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000) + 'jt';
                                } else if (value >= 100000) {
                                    return 'Rp ' + (value / 1000) + 'k';
                                }
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle'
                        },
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });


        const ctx2 = document.getElementById('morningShiftChart').getContext('2d');

        const morningChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: <?= $morningHours ?>,
                datasets: [{
                    label: 'Shift Pagi (Rp)',
                    data: <?= $morningTotals ?>,
                    borderColor: 'rgba(234, 179, 8, 1)',
                    backgroundColor: 'rgba(234, 179, 8, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(234, 179, 8, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: context => 'Rp ' + context.parsed.y.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000) + 'jt';
                                } else if (value >= 100000) {
                                    return 'Rp ' + (value / 1000) + 'k';
                                }
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        const ctx3 = document.getElementById('nightShiftChart').getContext('2d');

        const nightChart = new Chart(ctx3, {
            type: 'line',
            data: {
                labels: <?= $nightHours ?>,
                datasets: [{
                    label: 'Shift Malam (Rp)',
                    data: <?= $nightTotals ?>,
                    borderColor: 'rgba(37, 99, 235, 1)',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(37, 99, 235, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: context => 'Rp ' + context.parsed.y.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000) + 'jt';
                                } else if (value >= 100000) {
                                    return 'Rp ' + (value / 1000) + 'k';
                                }
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<?= $this->endSection() ?>