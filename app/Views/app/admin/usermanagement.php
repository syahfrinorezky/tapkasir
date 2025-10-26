<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Manajemen User
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main>
    <div class="fixed top-0 w-full z-40">
        <?= $this->include('components/admin/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/admin/sidebar'); ?>

        <div class="flex flex-col flex-1 font-secondary overflow-y-auto min-h-screen"
            x-data="userManagement()" x-init="fetchUsers('approved'); fetchUsers('pending')">

            <div class="flex justify-between items-center pt-22 px-4 pb-4 md:pt-24 md:px-6 md:pb-6 lg:p-8">
                <div>
                    <h1 class="text-xl text-primary font-primary font-bold">Manajemen User</h1>
                    <p class="text-gray-600">Halaman untuk mengelola pengguna aplikasi.</p>
                </div>
                <div class="hidden md:block border border-gray-200 rounded-md px-3 py-2">
                    <?= date('l, d M Y'); ?>
                </div>
            </div>

            <hr class="border-gray-200 mx-4 md:mx-6 lg:mx-8">

            <div class="mt-4 md:mt-6 lg:mt-8 px-4 md:px-6 lg:px-8 lg:pb-8 flex flex-col 2xl:flex-row gap-5">
                <div class="flex flex-col space-y-2 w-full lg:flex-1">
                    <h1 class="font-bold text-lg text-gray-700">
                        <i class="fas fa-users text-lg text-primary inline-flex mr-1"></i>
                        Daftar Pengguna
                    </h1>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-max">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">No</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Nama</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Email</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Role</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-if="approved.length === 0">
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-gray-500">
                                                <div class="w-full flex flex-col items-center justify-center text-gray-500">
                                                    <video src="<?= base_url('videos/nodata.mp4') ?>" class="w-64 h-36 mb-2" autoplay muted loop></video>
                                                    <span class="text-center">Tidak ada akun yang pending</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    <template x-for="user in approved" :key="user.id">
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-center" x-text="approved.indexOf(user) + 1"></td>
                                            <td class="px-4 py-3 text-sm " x-text="user.nama_lengkap"></td>
                                            <td class="px-4 py-3 text-sm text-center" x-text="user.email"></td>
                                            <td class="px-4 py-3 text-sm text-center capitalize" x-text="user.role_name"></td>
                                            <td class="px-4 py-3 text-sm space-x-2 flex items-center justify-center">
                                                <button type="button" @click="openDetail(user)" class="flex items-center justify-center p-2 bg-primary hover:bg-primary/80 text-white rounded-md transition-colors duration-300 ease-in-out cursor-pointer">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                                <button type="button" @click="openEdit(user)" class="flex items-center justify-center p-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition-colors duration-300 ease-in-out cursor-pointer">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <button type="button" @click="openDelete(user)" class="flex items-center justify-center p-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors duration-300 ease-in-out cursor-pointer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col space-y-2 w-full 2xl:w-1/3">
                    <template x-if="message">
                        <div x-text="message" class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-md"></div>
                    </template>

                    <template x-if="error">
                        <div x-text="error" class="fixed top-5 right-5 bg-red-500 text-white px-4 py-2 rounded shadow-md"></div>
                    </template>

                    <h1 class="font-bold text-lg text-gray-700">
                        <i class="fas fa-user-clock text-lg text-primary inline-flex mr-1"></i>
                        Daftar Pending
                    </h1>

                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-max">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-sm font-semibold">Nama</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Status</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-if="pending.length === 0">
                                        <tr>
                                            <td colspan="3" class="py-6">
                                                <div class="w-full flex flex-col items-center justify-center text-gray-500">
                                                    <video src="<?= base_url('videos/nodata.mp4') ?>" class="w-64 h-36 mb-2" autoplay muted loop></video>
                                                    <span class="text-center">Tidak ada akun yang pending</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    <template x-for="user in pending" :key="user.id">
                                        <tr>
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex flex-col">
                                                    <span class="font-semibold" x-text="user.nama_lengkap"></span>
                                                    <span class="text-gray-500 text-xs" x-text="user.email"></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-center">
                                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 border border-yellow-300 uppercase" x-text="user.status"></span>
                                            </td>
                                            <td class="px-4 py-3 text-sm flex items-center justify-center space-x-2">
                                                <button @click="updateStatus(user.id, 'approved')" class="bg-green-500 hover:bg-green-600 p-2 rounded-md">
                                                    <i class="fas fa-check text-white"></i>
                                                </button>
                                                <button @click="updateStatus(user.id, 'rejected')" class="bg-red-500 hover:bg-red-600 p-2 rounded-md">
                                                    <i class="fas fa-xmark text-white"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- delete modal -->
                <div x-cloak x-show="openDeleteModal" @click.self="openDeleteModal = false"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 flex items-center justify-center bg-black/40 bg-opacity-50 backdrop-blur-xs z-50"></div>

                <div
                    x-cloak
                    x-show="openDeleteModal"
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
                            <h3 class="text-lg font-semibold text-white">Hapus Pengguna</h3>
                            <button @click="openDeleteModal = false" class="p-2 rounded hover:bg-gray-100">
                                <i class="fas fa-times text-white"></i>
                            </button>
                        </div>
                        <div class="px-5 py-4 flex flex-col items-center justify-center space-y-3">
                            <img src="<?= base_url('images/illustration/deletemodal-illustration.png') ?>" alt="Delete Illustration" class="w-1/2 mb-2">
                            <p class="text-gray-700 text-center">
                                Apakah Anda yakin ingin menghapus pengguna
                                <span class="font-semibold" x-text="selectedUser?.nama_lengkap || 'ini'"></span>?
                            </p>
                            <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                        <div class="px-5 py-4 bg-gray-50 flex items-center justify-center gap-4">
                            <button @click="openDeleteModal = false" class="px-4 py-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-100">
                                Batal
                            </button>
                            <button @click="deleteUser(selectedUser.id)" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>

                <!-- detail Modal -->
                <div
                    x-cloak
                    x-show="openDetailModal"
                    @click.self="openDetailModal = false"
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
                    x-show="openDetailModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    role="dialog"
                    aria-modal="true">
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
                                    <span class="text-sm font-medium text-gray-800 capitalize" x-text="selectedUser?.role_name || '-'"></span>
                                </div>
                                <div class="flex items-start justify-between" x-show="selectedUser?.status">
                                    <span class="text-sm text-gray-500">Status</span>
                                    <span class="text-xs px-2 py-1 rounded-full border uppercase"
                                        :class="{
                                            'bg-green-50 text-green-700 border-green-200': selectedUser?.status === 'approved',
                                            'bg-yellow-50 text-yellow-700 border-yellow-200': selectedUser?.status === 'pending',
                                            'bg-red-50 text-red-700 border-red-200': selectedUser?.status === 'rejected'
                                        }"
                                        x-text="selectedUser?.status || '-'">
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="px-5 py-4 bg-gray-50 flex flex-wrap items-center justify-end gap-2">
                            <template x-if="selectedUser?.status === 'pending'">
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="updateStatus(selectedUser.id, 'approved'); openDetailModal = false"
                                        class="px-3 py-2 rounded-md bg-green-600 text-white hover:bg-green-700">
                                        Setujui
                                    </button>
                                    <button
                                        @click="updateStatus(selectedUser.id, 'rejected'); openDetailModal = false"
                                        class="px-3 py-2 rounded-md bg-red-600 text-white hover:bg-red-700">
                                        Tolak
                                    </button>
                                </div>
                            </template>

                            <button @click="openDetailModal = false" class="px-4 py-2 rounded-md border border-gray-200 text-gray-700 hover:bg-gray-100">
                                Tutup
                            </button>
                            <button @click="openEdit(selectedUser); openDetailModal = false" class="px-4 py-2 rounded-md bg-primary text-white hover:bg-primary/90">
                                Edit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- edit Modal -->
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
                                    <input
                                        type="text"
                                        placeholder="Masukkan nama lengkap"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                        x-model="selectedUser.nama_lengkap"
                                        required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input
                                        type="email"
                                        placeholder="nama@email.com"
                                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                        x-model="selectedUser.email"
                                        required>
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
                                        <option :value="1">Admin</option>
                                        <option :value="2">Kasir</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                @click="openEditModal = false"
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
            </div>
        </div>
    </div>

</main>

<script>
    function userManagement() {
        return {
            approved: [],
            pending: [],
            message: '',
            error: '',
            selectedUser: null,
            openDetailModal: false,
            openEditModal: false,
            openDeleteModal: false,

            openDetail(user) {
                this.selectedUser = user;
                this.openDetailModal = true;
            },

            openEdit(user) {
                this.selectedUser = {
                    ...user,
                    role_id: user.role_id
                };
                this.openEditModal = true;
            },

            openDelete(user) {
                this.selectedUser = user;
                this.openDeleteModal = true;
            },

            async fetchUsers(type) {
                try {
                    const res = await fetch(`/admin/users?type=${type}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (type === 'approved') this.approved = data;
                    if (type === 'pending') this.pending = data;
                } catch (e) {
                    this.error = 'Gagal mengambil data pengguna.';
                    setTimeout(() => this.error = '', 3000);
                }
            },

            async updateStatus(id, status) {
                try {
                    const res = await fetch(`/admin/users/updateStatus/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            status
                        })
                    });
                    const data = await res.json();

                    if (res.ok) {
                        this.message = data.message;
                        this.fetchUsers('pending');
                        this.fetchUsers('approved');
                        setTimeout(() => this.message = '', 3000);
                    } else {
                        this.error = data.message || 'Gagal memperbarui status.';
                        setTimeout(() => this.error = '', 3000);
                    }
                } catch (err) {
                    this.error = 'Terjadi kesalahan server.';
                    setTimeout(() => this.error = '', 3000);
                }
            },

            async updateInfo() {
                try {
                    const res = await fetch(`/admin/users/updateInfo/${this.selectedUser.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            nama_lengkap: this.selectedUser.nama_lengkap,
                            email: this.selectedUser.email,
                            role_id: this.selectedUser.role_id
                        })
                    });
                    const data = await res.json();

                    if (res.ok) {
                        this.message = data.message;
                        this.fetchUsers('approved');
                        this.openEditModal = false;
                        setTimeout(() => this.message = '', 3000);
                    } else {
                        this.error = data.message || 'Gagal memperbarui informasi.';
                        setTimeout(() => this.error = '', 3000);
                    }
                } catch (error) {
                    this.error = 'Terjadi kesalahan server.';
                    setTimeout(() => this.error = '', 3000);
                }
            },

            async deleteUser(id) {
                const res = await fetch(`/admin/users/delete/${id}`, {
                    method: 'DELETE'
                });
                const data = await res.json();
                if (res.ok) {
                    this.message = data.message;
                    this.fetchUsers('approved');
                    this.openDeleteModal = false;
                    setTimeout(() => this.message = '', 3000);
                } else {
                    this.error = data.message || 'Gagal menghapus user.';
                    setTimeout(() => this.error = '', 3000);
                }
            }
        }
    }
</script>

<?= $this->endSection() ?>