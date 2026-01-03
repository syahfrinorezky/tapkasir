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

            <div class="mt-4 md:mt-6 lg:mt-8 px-4 md:px-6 lg:px-8 pb-20 flex flex-col 2xl:flex-row gap-5">
                <div class="flex flex-col space-y-2 w-full lg:flex-1">
                    <div class="flex justify-between items-center">
                        <h1 class="font-bold text-lg text-gray-700" x-text="showTrashCashiers ? 'Sampah Kasir' : 'Daftar Kasir'"></h1>
                        <div class="flex items-center gap-2">
                            <button @click="toggleTrashCashiers()" type="button"
                                :class="showTrashCashiers ? 'bg-primary text-white hover:bg-primary/90' : 'bg-white text-primary hover:bg-gray-200'"
                                class="transition-colors duration-300 ease-in-out p-2 rounded-md flex items-center justify-center cursor-pointer"
                                title="Sampah / Restore">
                                <i class="fas fa-trash-restore"></i>
                            </button>
                        </div>
                    </div>

                    <div x-show="showTrashCashiers" x-cloak
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-red-50">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" 
                                    @change="toggleSelectAllTrashCashiers($event)"
                                    class="rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="text-sm text-gray-600" x-text="selectedTrashCashiers.length + ' dipilih'"></span>
                            </div>
                            <div class="flex gap-2" x-show="selectedTrashCashiers.length > 0">
                                <button @click="restoreSelectedCashiers()" class="px-3 py-1.5 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                                    <i class="fas fa-undo mr-1"></i> Pulihkan
                                </button>
                                <button @click="deletePermanentSelectedCashiers()" class="px-3 py-1.5 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                    <i class="fas fa-trash-alt mr-1"></i> Hapus Permanen
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
                            <table class="w-full min-w-max">
                                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                                    <tr>
                                        <th class="w-10 px-4 py-3 text-center"></th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Nama</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Dihapus Pada</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-if="trashCashiers.length === 0">
                                        <tr>
                                            <td colspan="4" class="py-8 text-center text-gray-500">
                                                <i class="fas fa-trash-alt text-4xl mb-2 text-gray-300"></i>
                                                <p>Sampah kosong</p>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="cashier in paginatedTrashCashiers" :key="cashier.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-center">
                                                <input type="checkbox" 
                                                    :value="cashier.id" 
                                                    x-model="selectedTrashCashiers"
                                                    class="rounded border-gray-300 text-primary focus:ring-primary">
                                            </td>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900" x-text="cashier.nama_lengkap"></td>
                                            <td class="px-4 py-3 text-sm text-center text-gray-500" x-text="formatDateTime(cashier.deleted_at)"></td>
                                            <td class="px-4 py-3 text-sm text-center">
                                                <div class="flex justify-center gap-2">
                                                    <button @click="confirmRestoreCashier(cashier.id)" class="text-green-600 hover:text-green-800" title="Pulihkan">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                    <button @click="confirmDeletePermanentCashier(cashier.id)" class="text-red-600 hover:text-red-800" title="Hapus Permanen">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 py-2 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-2">
                            <div class="text-xs text-gray-600">
                                <span class="font-semibold" x-text="trashCashiers.length === 0 ? 0 : ((trashCashiersPage - 1) * trashCashiersPageSize) + 1"></span>
                                -
                                <span class="font-semibold" x-text="Math.min(trashCashiersPage * trashCashiersPageSize, trashCashiers.length)"></span>
                                dari
                                <span class="font-semibold" x-text="trashCashiers.length"></span>
                            </div>

                            <div class="flex items-center gap-1" x-show="totalTrashCashiersPages > 1">
                                <button
                                    @click="changeTrashCashiersPage(trashCashiersPage - 1)"
                                    :disabled="trashCashiersPage === 1"
                                    :class="trashCashiersPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </button>

                                <template x-for="page in getTrashCashiersPageNumbers()" :key="page">
                                    <button
                                        @click="changeTrashCashiersPage(page)"
                                        :class="page === trashCashiersPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 text-xs font-medium transition"
                                        x-text="page">
                                    </button>
                                </template>

                                <button
                                    @click="changeTrashCashiersPage(trashCashiersPage + 1)"
                                    :disabled="trashCashiersPage === totalTrashCashiersPages"
                                    :class="trashCashiersPage === totalTrashCashiersPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div x-show="!showTrashCashiers" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        class="bg-white rounded-lg shadow-md overflow-hidden">
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
                                                    <img src="<?= base_url('images/illustration/nodata.png') ?>" class="w-32 md:w-48 lg:w-64 h-auto mb-2 object-contain" alt="No Data">
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
                        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-4">
                            <div class="text-xs sm:text-sm text-gray-600">
                                <span class="hidden sm:inline">
                                    Menampilkan
                                    <span class="font-semibold" x-text="cashiers.length === 0 ? 0 : ((dataCashierPage - 1) * dataCashierPageSize) + 1"></span>
                                    hingga
                                    <span class="font-semibold" x-text="Math.min(dataCashierPage * dataCashierPageSize, cashiers.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="cashiers.length"></span>
                                    kasir
                                </span>
                                <span class="sm:hidden">
                                    <span class="font-semibold" x-text="cashiers.length === 0 ? 0 : ((dataCashierPage - 1) * dataCashierPageSize) + 1"></span>
                                    -
                                    <span class="font-semibold" x-text="Math.min(dataCashierPage * dataCashierPageSize, cashiers.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="cashiers.length"></span>
                                </span>
                            </div>

                            <div class="flex items-center gap-2" x-show="totalCashierPages > 1">
                                <button
                                    @click="changeDataCashierPage(dataCashierPage - 1)"
                                    :disabled="dataCashierPage === 1"
                                    :class="dataCashierPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 bg-white text-xs sm:text-sm font-medium text-gray-700 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </button>

                                <template x-for="page in getDataCashiersNumber()" :key="page">
                                    <button
                                        @click="changeDataCashierPage(page)"
                                        :class="page === dataCashierPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                        class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 text-xs sm:text-sm font-medium transition"
                                        x-text="page">
                                    </button>
                                </template>

                                <button
                                    @click="changeDataCashierPage(dataCashierPage + 1)"
                                    :disabled="dataCashierPage === totalCashierPages"
                                    :class="dataCashierPage === totalCashierPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-md border border-gray-300 bg-white text-xs sm:text-sm font-medium text-gray-700 transition">
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
                            <h1 class="font-bold text-lg text-gray-700" x-text="showTrashShifts ? 'Sampah Shift' : 'Daftar Shift'"></h1>

                            <div class="flex items-center gap-2">
                                <button @click="toggleTrashShifts()" type="button"
                                    :class="showTrashShifts ? 'bg-primary text-white hover:bg-primary/90' : 'bg-white text-primary hover:bg-gray-200'"
                                    class="transition-colors duration-300 ease-in-out p-2 rounded-md flex items-center justify-center cursor-pointer"
                                    title="Sampah / Restore">
                                    <i class="fas fa-trash-restore"></i>
                                </button>
                                <button x-show="!showTrashShifts" @click="openAddShift()" type="button"
                                    class="bg-white hover:bg-gray-200 transition-colors duration-300 ease-in-out p-2 rounded-md flex items-center justify-center cursor-pointer">
                                    <i class="fas fa-plus text-primary"></i>
                                </button>
                            </div>
                        </div>

                        <div x-show="showTrashShifts" x-cloak
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform translate-x-4"
                            x-transition:enter-end="opacity-100 transform translate-x-0"
                            class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-red-50">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" 
                                        @change="toggleSelectAllTrashShifts($event)"
                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                    <span class="text-sm text-gray-600" x-text="selectedTrashShifts.length + ' dipilih'"></span>
                                </div>
                                <div class="flex gap-2" x-show="selectedTrashShifts.length > 0">
                                    <button @click="restoreSelectedShifts()" class="px-3 py-1.5 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                                        <i class="fas fa-undo mr-1"></i> Pulihkan
                                    </button>
                                    <button @click="deletePermanentSelectedShifts()" class="px-3 py-1.5 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                                        <i class="fas fa-trash-alt mr-1"></i> Hapus Permanen
                                    </button>
                                </div>
                            </div>
                            <div class="overflow-x-auto max-h-[40vh] overflow-y-auto">
                                <table class="w-full min-w-max">
                                    <thead class="bg-gray-100 text-gray-700 sticky top-0 z-10">
                                        <tr>
                                            <th class="w-10 px-4 py-3 text-center"></th>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Nama Shift</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Dihapus Pada</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-if="trashShifts.length === 0">
                                            <tr>
                                                <td colspan="4" class="py-8 text-center text-gray-500">
                                                    <i class="fas fa-trash-alt text-4xl mb-2 text-gray-300"></i>
                                                    <p>Sampah kosong</p>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-for="shift in paginatedTrashShifts" :key="shift.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-center">
                                                    <input type="checkbox" 
                                                        :value="shift.id" 
                                                        x-model="selectedTrashShifts"
                                                        class="rounded border-gray-300 text-primary focus:ring-primary">
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900" x-text="shift.name"></td>
                                                <td class="px-4 py-3 text-sm text-center text-gray-500" x-text="formatDateTime(shift.deleted_at)"></td>
                                                <td class="px-4 py-3 text-sm text-center">
                                                    <div class="flex justify-center gap-2">
                                                        <button @click="confirmRestoreShift(shift.id)" class="text-green-600 hover:text-green-800" title="Pulihkan">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                        <button @click="confirmDeletePermanentShift(shift.id)" class="text-red-600 hover:text-red-800" title="Hapus Permanen">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-4 py-2 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-2">
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold" x-text="trashShifts.length === 0 ? 0 : ((trashShiftsPage - 1) * trashShiftsPageSize) + 1"></span>
                                    -
                                    <span class="font-semibold" x-text="Math.min(trashShiftsPage * trashShiftsPageSize, trashShifts.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="trashShifts.length"></span>
                                </div>

                                <div class="flex items-center gap-1" x-show="totalTrashShiftsPages > 1">
                                    <button
                                        @click="changeTrashShiftsPage(trashShiftsPage - 1)"
                                        :disabled="trashShiftsPage === 1"
                                        :class="trashShiftsPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getTrashShiftsPageNumbers()" :key="page">
                                        <button
                                            @click="changeTrashShiftsPage(page)"
                                            :class="page === trashShiftsPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-2.5 py-1.5 rounded border border-gray-300 text-xs font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button
                                        @click="changeTrashShiftsPage(trashShiftsPage + 1)"
                                        :disabled="trashShiftsPage === totalTrashShiftsPages"
                                        :class="trashShiftsPage === totalTrashShiftsPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div x-show="!showTrashShifts" 
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform -translate-x-4"
                            x-transition:enter-end="opacity-100 transform translate-x-0"
                            class="bg-white rounded-lg shadow-md overflow-hidden">
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
                                                        <img src="<?= base_url('images/illustration/nodata.png') ?>" class="w-32 md:w-48 lg:w-64 h-auto mb-2 object-contain" alt="No Data">
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
                            <div class="px-4 py-2 border-t border-gray-200 bg-gray-50 flex items-center justify-between gap-2">
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold" x-text="shifts.length === 0 ? 0 : ((dataShiftsPage - 1) * dataShiftsPageSize) + 1"></span>
                                    -
                                    <span class="font-semibold" x-text="Math.min(dataShiftsPage * dataShiftsPageSize, shifts.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="shifts.length"></span>
                                </div>

                                <div class="flex items-center gap-1" x-show="totalShiftsPages > 1">
                                    <button
                                        @click="changeDataShiftsPage(dataShiftsPage - 1)"
                                        :disabled="dataShiftsPage === 1"
                                        :class="dataShiftsPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getDataShiftsNumber()" :key="page">
                                        <button
                                            @click="changeDataShiftsPage(page)"
                                            :class="page === dataShiftsPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-2.5 py-1.5 rounded border border-gray-300 text-xs font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button
                                        @click="changeDataShiftsPage(dataShiftsPage + 1)"
                                        :disabled="dataShiftsPage === totalShiftsPages"
                                        :class="dataShiftsPage === totalShiftsPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-2.5 py-1.5 rounded border border-gray-300 bg-white text-xs font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->include('components/admin/modals/shift-modals'); ?>
            <?= $this->include('components/admin/modals/trash-modals'); ?>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/main/admin/master-data/shift.js') ?>"></script>
<?= $this->endSection() ?>