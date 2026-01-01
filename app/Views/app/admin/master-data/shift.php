<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Manajemen Shift
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main>
    <div class="fixed top-0 w-full z-40">
        <?= $this->include('components/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/sidebar'); ?>

        <div class="flex flex-col flex-1 font-secondary overflow-y-auto min-h-screen"
            x-data="shiftManagement()" x-init="fetchData()">
            <div class="flex justify-between items-center pt-22 px-4 pb-4 md:pt-24 md:px-6 md:pb-6 lg:p-8">
                <div>
                    <h1 class="text-xl text-primary font-primary font-bold">Manajemen Shift</h1>
                    <p class="text-gray-600">Kelola jadwal dan shift kasir di sistem kasir.</p>
                </div>
                <div class="hidden md:block border border-gray-200 rounded-md px-3 py-2">
                    <?= date('l, d M Y'); ?>
                </div>
            </div>

            <hr class="border-gray-200 mx-4 md:mx-6 lg:mx-8">

            <div class="mt-4 md:mt-6 lg:mt-8 px-4 md:px-6 lg:px-8 lg:pb-8 flex flex-col 2xl:flex-row gap-5">
                <div class="flex flex-col space-y-2 w-full lg:flex-1">
                    <h1 class="font-bold text-lg text-gray-700">
                        <i class="fas fa-cash-register text-lg text-primary inline-flex mr-1"></i>
                        Daftar Kasir
                    </h1>

                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
                            <table class="w-full min-w-max">
                                <thead class="bg-primary text-white sticky top-0 z-10">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">No</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Nama</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Email</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Shift</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-if="cashiers.length === 0">
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-gray-500">
                                                <div class="w-full flex flex-col items-center justify-center text-gray-500">
                                                    <video src="<?= base_url('videos/nodata.mp4') ?>" class="w-64 h-36 mb-2" autoplay muted loop></video>
                                                    <span class="text-center">Tidak ada kasir</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    <template x-for="(cashier, index) in paginatedCashiers" :key="cashier.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-center" x-text="getCashierRowNumber(index)"></td>
                                            <td class="px-4 py-3 text-sm" x-text="cashier.nama_lengkap"></td>
                                            <td class="px-4 py-3 text-sm text-center" x-text="cashier.email"></td>
                                            <td class="px-4 py-3 text-sm text-center" x-text="cashier.shift_name ?? '-'"></td>
                                            <td class="px-4 py-3 text-sm space-x-2 flex items-center justify-center">
                                                <button type="button" @click="openEditCashier(cashier)"
                                                    class="flex items-center justify-center p-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition-colors duration-300 ease-in-out cursor-pointer">
                                                    <i class="fas fa-pen"></i>
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
                                <span class="font-semibold" x-text="cashiers.length === 0 ? 0 : ((dataCashierPage - 1) * dataCashierPageSize) + 1"></span>
                                hingga
                                <span class="font-semibold" x-text="Math.min(dataCashierPage * dataCashierPageSize, cashiers.length)"></span>
                                dari
                                <span class="font-semibold" x-text="cashiers.length"></span>
                                data
                            </div>

                            <div class="flex items-center gap-2" x-show="totalCashierPages > 1">
                                <button
                                    @click="changeDataCashierPage(dataCashierPage - 1)"
                                    :disabled="dataCashierPage === 1"
                                    :class="dataCashierPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </button>

                                <template x-for="page in getDataCashiersNumber()" :key="page">
                                    <button
                                        @click="changeDataCashierPage(page)"
                                        :class="page === dataCashierPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 text-sm font-medium transition"
                                        x-text="page">
                                    </button>
                                </template>

                                <button
                                    @click="changeDataCashierPage(dataCashierPage + 1)"
                                    :disabled="dataCashierPage === totalCashierPages"
                                    :class="dataCashierPage === totalCashierPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 w-full 2xl:w-1/3">
                    <div class="flex flex-col space-y-2">
                        <?= $this->include('components/notifications') ?>

                        <div class="flex justify-between items-center">
                            <h1 class="font-bold text-lg text-gray-700">
                                <i class="fas fa-clock text-lg text-primary inline-flex mr-1"></i>
                                Daftar Shift
                            </h1>

                            <button @click="openAddShift()" type="button"
                                class="bg-white hover:bg-gray-200 transition-colors duration-300 ease-in-out p-2 rounded-md flex items-center justify-center cursor-pointer">
                                <i class="fas fa-plus text-primary"></i>
                            </button>
                        </div>

                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="overflow-x-auto max-h-[40vh] overflow-y-auto">
                                <table class="w-full min-w-max">
                                    <thead class="bg-primary text-white sticky top-0 z-10">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Nama Shift</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Jam Kerja</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Status</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-if="shifts.length === 0">
                                            <tr>
                                                <td colspan="4" class="py-6">
                                                    <div class="w-full flex flex-col items-center justify-center text-gray-500">
                                                        <video src="<?= base_url('videos/nodata.mp4') ?>" class="w-64 h-36 mb-2" autoplay muted loop></video>
                                                        <span class="text-center">Tidak ada shift yang tersedia</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>

                                        <template x-for="(shift, index) in paginatedShifts" :key="shift.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm" x-text="shift.name"></td>
                                                <td class="px-4 py-3 text-sm text-center" x-text="`${shift.start_time} - ${shift.end_time}`"></td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    <span class="text-xs uppercase px-2 py-1 rounded-full border"
                                                        :class="shift.status === 'active' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-500 border-gray-200'"
                                                        x-text="shift.status"></span>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <div class="w-full flex flex-row items-center justify-center gap-2">
                                                        <button
                                                            type="button"
                                                            @click="openEditShift(shift)"
                                                            title="Edit Shift"
                                                            aria-label="Edit Shift"
                                                            class="w-full md:w-auto flex items-center justify-center p-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition">
                                                            <i class="fas fa-pen"></i>
                                                        </button>

                                                        <button
                                                            type="button"
                                                            @click="openDeleteShift(shift)"
                                                            title="Hapus Shift"
                                                            aria-label="Hapus Shift"
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
                            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Menampilkan
                                    <span class="font-semibold" x-text="shifts.length === 0 ? 0 : ((dataShiftsPage - 1) * dataShiftsPageSize) + 1"></span>
                                    hingga
                                    <span class="font-semibold" x-text="Math.min(dataShiftsPage * dataShiftsPageSize, shifts.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="shifts.length"></span>
                                    data
                                </div>

                                <div class="flex items-center gap-2" x-show="totalShiftsPages > 1">
                                    <button
                                        @click="changeDataShiftsPage(dataShiftsPage - 1)"
                                        :disabled="dataShiftsPage === 1"
                                        :class="dataShiftsPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getDataShiftsNumber()" :key="page">
                                        <button
                                            @click="changeDataShiftsPage(page)"
                                            :class="page === dataShiftsPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-3 py-1.5 rounded-md border border-gray-300 text-sm font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button
                                        @click="changeDataShiftsPage(dataShiftsPage + 1)"
                                        :disabled="dataShiftsPage === totalShiftsPages"
                                        :class="dataShiftsPage === totalShiftsPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->include('components/admin/modals/shift-modals'); ?>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/main/admin/master-data/shift.js') ?>"></script>
<?= $this->endSection() ?>