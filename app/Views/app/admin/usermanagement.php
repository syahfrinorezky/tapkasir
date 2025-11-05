<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Manajemen User
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main>
    <div class="fixed top-0 w-full z-40">
        <?= $this->include('components/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/sidebar'); ?>

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
                                        <tr class="hover:bg-gray-50">
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
                                            <tr class="hover:bg-gray-50">
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
                                                    <button @click="updateStatus(user.id, 'approved')"
                                                        :disabled="approvingUserId === user.id || rejectingUserId === user.id"
                                                        :class="approvingUserId === user.id || rejectingUserId === user.id ? 'opacity-60 cursor-not-allowed' : ''"
                                                        class="bg-green-500 hover:bg-green-600 p-2 rounded-md inline-flex items-center justify-center w-9 h-9">
                                                        <i x-show="approvingUserId !== user.id" class="fas fa-check text-white"></i>
                                                        <i x-show="approvingUserId === user.id" class="fas fa-circle-notch fa-spin text-white"></i>
                                                    </button>
                                                    <button @click="updateStatus(user.id, 'rejected')"
                                                        :disabled="approvingUserId === user.id || rejectingUserId === user.id"
                                                        :class="approvingUserId === user.id || rejectingUserId === user.id ? 'opacity-60 cursor-not-allowed' : ''"
                                                        class="bg-red-500 hover:bg-red-600 p-2 rounded-md inline-flex items-center justify-center w-9 h-9">
                                                        <i x-show="rejectingUserId !== user.id" class="fas fa-xmark text-white"></i>
                                                        <i x-show="rejectingUserId === user.id" class="fas fa-circle-notch fa-spin text-white"></i>
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
                                            <tr class="hover:bg-gray-50">
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
                <?= $this->include('components/admin/modals/user-modals'); ?>
            </div>
        </div>

</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/main/admin/usermanagement.js') ?>"></script>
<?= $this->endSection() ?>