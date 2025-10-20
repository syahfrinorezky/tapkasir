<div x-cloak x-show="slideOpen"
    x-transition:enter="transition ease-in-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-30"
    x-transition:leave="transition ease-in-out duration-300"
    x-transition:leave-start="opacity-30"
    x-transition:leave-end="opacity-0"
    class="md:hidden fixed inset-0 z-50 bg-black/30 backdrop-blur-sm">
</div>
<div x-cloak x-show="slideOpen"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-300 transform"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed top-0 right-0 w-64 h-full bg-white shadow-lg p-4 z-50">

    <div class="flex flex-col w-full space-y-5 font-secondary">
        <div class="flex items-center justify-end">
            <button @click="slideOpen = false" class="mb-4 p-2 hover:bg-gray-200 rounded-md transition-colors duration-300 ease-in-out">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <nav class="flex flex-col space-y-4">
            <a href="<?= base_url('admin/beranda') ?>" class="px-3 py-4 flex items-center space-x-2 hover:bg-gray-200 hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?= uri_string() == 'admin/beranda' ? 'bg-gray-200 font-medium' : '' ?>">
                <i class="fas fa-home text-primary"></i>
                <span>Beranda</span>
            </a>
        </nav>
    </div>
</div>