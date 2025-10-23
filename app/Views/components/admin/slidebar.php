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
    class="fixed top-0 right-0 w-64 h-full bg-white shadow-lg z-50 flex flex-col justify-between">

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

        <nav class="flex flex-col space-y-4 font-secondary overflow-y-auto">
            <a href="<?= base_url('admin/dashboard') ?>" class="px-4 py-4 flex items-center space-x-3 hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?= uri_string() == 'admin/dashboard' ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                <i class="fas fa-home text-primary text-xl"></i>
                <span>Dashboard</span>
            </a>
        </nav>
    </div>

    <div class="flex items-center justify-between p-4">
        <div class="flex space-x-3 items-center">
            <div class=" aspect-square w-10 flex items-center justify-center bg-gray-200 rounded-full">
                <i class="fas fa-user text-gray-600 text-sm"></i>
            </div>
            <div class="flex flex-col justify-center min-w-0 flex-1">
                <?php $nama = session()->get('nama_lengkap') ?>
                <span title="<?= esc($nama) ?>" class="font-secondary font-semibold text-sm text-gray-700 truncate max-w-[75%]">
                    <?= esc($nama) ?>
                </span>
                <span class="text-gray-400 text-xs capitalize truncate max-w-xs">
                    <?= esc(session()->get('role_name')) ?>
                </span>
            </div>
        </div>
        <div x-data="{ isAccMenuOpen: false }" class="relative -left-10">
            <button @click="isAccMenuOpen = !isAccMenuOpen" type="button"
                class="flex items-center justify-center w-8 h-8 hover:bg-gray-200 rounded-md cursor-pointer transition-colors duration-300 ease-in-out">
                <i class="fas fa-ellipsis text-gray-600 text-sm"></i>
            </button>

            <div x-show="isAccMenuOpen" @click.away="isAccMenuOpen = false" x-transition
                class="absolute bottom-full right-0 mb-2 w-48 bg-white shadow-md rounded-md z-50">
                <a href="<?= base_url('logout') ?>"
                    class="flex items-center px-4 py-3 hover:bg-gray-100 rounded-md transition-colors duration-300 ease-in-out">
                    <i class="fas fa-sign-out-alt text-red-500 mr-2"></i>
                    <span class="text-gray-500 text-sm">Logout</span>
                </a>
            </div>
        </div>
    </div>
</div>