<div x-cloak x-show="slideOpen"
    x-transition:enter="transition ease-in-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-30"
    x-transition:leave="transition ease-in-out duration-300"
    x-transition:leave-start="opacity-30"
    x-transition:leave-end="opacity-0"
    class="lg:hidden fixed inset-0 z-50 bg-black/30 backdrop-blur-sm"
    @click="slideOpen = false">
</div>

<div x-cloak x-show="slideOpen"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-300 transform"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed top-0 right-0 w-64 md:w-80 h-full bg-white shadow-lg z-50 flex flex-col justify-between">

    <div class="flex flex-col p-4">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-2">
                <img src="<?= base_url('images/logo/tapkasir.png') ?>" alt="TapKasir" class="w-10 h-10 object-contain">
                <span class="font-primary font-extrabold text-gray-700 text-lg">TapKasir</span>
            </div>
            <button @click="slideOpen = false" class="p-2 hover:bg-gray-200 rounded-md transition-colors duration-300 ease-in-out">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <?php
        $current_uri = uri_string();
        $is_master_active = in_array($current_uri, ['admin/products', 'admin/kategori', 'admin/shifts']);
        ?>

        <li x-data="{ dropDownMaster: <?= $is_master_active ? '1' : 'null' ?> }"
            x-init="dropDownMaster = <?= $is_master_active ? 1 : 'null' ?>"
            class="flex flex-col space-y-1">
            <a href="<?= base_url('admin/dashboard') ?>" class="py-4 px-6 flex items-center space-x-3 hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?= uri_string() == 'admin/dashboard' ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?> ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                <i class="fas fa-home text-primary text-xl"></i>
                <span>Dashboard</span>
            </a>
            <button @click="dropDownMaster === 1 ? dropDownMaster = null : dropDownMaster = 1"
                type="button"
                class="py-4 px-6 flex justify-between items-center hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?= $is_master_active ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-database text-primary text-xl"></i>
                    <span>Master Data</span>
                </div>
                <i x-bind:class="dropDownMaster === 1 ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-gray-400 transition-transform duration-300"></i>
            </button>
            <ul x-cloak x-show="dropDownMaster === 1" x-transition class="pl-12 space-y-1">
                <li>
                   <a href="<?= base_url('admin/products') ?>"
                        class="p-3 flex items-center rounded-md hover:bg-secondary <?= $current_uri == 'admin/products' ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500 hover:text-gray-800' ?>">
                        <i class="fas fa-box mr-2 text-primary"></i>
                        <span class="text-sm">Produk</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('admin/shifts') ?>"
                        class="p-3 flex items-center rounded-md hover:bg-secondary <?= $current_uri == 'admin/shifts' ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500 hover:text-gray-800' ?>">
                        <i class="fas fa-clock mr-2 text-primary"></i>
                        <span class="text-sm">Shift</span>
                    </a>
                </li>
            </ul>
            <a href="<?= base_url('admin/users') ?>" class="py-4 px-6 flex items-center space-x-3 hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?= uri_string() == 'admin/users' ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                <i class="fas fa-users-cog text-primary text-xl"></i>
                <span>Manajemen User</span>
            </a>
            <a href="<?= base_url('admin/transactions') ?>" class="py-4 px-6 flex items-center space-x-3 hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?= uri_string() == 'admin/transactions' ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                <i class="fas fa-exchange-alt text-primary text-xl"></i>
                <span>Transaksi</span>
            </a>
            <a href="<?= base_url('admin/laporan') ?>" class="py-4 px-6 flex items-center space-x-3 hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?= uri_string() == 'admin/laporan' ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                <i class="fas fa-file-alt text-primary text-xl"></i>
                <span>Laporan</span>
            </a>
        </li>
    </div>

    <div class="flex items-center justify-between p-4 gap-2">
        <div class="flex space-x-2 lg:space-x-3 items-center min-w-0 flex-1">
            <div class="flex items-center justify-center aspect-square p-2 lg:p-3 bg-gray-200 rounded-full flex-shrink-0">
                <i class="fas fa-user text-gray-600 text-xs lg:text-sm"></i>
            </div>
            <div class="flex flex-col justify-center min-w-0 flex-1">
                <?php $nama = session()->get('nama_lengkap') ?>
                <span title="<?= esc($nama) ?>" class="font-secondary font-semibold text-xs lg:text-sm text-gray-700 truncate">
                    <?= esc($nama) ?>
                </span>
                <span class="text-gray-400 text-[10px] lg:text-xs capitalize truncate">
                    <?= esc(session()->get('role_name')) ?>
                </span>
            </div>
        </div>
        <div x-data="{ isAccMenuOpen: false }" class="relative flex-shrink-0">
            <button @click="isAccMenuOpen = !isAccMenuOpen" type="button"
                class="flex items-center justify-center w-7 h-7 lg:w-8 lg:h-8 hover:bg-gray-200 rounded-md cursor-pointer transition-colors duration-300 ease-in-out">
                <i class="fas fa-ellipsis text-gray-600 text-xs lg:text-sm"></i>
            </button>

            <div x-show="isAccMenuOpen" @click.away="isAccMenuOpen = false" x-transition
                class="absolute bottom-12 lg:bottom-14 -right-10 w-40 lg:w-48 bg-white shadow-md rounded-md z-50">
                <a href="<?= base_url('logout') ?>"
                    class="flex items-center px-3 lg:px-4 py-2 lg:py-3 hover:bg-gray-100 rounded-md transition-colors duration-300 ease-in-out">
                    <i class="fas fa-sign-out-alt text-red-500 mr-2 text-xs lg:text-sm"></i>
                    <span class="text-gray-500 text-xs lg:text-sm">Logout</span>
                </a>
            </div>
        </div>
    </div>
</div>