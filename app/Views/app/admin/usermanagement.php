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
            x-data="userManagement()" x-init="fetchUsers('approved'); fetchUsers('pending'); fetchRoles()">

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
                        <div class="overflow-x-auto max-h-[70vh] overflow-y-auto">
                            <table class="w-full min-w-max">
                                <thead class="bg-primary text-white sticky top-0 z-10">
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
                                            <td colspan="5" class="text-center py-4 text-gray-500">
                                                <div class="w-full flex flex-col items-center justify-center text-gray-500">
                                                    <video src="<?= base_url('videos/nodata.mp4') ?>" class="w-64 h-36 mb-2" autoplay muted loop></video>
                                                    <span class="text-center">Tidak ada akun pengguna</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    <template x-for="(user, index) in paginatedUsers" :key="user.id">
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-center" x-text="getUserRowNumber(index)"></td>
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
                        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                Menampilkan
                                <span class="font-semibold" x-text="approved.length === 0 ? 0 : ((dataUserPage - 1) * dataUserPageSize) + 1"></span>
                                hingga
                                <span class="font-semibold" x-text="Math.min(dataUserPage * dataUserPageSize, approved.length)"></span>
                                dari
                                <span class="font-semibold" x-text="approved.length"></span>
                                data
                            </div>

                            <div class="flex items-center gap-2" x-show="totalUserPages > 1">
                                <button
                                    @click="changeDataUserPage(dataUserPage - 1)"
                                    :disabled="dataUserPage === 1"
                                    :class="dataUserPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </button>

                                <template x-for="page in getDataUsersNumber()" :key="page">
                                    <button
                                        @click="changeDataUserPage(page)"
                                        :class="page === dataUserPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 text-sm font-medium transition"
                                        x-text="page">
                                    </button>
                                </template>

                                <button
                                    @click="changeDataUserPage(dataUserPage + 1)"
                                    :disabled="dataUserPage === totalUserPages"
                                    :class="dataUserPage === totalUserPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-4 w-full 2xl:w-1/3">
                    <div class="flex flex-col space-y-2 order-2">
                        <template x-if="message">
                            <div x-text="message" class="fixed top-10 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-md"></div>
                        </template>

                        <template x-if="error">
                            <div x-text="error" class="fixed top-10 right-5 bg-red-500 text-white px-4 py-2 rounded shadow-md"></div>
                        </template>

                        <h1 class="font-bold text-lg text-gray-700">
                            <i class="fas fa-user-clock text-lg text-primary inline-flex mr-1"></i>
                            Daftar Pending
                        </h1>

                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="overflow-x-auto max-h-[40vh] overflow-y-auto">
                                <table class="w-full min-w-max">
                                    <thead class="bg-primary text-white sticky top-0 z-10">
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

                                        <template x-for="(user, index) in paginatedPending" :key="user.id">
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
                            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Menampilkan
                                    <span class="font-semibold" x-text="pending.length === 0 ? 0 : ((dataPendingPage - 1) * dataPendingPageSize) + 1"></span>
                                    hingga
                                    <span class="font-semibold" x-text="Math.min(dataPendingPage * dataPendingPageSize, pending.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="pending.length"></span>
                                    data
                                </div>

                                <div class="flex items-center gap-2" x-show="totalPendingPages > 1">
                                    <button
                                        @click="changeDataPendingPage(dataPendingPage - 1)"
                                        :disabled="dataPendingPage === 1"
                                        :class="dataPendingPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getDataPendingNumber()" :key="page">
                                        <button
                                            @click="changeDataPendingPage(page)"
                                            :class="page === dataPendingPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-3 py-1.5 rounded-md border border-gray-300 text-sm font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button
                                        @click="changeDataPendingPage(dataPendingPage + 1)"
                                        :disabled="dataPendingPage === totalPendingPages"
                                        :class="dataPendingPage === totalPendingPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-2 order-1">
                        <template x-if="message">
                            <div x-text="message" class="fixed top-10 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-md"></div>
                        </template>

                        <template x-if="error">
                            <div x-text="error" class="fixed top-10 right-5 bg-red-500 text-white px-4 py-2 rounded shadow-md"></div>
                        </template>

                        <div class="flex justify-between items-center">
                            <h1 class="font-bold text-lg text-gray-700">
                                <i class="fas fa-user-cog text-lg text-primary inline-flex mr-1"></i>
                                Daftar Role
                            </h1>

                            <button @click="openRole()" type="button" class="bg-white hover:bg-gray-200 transition-colors duration-300 ease-in-out p-2 rounded-md flex items-center justify-center cursor-pointer">
                                <i class="fas fa-plus text-primary"></i>
                            </button>
                        </div>

                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="overflow-x-auto max-h-[40vh] overflow-y-auto">
                                <table class="w-full min-w-max">
                                    <thead class="bg-primary text-white sticky top-0 z-10">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-sm font-semibold">Nama Role</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Tanggal Dibuat</th>
                                            <th class="px-4 py-3 text-center text-sm font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <template x-if="roles.length === 0">
                                            <tr>
                                                <td colspan="3" class="py-6">
                                                    <div class="w-full flex flex-col items-center justify-center text-gray-500">
                                                        <video src="<?= base_url('videos/nodata.mp4') ?>" class="w-64 h-36 mb-2" autoplay muted loop></video>
                                                        <span class="text-center">Tidak ada role yang tersedia</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>

                                        <template x-for="(role, index) in paginatedRoles" :key="role.id">
                                            <tr>
                                                <td class="px-4 py-3 text-sm capitalize">
                                                    <span x-text="role.role_name"></span>
                                                </td>
                                                <td class="px-4 py-3 text-sm flex items-center justify-center space-x-2">
                                                    <span class="text-gray-500 text-xs" x-text="new Date(role.created_at).toLocaleDateString()"></span>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <div class="w-full flex flex-row items-center justify-center gap-2">
                                                        <button
                                                            type="button"
                                                            @click="openRoleEdit(role)"
                                                            title="Edit Role"
                                                            aria-label="Edit Role"
                                                            class="w-full md:w-auto flex items-center justify-center p-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition">
                                                            <i class="fas fa-pen"></i>
                                                        </button>

                                                        <button
                                                            type="button"
                                                            @click="openRoleDelete(role)"
                                                            title="Hapus Role"
                                                            aria-label="Hapus Role"
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
                                    <span class="font-semibold" x-text="roles.length === 0 ? 0 : ((dataRolesPage - 1) * dataRolesPageSize) + 1"></span>
                                    hingga
                                    <span class="font-semibold" x-text="Math.min(dataRolesPage * dataRolesPageSize, roles.length)"></span>
                                    dari
                                    <span class="font-semibold" x-text="roles.length"></span>
                                    data
                                </div>

                                <div class="flex items-center gap-2" x-show="totalRolesPages > 1">
                                    <button
                                        @click="changeDataRolesPage(dataRolesPage - 1)"
                                        :disabled="dataRolesPage === 1"
                                        :class="dataRolesPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>

                                    <template x-for="page in getDataRolesNumber()" :key="page">
                                        <button
                                            @click="changeDataRolesPage(page)"
                                            :class="page === dataRolesPage ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                            class="px-3 py-1.5 rounded-md border border-gray-300 text-sm font-medium transition"
                                            x-text="page">
                                        </button>
                                    </template>

                                    <button
                                        @click="changeDataRolesPage(dataRolesPage + 1)"
                                        :disabled="dataRolesPage === totalRolesPages"
                                        :class="dataRolesPage === totalRolesPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
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
                                        <template x-for="role in roles" :key="role.id">
                                            <option :value="role.id" x-text="role.role_name" class="capitalize"></option>
                                        </template>
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

            <!-- role add Modal -->
            <div
                x-cloak
                x-show="openRoleModal"
                @click.self="openRoleModal = false"
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
                x-show="openRoleModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true">
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
                                <input
                                    type="text"
                                    placeholder="Masukkan nama role baru"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                    x-model="selectedRole.role_name"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            @click="openRoleModal = false"
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

            <!-- role edit Modal -->
            <div
                x-cloak
                x-show="openRoleEditModal"
                @click.self="openRoleEditModal = false"
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
                x-show="openRoleEditModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true">
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
                                <input
                                    type="text"
                                    placeholder="Masukkan nama role"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                                    x-model="selectedRole.role_name"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 py-4 bg-gray-50 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            @click="openRoleEditModal = false"
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

</main>

<script>
    function userManagement() {
        return {
            approved: [],
            pending: [],
            roles: [],
            message: '',
            error: '',
            selectedUser: null,
            selectedRole: {
                id: null,
                role_name: ''
            },
            openDetailModal: false,
            openEditModal: false,
            openDeleteModal: false,
            openRoleModal: false,
            openRoleEditModal: false,
            dataUserPage: 1,
            dataUserPageSize: 10,
            dataPendingPage: 1,
            dataPendingPageSize: 5,
            dataRolesPage: 1,
            dataRolesPageSize: 5,

            get paginatedUsers() {
                const start = (this.dataUserPage - 1) * this.dataUserPageSize;
                const end = start + this.dataUserPageSize;
                return this.approved.slice(start, end);
            },

            get totalUserPages() {
                return Math.ceil(this.approved.length / this.dataUserPageSize);
            },

            get paginatedPending() {
                const start = (this.dataPendingPage - 1) * this.dataPendingPageSize;
                const end = start + this.dataPendingPageSize;
                return this.pending.slice(start, end);
            },

            get totalPendingPages() {
                return Math.ceil(this.pending.length / this.dataPendingPageSize);
            },

            get paginatedRoles() {
                const start = (this.dataRolesPage - 1) * this.dataRolesPageSize;
                const end = start + this.dataRolesPageSize;
                return this.roles.slice(start, end);
            },

            get totalRolesPages() {
                return Math.ceil(this.roles.length / this.dataRolesPageSize);
            },

            changeDataUserPage(page) {
                if (page >= 1 && page <= this.totalUserPages) {
                    this.dataUserPage = page;
                }
            },

            changeDataPendingPage(page) {
                if (page >= 1 && page <= this.totalPendingPages) {
                    this.dataPendingPage = page;
                }
            },

            changeDataRolesPage(page) {
                if (page >= 1 && page <= this.totalRolesPages) {
                    this.dataRolesPage = page;
                }
            },

            getDataUsersNumber() {
                const pages = [];
                for (let i = 1; i <= this.totalUserPages; i++) {
                    pages.push(i);
                }
                return pages;
            },

            getDataPendingNumber() {
                const pages = [];
                for (let i = 1; i <= this.totalPendingPages; i++) {
                    pages.push(i);
                }
                return pages;
            },

            getDataRolesNumber() {
                const pages = [];
                for (let i = 1; i <= this.totalRolesPages; i++) {
                    pages.push(i);
                }
                return pages;
            },

            getUserRowNumber(index) {
                return ((this.dataUserPage - 1) * this.dataUserPageSize) + index + 1;
            },

            getPendingRowNumber(index) {
                return ((this.dataPendingPage - 1) * this.dataPendingPageSize) + index + 1;
            },

            getRoleRowNumber(index) {
                return ((this.dataRolesPage - 1) * this.dataRolesPageSize) + index + 1;
            },

            openDetail(user) {
                this.selectedUser = user;
                this.openDetailModal = true;
            },

            async openEdit(user) {
                await this.fetchRoles();


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

            openRole() {
                this.selectedRole = {
                    id: null,
                    role_name: ''
                };
                this.openRoleModal = true;
            },

            openRoleEdit(role) {
                this.selectedRole = {
                    ...role
                };
                this.openRoleEditModal = true;
            },

            async fetchRoles() {
                try {
                    const res = await fetch(`/admin/roles`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    this.roles = data;

                    this.dataRolesPage = 1;
                } catch (e) {
                    this.error = 'Gagal mengambil data role.';
                    setTimeout(() => this.error = '', 3000);
                }
            },

            async addRole() {
                if (this.selectedRole.role_name.trim() === '') {
                    this.error = 'Nama role tidak boleh kosong.';
                    setTimeout(() => this.error = '', 3000);
                    return;

                }

                try {
                    const res = await fetch(`/admin/roles/add`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            role_name: this.selectedRole.role_name
                        })
                    })

                    const data = await res.json();

                    if (res.ok) {
                        this.message = data.message;
                        await this.fetchRoles();
                        this.openRoleModal = false;

                        this.selectedRole = {
                            id: null,
                            role_name: ''
                        };

                        setTimeout(() => this.message = '', 3000);
                    } else {
                        this.error = data.message || 'Gagal menambahkan role.';
                        setTimeout(() => this.error = '', 3000);
                    }
                } catch (error) {
                    this.error = 'Terjadi kesalahan saat menambahkan role.';
                    setTimeout(() => this.error = '', 3000);
                }
            },

            async editRole() {
                try {
                    const res = await fetch(`/admin/roles/edit/${this.selectedRole.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            role_name: this.selectedRole.role_name
                        })
                    })

                    const data = await res.json();

                    if (res.ok) {
                        this.message = data.message;
                        await this.fetchRoles();
                        this.openRoleEditModal = false;
                        setTimeout(() => this.message = '', 3000);
                    } else {
                        this.error = data.message || 'Gagal memperbarui role.';
                        setTimeout(() => this.error = '', 3000);
                    }
                } catch (error) {
                    this.error = 'Terjadi kesalahan saat memperbarui role.';
                    setTimeout(() => this.error = '', 3000);
                }
            },

            async fetchUsers(type) {
                try {
                    const res = await fetch(`/admin/users?type=${type}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (type === 'approved') {
                        this.approved = data;
                        this.dataUserPage = 1;
                    }
                    if (type === 'pending') {
                        this.pending = data;
                        this.dataPendingPage = 1;
                    }
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
            },
        }
    }
</script>

<?= $this->endSection() ?>