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