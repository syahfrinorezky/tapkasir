<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Dashboard Admin
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="">
    <div class="fixed top-0 w-full">
        <?= $this->include('components/admin/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/admin/sidebar'); ?>

        <div class="flex flex-col w-full font-secondary overflow-y-auto min-h-[100vh]">
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
                                <span class="text-lg md:text-xl font-bold text-primary">Rp. <?= number_format($todaySales, 0, ',', '.') ?></span>
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
            </div>
        </div>
    </div>

</main>
<?= $this->endSection() ?>