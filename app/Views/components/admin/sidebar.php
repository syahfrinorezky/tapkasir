<div class="hidden lg:flex w-2/8 bg-white shadow-md h-screen">
    <div class="overflow-hidden w-full flex flex-col justify-between p-4">
        <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center justify-center space-x-2 py-4">
            <img src="<?= base_url('images/logo/tapkasir.png') ?>" alt="TapKasir" class="w-14 h-14 object-contain">
            <span class="hidden md:inline font-primary font-extrabold text-gray-700 text-xl">TapKasir</span>
        </a>
        <div class="flex flex-col w-full space-y-5 font-secondary  overflow-y-auto h-screen">
            <nav class="flex flex-col space-y-4">
                <a href="<?= base_url('admin/dashboard') ?>" class="px-8 py-4 flex items-center space-x-3 hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?= uri_string() == 'admin/dashboard' ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                    <i class="fas fa-home text-primary text-xl"></i>
                    <span>Dashboard</span>
                </a>
            </nav>
        </div>
        <div class="flex items-center justify-between p-4">
            <div class="flex space-x-3 items-center">
                <div class="flex items-center justify-center aspect-square p-3 bg-gray-200 rounded-full">
                    <i class="fas fa-user text-gray-600 text-sm"></i>
                </div>
                <div class="hidden md:flex flex-col justify-center min-w-0 flex-1">
                    <?php $nama = session()->get('nama_lengkap') ?>
                    <span title="<?= esc($nama) ?>" class="font-secondary font-semibold text-sm text-gray-700 truncate max-w-[90%]">
                        <?= esc($nama) ?>
                    </span>
                    <span class="text-gray-400 text-xs capitalize truncate max-w-xs">
                        <?= esc(session()->get('role_name')) ?>
                    </span>
                </div>
            </div>
            <div x-data="{ isAccMenuOpen: false }" class="relative">
                <button @click="isAccMenuOpen = !isAccMenuOpen" type="button"
                    class="flex items-center justify-center w-8 h-8 hover:bg-gray-200 rounded-md cursor-pointer transition-colors duration-300 ease-in-out">
                    <i class="fas fa-ellipsis text-gray-600 text-sm"></i>
                </button>

                <div x-show="isAccMenuOpen" @click.away="isAccMenuOpen = false" x-transition
                    class="absolute bottom-14 -translate-x-3/4 w-48 bg-white shadow-md rounded-md z-50">
                    <a href="<?= base_url('logout') ?>"
                        class="flex items-center px-4 py-3 hover:bg-gray-100 rounded-md transition-colors duration-300 ease-in-out">
                        <i class="fas fa-sign-out-alt text-red-500 mr-2"></i>
                        <span class="text-gray-500 text-sm">Logout</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>