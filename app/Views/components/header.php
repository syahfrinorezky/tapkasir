<div x-data="{ slideOpen: false }" class="flex lg:hidden items-center justify-between px-4 py-3 bg-white shadow-md w-full">
    <?php $role = strtolower(session()->get('role_name') ?? ''); ?>
    <a href="<?= $role === 'kasir' ? base_url('kasir/dashboard') : base_url('admin/dashboard') ?>" class="flex items-center space-x-2">
        <img src="<?= base_url('images/logo/tapkasir.png') ?>" alt="TapKasir" class="w-12 h-12">
        <h1 class="hidden md:flex font-primary text-lg md:text-xl font-semibold">TapKasir</h1>
    </a>

    <button @click="slideOpen = true" type="button" class="lg:hidden p-2 hover:bg-gray-200 rounded-md transition-colors duration-300 ease-in-out">
        <i class="fas fa-bars text-lg"></i>
    </button>

    <?= $this->include('components/slidebar'); ?>
</div>