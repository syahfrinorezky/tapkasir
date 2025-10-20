<div x-data="{ slideOpen: false }" class="flex items-center justify-between px-4 py-3 bg-white shadow-md w-full">
    <a href="<?= base_url('admin/beranda') ?>" class="flex items-center space-x-2">
        <img src="<?= base_url('images/logo/tapkasir.png') ?>" alt="TapKasir" class="w-12 h-12">
        <h1 class="hidden md:flex font-primary text-lg md:text-xl font-semibold">TapKasir</h1>
    </a>

    <button @click="slideOpen = true" type="button" class="md:hidden p-2 hover:bg-gray-200 rounded-md transition-colors duration-300 ease-in-out">
        <i class="fas fa-bars text-lg"></i>
    </button>

    <?= $this->include('components/admin/slidebar'); ?>
</div>