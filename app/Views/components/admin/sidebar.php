<div class="hidden lg:flex lg:w-75 bg-white shadow-md h-screen sticky top-0 flex-shrink-0">
    <div class="w-full flex flex-col justify-between p-4">
        <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center justify-center space-x-2 py-4">
            <img src="<?= base_url('images/logo/tapkasir.png') ?>" alt="TapKasir" class="w-14 h-14 object-contain">
            <span class="font-primary font-extrabold text-gray-700 text-xl">TapKasir</span>
        </a>
        <div class="flex flex-col w-full space-y-5 font-secondary overflow-y-auto flex-1">
            <li x-data="{ dropDownMaster: <?= in_array(uri_string(), ['admin/produk', 'admin/kategori']) ? '1' : 'null' ?> }"
                x-init="dropDownMaster = <?= in_array(uri_string(), ['admin/produk', 'admin/kategori']) ? 1 : 'null' ?>"
                class="flex flex-col space-y-1">
                <a href="<?= base_url('admin/dashboard') ?>" class="py-4 px-6 flex items-center space-x-3 hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?=  base_url('admin/dashboard') == current_url() ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                    <i class="fas fa-home text-primary text-xl"></i>
                    <span>Dashboard</span>
                </a>
                <button @click="dropDownMaster === 1 ? dropDownMaster = null : dropDownMaster = 1" type="button" class="py-4 px-6 flex justify-between items-center hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out  <?= in_array(uri_string(), ['admin/produk', 'admin/kategori']) ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-database text-primary text-xl"></i>
                        <span>Master Data</span>
                    </div>
                    <i x-bind:class="dropDownMaster === 1 ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-gray-400 transition-transform duration-300"></i>
                </button>
                <ul x-cloak x-show="dropDownMaster === 1" x-transition class="pl-12 space-y-1">
                    <li>
                        <a href="<?= base_url('admin/produk') ?>" class="p-3 flex items-center text-gray-600 hover:text-gray-800 bg-white rounded-md hover:bg-secondary <?= base_url('admin/produk') == current_url() ? 'bg-secondary text-gray-800 font-semibold' : '' ?>">
                            <i class="fas fa-box mr-2 text-primary"></i>
                            <span class="text-sm">Produk</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/kategori') ?>" class="p-3 flex items-center text-gray-600 hover:text-gray-800 bg-white rounded-md hover:bg-secondary <?= base_url('admin/kategori') == current_url() ? 'bg-secondary text-gray-800 font-semibold' : '' ?>">
                            <i class="fas fa-tags mr-2 text-primary"></i>
                            <span class="text-sm">Kategori</span>
                        </a>
                    </li>   
                </ul>
                <a href="<?= base_url('admin/users') ?>" class="py-4 px-6 flex items-center space-x-3 hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?=  base_url('admin/users') == current_url() ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                    <i class="fas fa-users-cog text-primary text-xl"></i>
                    <span>Manajemen User</span>
                </a>
                <a href="<?= base_url('admin/transaksi') ?>" class="py-4 px-6 flex items-center space-x-3 hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?=  base_url('admin/transaksi') == current_url() ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                    <i class="fas fa-exchange-alt text-primary text-xl"></i>
                    <span>Transaksi</span>
                </a>
                <a href="<?= base_url('admin/laporan') ?>" class="py-4 px-6 flex items-center space-x-3 hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?=  base_url('admin/laporan') == current_url() ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                    <i class="fas fa-file-alt text-primary text-xl"></i>
                    <span>Laporan</span>
                </a>
            </li>
        </div>
        <div class=" flex items-center justify-between p-4 gap-2">
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
                    class="absolute bottom-12 lg:bottom-14 right-0 lg:-translate-x-3/4 w-40 lg:w-48 bg-white shadow-md rounded-md z-50">
                    <a href="<?= base_url('logout') ?>"
                        class="flex items-center px-3 lg:px-4 py-2 lg:py-3 hover:bg-gray-100 rounded-md transition-colors duration-300 ease-in-out">
                        <i class="fas fa-sign-out-alt text-red-500 mr-2 text-xs lg:text-sm"></i>
                        <span class="text-gray-500 text-xs lg:text-sm">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>