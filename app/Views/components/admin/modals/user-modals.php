<!-- delete modal -->
<div x-cloak x-show="openDeleteModal" @click.self="openDeleteModal = false"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center bg-black/40 bg-opacity-50 backdrop-blur-xs z-50"></div>

<div x-cloak x-show="openDeleteModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-md bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Hapus Pengguna</h3>
            <button @click="openDeleteModal = false" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>
        <div class="px-5 py-6 flex flex-col items-center text-center gap-3">
            <div class="h-16 w-16 flex items-center justify-center rounded-full bg-red-50 text-red-600">
                <i class="fas fa-trash-alt text-2xl"></i>
            </div>
            <p class="text-gray-700">
                Apakah Anda yakin ingin menghapus pengguna
                <span class="font-semibold" x-text="selectedUser?.nama_lengkap || 'ini'"></span>?
            </p>
            <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="px-5 py-4 bg-gray-50 flex items-center justify-center gap-4">
            <button @click="openDeleteModal = false"
                class="px-4 py-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-100">
                Batal
            </button>
            <button @click="deleteUser(selectedUser.id)" :disabled="isDeletingUser"
                :class="isDeletingUser ? 'opacity-60 cursor-not-allowed' : ''"
                class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 inline-flex items-center gap-2">
                <i x-show="isDeletingUser" class="fas fa-circle-notch fa-spin"></i>
                <span x-text="isDeletingUser ? 'Menghapus...' : 'Hapus'"></span>
            </button>
        </div>
    </div>
</div>

<!-- detail Modal -->
<div x-cloak x-show="openDetailModal" @click.self="openDetailModal = false"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50" aria-hidden="true"></div>
<div x-cloak x-show="openDetailModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-lg bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                <i class="fas fa-id-card"></i>
                Detail Pengguna
            </h3>
            <button @click="openDetailModal = false" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>

        <div class="px-5 py-5 space-y-4">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                    <i class="fas fa-user text-lg"></i>
                </div>
                <div class="min-w-0">
                    <div class="font-semibold text-gray-800 truncate" x-text="selectedUser?.nama_lengkap || '-'"></div>
                    <div class="text-sm text-gray-500 truncate" x-text="selectedUser?.email || '-'"></div>
                </div>
            </div>

            <div class="h-px bg-gray-100"></div>

            <div class="space-y-3">
                <div class="flex items-start justify-between">
                    <span class="text-sm text-gray-500">Nama Lengkap</span>
                    <span class="text-sm font-medium text-gray-800" x-text="selectedUser?.nama_lengkap || '-'"></span>
                </div>
                <div class="flex items-start justify-between">
                    <span class="text-sm text-gray-500">Email</span>
                    <span class="text-sm font-medium text-gray-800" x-text="selectedUser?.email || '-'"></span>
                </div>
                <div class="flex items-start justify-between">
                    <span class="text-sm text-gray-500">Role</span>
                    <span class="text-sm font-medium text-gray-800 capitalize"
                        x-text="selectedUser?.role_name || '-'"></span>
                </div>
                <div class="flex items-start justify-between" x-show="selectedUser?.status">
                    <span class="text-sm text-gray-500">Status</span>
                    <span class="text-xs px-2 py-1 rounded-full border uppercase" :class="{
                                            'bg-green-50 text-green-700 border-green-200': selectedUser?.status === 'approved',
                                            'bg-yellow-50 text-yellow-700 border-yellow-200': selectedUser?.status === 'pending',
                                            'bg-red-50 text-red-700 border-red-200': selectedUser?.status === 'rejected'
                                        }" x-text="selectedUser?.status || '-'">
                    </span>
                </div>
            </div>
        </div>

        <div class="px-5 py-4 bg-gray-50 flex flex-wrap items-center justify-end gap-2">
            <template x-if="selectedUser?.status === 'pending'">
                <div class="flex items-center gap-2">
                    <button @click="updateStatus(selectedUser.id, 'approved'); openDetailModal = false"
                        :disabled="approvingUserId === selectedUser.id || rejectingUserId === selectedUser.id"
                        :class="(approvingUserId === selectedUser.id || rejectingUserId === selectedUser.id) ? 'opacity-60 cursor-not-allowed' : ''"
                        class="px-3 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 inline-flex items-center gap-2">
                        <i x-show="approvingUserId === selectedUser.id" class="fas fa-circle-notch fa-spin"></i>
                        <span>Setujui</span>
                    </button>
                    <button @click="updateStatus(selectedUser.id, 'rejected'); openDetailModal = false"
                        :disabled="approvingUserId === selectedUser.id || rejectingUserId === selectedUser.id"
                        :class="(approvingUserId === selectedUser.id || rejectingUserId === selectedUser.id) ? 'opacity-60 cursor-not-allowed' : ''"
                        class="px-3 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 inline-flex items-center gap-2">
                        <i x-show="rejectingUserId === selectedUser.id" class="fas fa-circle-notch fa-spin"></i>
                        <span>Tolak</span>
                    </button>
                </div>
            </template>

            <button @click="openDetailModal = false"
                class="px-4 py-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-100">
                Tutup
            </button>
            <button @click="openEdit(selectedUser); openDetailModal = false"
                class="px-4 py-2 rounded-md bg-primary text-white hover:bg-primary/90">
                Edit
            </button>
        </div>
    </div>
</div>

<!-- edit Modal -->
<div x-cloak x-show="openEditModal" @click.self="openEditModal = false"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50" aria-hidden="true"></div>
<div x-cloak x-show="openEditModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
    <form @submit.prevent="updateInfo" class="w-full max-w-xl bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Edit Pengguna</h3>
            <button type="button" @click="openEditModal = false" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>

        <div class="px-5 py-5 grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Masukkan nama lengkap"
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedUser.nama_lengkap" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="email" placeholder="nama@email.com"
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedUser.email" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <div class="relative">
                    <i class="fas fa-user-gear absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <select
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedUser.role_id">
                        <option value="" disabled>Pilih role</option>
                        <template x-for="role in roles" :key="role.id">
                            <option :value="role.id" x-text="role.role_name" class="capitalize"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
            <button type="button" @click="openEditModal = false"
                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                Batal
            </button>
            <button type="submit"
                :disabled="isUpdatingInfo || !(selectedUser.nama_lengkap && selectedUser.nama_lengkap.trim()) || !(selectedUser.email && selectedUser.email.trim()) || !selectedUser.role_id"
                :class="(isUpdatingInfo || !(selectedUser.nama_lengkap && selectedUser.nama_lengkap.trim()) || !(selectedUser.email && selectedUser.email.trim()) || !selectedUser.role_id) ? 'opacity-60 cursor-not-allowed' : ''"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition inline-flex items-center gap-2">
                <i x-show="isUpdatingInfo" class="fas fa-circle-notch fa-spin"></i>
                <span x-text="isUpdatingInfo ? 'Menyimpan...' : 'Simpan'"></span>
            </button>
        </div>
    </form>
</div>

<!-- role delete Modal -->
<div x-cloak x-show="openRoleDeleteModal" @click.self="openRoleDeleteModal = false"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 flex items-center justify-center bg-black/40 bg-opacity-50 backdrop-blur-xs z-50"></div>

<div x-cloak x-show="openRoleDeleteModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="w-full max-w-md bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Hapus Role</h3>
            <button @click="openRoleDeleteModal = false" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>
        <div class="px-5 py-6 flex flex-col items-center text-center gap-3">
            <div class="h-16 w-16 flex items-center justify-center rounded-full bg-red-50 text-red-600">
                <i class="fas fa-trash-alt text-2xl"></i>
            </div>
            <p class="text-gray-700">
                Apakah Anda yakin ingin menghapus role
                <span class="font-semibold" x-text="selectedRole?.role_name || 'ini'"></span>?
            </p>
            <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="px-5 py-4 bg-gray-50 flex items-center justify-center gap-4">
            <button @click="openRoleDeleteModal = false"
                class="px-4 py-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-100">
                Batal
            </button>
            <button @click="deleteRole(selectedRole.id)" :disabled="isDeletingRole"
                :class="isDeletingRole ? 'opacity-60 cursor-not-allowed' : ''"
                class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 inline-flex items-center gap-2">
                <i x-show="isDeletingRole" class="fas fa-circle-notch fa-spin"></i>
                <span x-text="isDeletingRole ? 'Menghapus...' : 'Hapus'"></span>
            </button>
        </div>
    </div>
</div>
</div>

<!-- role add Modal -->
<div x-cloak x-show="openRoleModal" @click.self="openRoleModal = false"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50" aria-hidden="true"></div>
<div x-cloak x-show="openRoleModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
    <form @submit.prevent="addRole" class="w-full max-w-xl bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Tambah Role</h3>
            <button type="button" @click="openRoleModal = false" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>

        <div class="px-5 py-5 grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role</label>
                <div class="relative">
                    <i class="fas fa-user-cog absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Masukkan nama role baru"
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedRole.role_name" required>
                </div>
            </div>
        </div>

        <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
            <button type="button" @click="openRoleModal = false"
                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                Batal
            </button>
            <button type="submit" :disabled="isAddingRole || !(selectedRole.role_name && selectedRole.role_name.trim())"
                :class="(isAddingRole || !(selectedRole.role_name && selectedRole.role_name.trim())) ? 'opacity-60 cursor-not-allowed' : ''"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition inline-flex items-center gap-2">
                <i x-show="isAddingRole" class="fas fa-circle-notch fa-spin"></i>
                <span x-text="isAddingRole ? 'Menyimpan...' : 'Simpan'"></span>
            </button>
        </div>
    </form>
</div>

<!-- role edit Modal -->
<div x-cloak x-show="openRoleEditModal" @click.self="openRoleEditModal = false"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50" aria-hidden="true"></div>
<div x-cloak x-show="openRoleEditModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
    <form @submit.prevent="editRole" class="w-full max-w-xl bg-white rounded-xl shadow-xl overflow-hidden">
        <div class="bg-primary flex items-center justify-between px-5 py-4">
            <h3 class="text-lg font-semibold text-white">Edit Role</h3>
            <button type="button" @click="openRoleEditModal = false" class="p-2 rounded hover:bg-white/10">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>

        <div class="px-5 py-5 grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role</label>
                <div class="relative">
                    <i class="fas fa-user-cog absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Masukkan nama role"
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        x-model="selectedRole.role_name" required>
                </div>
            </div>
        </div>

        <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
            <button type="button" @click="openRoleEditModal = false"
                class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                Batal
            </button>
            <button type="submit"
                :disabled="isEditingRole || !(selectedRole.role_name && selectedRole.role_name.trim())"
                :class="(isEditingRole || !(selectedRole.role_name && selectedRole.role_name.trim())) ? 'opacity-60 cursor-not-allowed' : ''"
                class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:ring-2 focus:ring-primary/30 transition inline-flex items-center gap-2">
                <i x-show="isEditingRole" class="fas fa-circle-notch fa-spin"></i>
                <span x-text="isEditingRole ? 'Menyimpan...' : 'Simpan'"></span>
            </button>
        </div>
    </form>
</div>