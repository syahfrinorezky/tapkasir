<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Dashboard Admin
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="">
    <div class="fixed top-0 w-full z-40">
        <?= $this->include('components/admin/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/admin/sidebar'); ?>

        <div class="flex flex-col flex-1 font-secondary overflow-y-auto min-h-screen">
            <div class="flex justify-between items-center pt-22 px-4 pb-4 md:pt-24 md:px-6 md:pb-6 lg:p-8">
                <div class="">
                    <h1 class="text-xl text-primary font-primary font-bold">Manajemen User</h1>
                    <p class="text-gray-600">Halaman untuk mengelola pengguna aplikasi.</p>
                </div>
                <div class="hidden md:block border border-gray-200 rounded-md px-3 py-2">
                    <?= date('l, d M Y'); ?>
                </div>
            </div>

            <hr class="border-gray-200 mx-4 md:mx-6 lg:mx-8">

            <div class="mt-4 md:mt-6 lg:mt-8 px-4 md:px-6 lg:px-8 lg:pb-8">
                <!-- <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-md shadow-md flex items-center space-x-3 p-4 lg:p-6">
                        <div class="flex items-center justify-center aspect-square w-12 rounded-md bg-gradient-to-br from-primary to-accent-2 shadow-secondary shadow-md">
                            <i class="fas fa-shopping-cart text-2xl text-white"></i>
                        </div>
                        <div class="flex flex-col space-y-1">
                            <span class="text-base lg:text-lg font-bold text-primary"><?= $totalUsers ?></span>
                            <span class="text-sm text-gray-500">Users</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-md shadow-md flex items-center space-x-3 p-4 lg:p-6">
                        <div class="flex items-center justify-center aspect-square w-12 rounded-md bg-gradient-to-br from-primary to-accent-2 shadow-secondary shadow-md">
                            <i class="fas fa-shopping-cart text-2xl text-white"></i>
                        </div>
                        <div class="flex flex-col space-y-1">
                            <span class="text-base lg:text-lg font-bold text-primary"><?= $totalUsers ?></span>
                            <span class="text-sm text-gray-500">Users</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-md shadow-md flex items-center space-x-3 p-4 lg:p-6">
                        <div class="flex items-center justify-center aspect-square w-12 rounded-md bg-gradient-to-br from-primary to-accent-2 shadow-secondary shadow-md">
                            <i class="fas fa-shopping-cart text-2xl text-white"></i>
                        </div>
                        <div class="flex flex-col space-y-1">
                            <span class="text-base lg:text-lg font-bold text-primary"><?= $totalUsers ?></span>
                            <span class="text-sm text-gray-500">Users</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-md shadow-md flex items-center space-x-3 p-4 lg:p-6">
                        <div class="flex items-center justify-center aspect-square w-12 rounded-md bg-gradient-to-br from-primary to-accent-2 shadow-secondary shadow-md">
                            <i class="fas fa-shopping-cart text-2xl text-white"></i>
                        </div>
                        <div class="flex flex-col space-y-1">
                            <span class="text-base lg:text-lg font-bold text-primary"><?= $totalUsers ?></span>
                            <span class="text-sm text-gray-500">Users</span>
                        </div>
                    </div>
                </div> -->

                <div class="flex flex-col 2xl:flex-row gap-5">
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
                                            <th class="px-2 md:px-4 py-3 text-left text-xs md:text-sm font-semibold whitespace-nowrap">No</th>
                                            <th class="px-2 md:px-4 py-3 text-left text-xs md:text-sm font-semibold whitespace-nowrap">Nama</th>
                                            <th class="px-2 md:px-4 py-3 text-left text-xs md:text-sm font-semibold whitespace-nowrap hidden sm:table-cell">Email</th>
                                            <th class="px-2 md:px-4 py-3 text-left text-xs md:text-sm font-semibold whitespace-nowrap">Role</th>
                                            <th class="px-2 md:px-4 py-3 text-center text-xs md:text-sm font-semibold whitespace-nowrap">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <?php foreach ($userList as $index => $user) : ?>
                                            <tr>
                                                <td class="px-2 md:px-4 py-3 text-sm text-gray-700 whitespace-nowrap"><?= $index + 1; ?></td>
                                                <td class="px-2 md:px-4 py-3 text-sm text-gray-700 whitespace-nowrap"><?= esc($user['nama_lengkap']); ?></td>
                                                <td class="px-2 md:px-4 py-3 text-sm text-gray-700 whitespace-nowrap hidden sm:table-cell"><?= esc($user['email']); ?></td>
                                                <td class="px-2 md:px-4 py-3 text-sm text-gray-700 whitespace-nowrap"><?= esc(ucfirst($user['role_name'])); ?></td>
                                                <td class="px-2 md:px-4 py-3 text-sm text-gray-700 whitespace-nowrap flex items-center justify-center space-x-2">
                                                    <button type="button">
                                                        <div class="bg-primary hover:bg-shadow-primary p-2 rounded-md">
                                                            <i class="fas fa-info-circle text-white"></i>
                                                        </div>
                                                    </button>
                                                    <button type="button" class="bg-yellow-500 hover:bg-yellow-600 p-2 rounded-md cursor-pointer transition-colors duration-300 ease-in-out">
                                                        <i class="fas fa-edit text-white"></i>
                                                    </button>
                                                    <button type="button" class="bg-red-500 p-2 hover:bg-red-700 rounded-md cursor-pointer transition-colors duration-300 ease-in-out">
                                                        <i class="fas fa-trash-alt text-white"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-2 w-full 2xl:w-1/3">
                        <h1 class="font-bold text-lg text-gray-700">
                            <i class="fas fa-user-clock text-lg text-primary inline-flex mr-1"></i>
                            Daftar Pending
                        </h1>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-max">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th class="px-2 md:px-4 py-3 text-left text-xs md:text-sm font-semibold whitespace-nowrap">No</th>
                                            <th class="px-2 md:px-4 py-3 text-left text-xs md:text-sm font-semibold whitespace-nowrap">Akun</th>
                                            <th class="px-2 md:px-4 py-3 text-center text-xs md:text-sm font-semibold whitespace-nowrap">Status</th>
                                            <th class="px-2 md:px-4 py-3 text-center text-xs md:text-sm font-semibold whitespace-nowrap">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <?php foreach ($pendingUsersList as $index => $pendingUser) : ?>
                                            <tr>
                                                <td class="px-2 md:px-4 py-3 text-sm text-gray-700 whitespace-nowrap"><?= $index + 1; ?></td>
                                                <td class="px-2 md:px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                                    <div class="flex flex-col">
                                                        <span class="font-semibold"><?= esc($pendingUser['nama_lengkap']); ?></span>
                                                        <span class="text-gray-500 text-xs"><?= esc($pendingUser['email']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="px-2 md:px-4 py-3 text-sm text-center whitespace-nowrap">
                                                    <span class="inline-block px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 border border-yellow-300 uppercase">
                                                        <?= esc($pendingUser['status']); ?>
                                                    </span>
                                                </td>
                                                <td class="px-2 md:px-4 py-3 text-sm text-gray-700 flex items-center justify-center space-x-2">
                                                    <button type="button" class="bg-green-500 hover:bg-green-600 p-2 rounded-md cursor-pointer transition-colors duration-300 ease-in-out">
                                                        <i class="fas fa-check text-white"></i>
                                                    </button>
                                                    <button type="button" class="bg-red-500 hover:bg-red-600 p-2 rounded-md cursor-pointer transition-colors duration-300 ease-in-out">
                                                        <i class="fas fa-xmark text-white"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</main>
<?= $this->endSection() ?>