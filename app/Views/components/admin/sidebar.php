<div class="hidden lg:flex lg:w-1/5 bg-white shadow-md h-screen overflow-hidden">
    <div class="flex flex-col w-full space-y-5 font-secondary p-5">
        <h1 class="font-bold font-primary text-xl">Dashboard Admin</h1>
        <nav class="flex flex-col space-y-4">
            <a href="<?= base_url('admin/beranda') ?>" class="px-3 py-4 flex items-center space-x-2 hover:bg-gray-200 hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?= uri_string() == 'admin/beranda' ? 'bg-gray-200 font-medium' : '' ?>">
                <i class="fas fa-home text-primary"></i>
                <span>Beranda</span>
            </a>
        </nav>
    </div>
</div>