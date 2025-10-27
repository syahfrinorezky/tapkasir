<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Manajemen Shift
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main>
    <div class="fixed top-0 w-full z-40">
        <?= $this->include('components/admin/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/admin/sidebar'); ?>

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
                                        <tr>
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
                        <template x-if="message">
                            <div x-text="message" class="fixed top-10 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-md z-50"></div>
                        </template>

                        <template x-if="error">
                            <div x-text="error" class="fixed top-10 right-5 bg-red-500 text-white px-4 py-2 rounded shadow-md z-50"></div>
                        </template>

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
                                            <tr>
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

            <!-- edit shift kasir modal -->
            <div
                x-cloak
                x-show="openEditModal"
                @click.self="openEditModal = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50"
                aria-hidden="true"></div>
            <div
                x-cloak
                x-show="openEditModal"
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
                        <h3 class="text-lg font-semibold text-white">Edit Shift Kasir</h3>
                        <button type="button" @click="openEditModal = false" class="p-2 rounded hover:bg-white/10">
                            <i class="fas fa-times text-white"></i>
                        </button>
                    </div>

                    <div class="px-5 py-5 space-y-4">
                        <div class="space-y-3">
                            <p class="text-sm text-gray-600">Pilih shift baru untuk:</p>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="h-10 w-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-800 truncate" x-text="selectedCashier?.nama_lengkap || '-'"></div>
                                    <div class="text-sm text-gray-500 truncate" x-text="selectedCashier?.email || '-'"></div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Shift</label>
                            <div class="relative">
                                <i class="fas fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <select x-model="selectedShiftId"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
                                    <option value="">Pilih Shift</option>
                                    <template x-for="shift in shifts" :key="shift.id">
                                        <option :value="shift.id" x-text="`${shift.name} (${shift.start_time} - ${shift.end_time})`"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
                        <button @click="openEditModal = false"
                            class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button @click="updateShift()"
                            class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>

            <!-- add shift modal -->
            <div
                x-cloak
                x-show="openAddShiftModal"
                @click.self="openAddShiftModal = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50"
                aria-hidden="true"></div>
            <div
                x-cloak
                x-show="openAddShiftModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true">
                <form @submit.prevent="addShift" class="w-full max-w-xl bg-white rounded-xl shadow-xl overflow-hidden">
                    <div class="bg-primary flex items-center justify-between px-5 py-4">
                        <h3 class="text-lg font-semibold text-white">Tambah Shift</h3>
                        <button type="button" @click="openAddShiftModal = false" class="p-2 rounded hover:bg-white/10">
                            <i class="fas fa-times text-white"></i>
                        </button>
                    </div>

                    <div class="px-5 py-5 grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Shift</label>
                            <div class="relative">
                                <i class="fas fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input
                                    type="text"
                                    placeholder="Masukkan nama shift"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                    x-model="selectedShift.name"
                                    required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                                <div class="relative">
                                    <i class="fas fa-play absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input
                                        type="time"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                        x-model="selectedShift.start_time"
                                        required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                                <div class="relative">
                                    <i class="fas fa-stop absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input
                                        type="time"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                        x-model="selectedShift.end_time"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <div class="relative">
                                <i class="fas fa-circle-check absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <select
                                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                    x-model="selectedShift.status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            @click="openAddShiftModal = false"
                            class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

            <!-- edit shift modal -->
            <div
                x-cloak
                x-show="openEditShiftModal"
                @click.self="openEditShiftModal = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50"
                aria-hidden="true"></div>
            <div
                x-cloak
                x-show="openEditShiftModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true">
                <form @submit.prevent="editShift" class="w-full max-w-xl bg-white rounded-xl shadow-xl overflow-hidden">
                    <div class="bg-primary flex items-center justify-between px-5 py-4">
                        <h3 class="text-lg font-semibold text-white">Edit Shift</h3>
                        <button type="button" @click="openEditShiftModal = false" class="p-2 rounded hover:bg-white/10">
                            <i class="fas fa-times text-white"></i>
                        </button>
                    </div>

                    <div class="px-5 py-5 grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Shift</label>
                            <div class="relative">
                                <i class="fas fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input
                                    type="text"
                                    placeholder="Masukkan nama shift"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                    x-model="selectedShift.name"
                                    required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                                <div class="relative">
                                    <i class="fas fa-play absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input
                                        type="time"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                        x-model="selectedShift.start_time"
                                        required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                                <div class="relative">
                                    <i class="fas fa-stop absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input
                                        type="time"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                        x-model="selectedShift.end_time"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <div class="relative">
                                <i class="fas fa-circle-check absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <select
                                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                    x-model="selectedShift.status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            @click="openEditShiftModal = false"
                            class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

            <!-- delete shift modal -->
            <div
                x-cloak
                x-show="openDeleteShiftModal"
                @click.self="openDeleteShiftModal = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50"
                aria-hidden="true"></div>
            <div
                x-cloak
                x-show="openDeleteShiftModal"
                @click.self="openDeleteShiftModal = false"
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
                        <h3 class="text-lg font-semibold text-white">Hapus Shift</h3>
                        <button @click="openDeleteShiftModal = false" class="p-2 rounded hover:bg-white/10" aria-label="Tutup">
                            <i class="fas fa-times text-white"></i>
                        </button>
                    </div>

                    <div class="px-5 py-6 flex flex-col items-center text-center gap-3">
                        <div class="h-16 w-16 flex items-center justify-center rounded-full bg-red-50 text-red-600">
                            <i class="fas fa-trash-alt text-2xl"></i>
                        </div>

                        <p class="text-gray-700">
                            Apakah Anda yakin ingin menghapus shift
                            <span class="font-semibold" x-text="selectedShift?.name || 'ini'"></span>?
                        </p>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>

                    <div class="px-5 py-4 bg-gray-50 flex items-center justify-center gap-4">
                        <button
                            @click="openDeleteShiftModal = false"
                            class="px-4 py-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-100">
                            Batal
                        </button>
                        <button
                            @click="deleteShift(selectedShift.id)"
                            class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function shiftManagement() {
        return {
            cashiers: [],
            shifts: [],
            selectedCashier: null,
            selectedShift: {
                id: null,
                name: '',
                start_time: '',
                end_time: '',
                status: 'active'
            },
            selectedShiftId: null,
            message: '',
            error: '',
            openEditModal: false,
            openAddShiftModal: false,
            openEditShiftModal: false,
            openDeleteShiftModal: false,
            dataCashierPage: 1,
            dataCashierPageSize: 10,
            dataShiftsPage: 1,
            dataShiftsPageSize: 5,

            get paginatedCashiers() {
                const start = (this.dataCashierPage - 1) * this.dataCashierPageSize;
                const end = start + this.dataCashierPageSize;
                return this.cashiers.slice(start, end);
            },

            get totalCashierPages() {
                return Math.ceil(this.cashiers.length / this.dataCashierPageSize);
            },

            get paginatedShifts() {
                const start = (this.dataShiftsPage - 1) * this.dataShiftsPageSize;
                const end = start + this.dataShiftsPageSize;
                return this.shifts.slice(start, end);
            },

            get totalShiftsPages() {
                return Math.ceil(this.shifts.length / this.dataShiftsPageSize);
            },

            changeDataCashierPage(page) {
                if (page >= 1 && page <= this.totalCashierPages) {
                    this.dataCashierPage = page;
                }
            },

            changeDataShiftsPage(page) {
                if (page >= 1 && page <= this.totalShiftsPages) {
                    this.dataShiftsPage = page;
                }
            },

            getDataCashiersNumber() {
                const pages = [];
                for (let i = 1; i <= this.totalCashierPages; i++) {
                    pages.push(i);
                }
                return pages;
            },

            getDataShiftsNumber() {
                const pages = [];
                for (let i = 1; i <= this.totalShiftsPages; i++) {
                    pages.push(i);
                }
                return pages;
            },

            getCashierRowNumber(index) {
                return ((this.dataCashierPage - 1) * this.dataCashierPageSize) + index + 1;
            },

            getShiftRowNumber(index) {
                return ((this.dataShiftsPage - 1) * this.dataShiftsPageSize) + index + 1;
            },

            async fetchData() {
                try {
                    const res = await fetch(`/admin/shifts/data`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    this.cashiers = data.cashiers || [];
                    this.shifts = data.shifts || [];
                } catch (e) {
                    console.error(e);
                    this.error = 'Gagal memuat data shift & kasir.';
                    setTimeout(() => this.error = '', 3000);
                }
            },

            openEditCashier(cashier) {
                this.selectedCashier = cashier;
                this.selectedShiftId = cashier.shift_id || '';
                this.openEditModal = true;
            },

            openAddShift() {
                this.selectedShift = {
                    id: null,
                    name: '',
                    start_time: '',
                    end_time: '',
                    status: 'active'
                };
                this.openAddShiftModal = true;
            },

            openEditShift(shift) {
                this.selectedShift = {
                    ...shift
                };
                this.openEditShiftModal = true;
            },

            openDeleteShift(shift) {
                this.selectedShift = shift;
                this.openDeleteShiftModal = true;
            },

            async updateShift() {
                try {
                    const res = await fetch(`/admin/shifts/updateCashierShift/${this.selectedCashier.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `shift_id=${this.selectedShiftId}`
                    });
                    const data = await res.json();

                    if (res.ok) {
                        this.message = data.message;
                        this.openEditModal = false;
                        await this.fetchData();
                        setTimeout(() => this.message = '', 3000);
                    } else {
                        this.openEditModal = false;
                        this.error = data.message || 'Gagal memperbarui shift.';
                        setTimeout(() => this.error = '', 3000);
                    }
                } catch (e) {
                    this.error = 'Kesalahan koneksi.';
                    setTimeout(() => this.error = '', 3000);
                }
            },

            async addShift() {
                try {
                    const res = await fetch(`/admin/shifts/add`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(this.selectedShift)
                    });

                    const data = await res.json();

                    if (res.ok) {
                        this.message = data.message;
                        await this.fetchData();
                        this.openAddShiftModal = false;
                        setTimeout(() => this.message = '', 3000);
                    } else {
                        this.openAddShiftModal = false;
                        this.error = data.message || 'Gagal menambahkan shift.';
                        setTimeout(() => this.error = '', 3000);
                    }
                } catch (error) {
                    this.error = 'Terjadi kesalahan saat menambahkan shift.';
                    setTimeout(() => this.error = '', 3000);
                }
            },

            async editShift() {
                try {
                    const res = await fetch(`/admin/shifts/edit/${this.selectedShift.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(this.selectedShift)
                    });

                    const data = await res.json();

                    if (res.ok) {
                        this.message = data.message;
                        await this.fetchData();
                        this.openEditShiftModal = false;
                        setTimeout(() => this.message = '', 3000);
                    } else {
                        this.openEditShiftModal = false;
                        this.error = data.message || 'Gagal memperbarui shift.';
                        setTimeout(() => this.error = '', 3000);
                    }
                } catch (error) {
                    this.error = 'Terjadi kesalahan saat memperbarui shift.';
                    setTimeout(() => this.error = '', 3000);
                }
            },

            async deleteShift(id) {
                try {
                    const res = await fetch(`/admin/shifts/deleteShift/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();

                    if (res.ok) {
                        this.message = data.message;
                        await this.fetchData();
                        this.openDeleteShiftModal = false;
                        setTimeout(() => this.message = '', 3000);
                    } else {
                        this.openDeleteShiftModal = false;
                        this.error = data.message || 'Gagal menghapus shift.';
                        setTimeout(() => this.error = '', 3000);
                    }
                } catch (error) {
                    this.error = 'Terjadi kesalahan saat menghapus shift.';
                    setTimeout(() => this.error = '', 3000);
                }
            }
        }
    }
</script>

<?= $this->endSection() ?>