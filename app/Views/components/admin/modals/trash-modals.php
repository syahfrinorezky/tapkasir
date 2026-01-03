<div x-cloak x-show="openRestoreModal" @click.self="openRestoreModal = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center bg-black/40 bg-opacity-50 backdrop-blur-xs z-50"></div>

<div
    x-cloak
    x-show="openRestoreModal"
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
            <h3 class="text-lg font-semibold text-white" x-text="'Pulihkan ' + getTrashLabel()"></h3>
            <button @click="openRestoreModal = false" class="p-2 rounded hover:bg-primary/80">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>
        <div class="px-5 py-4 flex flex-col items-center justify-center space-y-3">
            <i class="fas fa-trash-restore text-6xl text-primary mb-2"></i>
            <p class="text-gray-700 text-center">
                Apakah Anda yakin ingin memulihkan
                <span x-show="restoreMode === 'single'"><span x-text="getTrashLabel().toLowerCase()"></span> <span class="font-semibold" x-text="getTrashItemName()"></span>?</span>
                <span x-show="restoreMode === 'multiple'"><span class="font-semibold" x-text="getTrashSelectedCount()"></span> data terpilih?</span>
            </p>
        </div>
        <div class="px-5 py-4 bg-gray-50 flex items-center justify-center gap-4">
            <button @click="openRestoreModal = false" class="px-4 py-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-100">
                Batal
            </button>
            <button @click="processRestore()" :disabled="isRestoring || isRestoringCategory || isRestoringLocation || isRestoringRole || isRestoringShift" class="px-4 py-2 rounded-md bg-primary text-white hover:bg-primary/90 disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-spinner fa-spin" x-show="isRestoring || isRestoringCategory || isRestoringLocation || isRestoringRole || isRestoringShift"></i>
                <span x-text="(isRestoring || isRestoringCategory || isRestoringLocation || isRestoringRole || isRestoringShift) ? 'Memulihkan…' : 'Pulihkan'"></span>
            </button>
        </div>
    </div>
</div>

<div x-cloak x-show="openDeletePermanentModal" @click.self="openDeletePermanentModal = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center bg-black/40 bg-opacity-50 backdrop-blur-xs z-50"></div>

<div
    x-cloak
    x-show="openDeletePermanentModal"
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
            <h3 class="text-lg font-semibold text-white" x-text="'Hapus Permanen ' + getTrashLabel()"></h3>
            <button @click="openDeletePermanentModal = false" class="p-2 rounded hover:bg-primary/80">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>
        <div class="px-5 py-4 flex flex-col items-center justify-center space-y-3">
            <i class="fas fa-exclamation-triangle text-6xl text-red-600 mb-2"></i>
            <p class="text-gray-700 text-center">
                Apakah Anda yakin ingin menghapus permanen
                <span x-show="deletePermanentMode === 'single'"><span x-text="getTrashLabel().toLowerCase()"></span> <span class="font-semibold" x-text="getTrashItemName()"></span>?</span>
                <span x-show="deletePermanentMode === 'multiple'"><span class="font-semibold" x-text="getTrashSelectedCount()"></span> data terpilih?</span>
            </p>
            <p class="text-sm text-red-500 font-semibold">Tindakan ini tidak dapat dibatalkan!</p>
        </div>
        <div class="px-5 py-4 bg-gray-50 flex items-center justify-center gap-4">
            <button @click="openDeletePermanentModal = false" class="px-4 py-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-100">
                Batal
            </button>
            <button @click="processDeletePermanent()" :disabled="isDeletingPermanent || isDeletingPermanentCategory || isDeletingPermanentLocation || isDeletingPermanentRole || isDeletingPermanentShift" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                <i class="fas fa-spinner fa-spin" x-show="isDeletingPermanent || isDeletingPermanentCategory || isDeletingPermanentLocation || isDeletingPermanentRole || isDeletingPermanentShift"></i>
                <span x-text="(isDeletingPermanent || isDeletingPermanentCategory || isDeletingPermanentLocation || isDeletingPermanentRole || isDeletingPermanentShift) ? 'Menghapus…' : 'Hapus Permanen'"></span>
            </button>
        </div>
    </div>
</div>
