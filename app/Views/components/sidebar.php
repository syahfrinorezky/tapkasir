<?php
$role = strtolower(session()->get('role_name') ?? '');
$current_uri = uri_string();

if ($role === 'kasir') {
    $menu = [
        ['uri' => 'cashier/transactions', 'icon' => 'fas fa-exchange-alt', 'label' => 'Transaksi'],
        ['uri' => 'cashier/transactions/log', 'icon' => 'fas fa-file-alt', 'label' => 'Log Transaksi'],
        ['uri' => 'cashier/products', 'icon' => 'fas fa-box', 'label' => 'Produk'],
    ];
} else {
    $menu = [
        ['uri' => 'admin/dashboard', 'icon' => 'fas fa-home', 'label' => 'Dashboard'],
        ['type' => 'dropdown', 'label' => 'Master Data', 'icon' => 'fas fa-database', 'children' => [
            ['uri' => 'admin/products', 'label' => 'Produk', 'icon' => 'fas fa-box'],
            ['uri' => 'admin/shifts', 'label' => 'Shift', 'icon' => 'fas fa-clock'],
        ]],
        ['uri' => 'admin/users', 'icon' => 'fas fa-users-cog', 'label' => 'Manajemen User'],
        ['uri' => 'admin/transactions', 'icon' => 'fas fa-exchange-alt', 'label' => 'Transaksi'],
        ['uri' => 'admin/laporan', 'icon' => 'fas fa-file-alt', 'label' => 'Laporan'],
    ];
}

if (!function_exists('is_active')) {
    function is_active($current_uri, $uri)
    {
        return $current_uri === $uri;
    }
}
?>

<div class="hidden lg:flex lg:w-75 bg-white shadow-md h-screen sticky top-0 flex-shrink-0">
    <div class="w-full flex flex-col justify-between p-4">
        <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center justify-center space-x-2 py-4">
            <img src="<?= base_url('images/logo/tapkasir.png') ?>" alt="TapKasir" class="w-14 h-14 object-contain">
            <span class="font-primary font-extrabold text-gray-700 text-xl">TapKasir</span>
        </a>
        <div class="flex flex-col w-full space-y-5 font-secondary overflow-y-auto flex-1">
            <li class="flex flex-col space-y-1">
                <?php foreach ($menu as $item): ?>
                    <?php if ((($item['type'] ?? 'link') === 'dropdown') && !empty($item['children'])): ?>
                        <?php
                        $childUris = is_array($item['children']) ? array_column($item['children'], 'uri') : [];
                        $is_open = in_array($current_uri, $childUris) ? '1' : 'null';
                        ?>
                        <div x-data="{ dropDown: <?= $is_open ?> }" x-init="dropDown = <?= $is_open ?>">
                            <button @click="dropDown === 1 ? dropDown = null : dropDown = 1"
                                type="button"
                                class="w-full py-4 px-6 flex justify-between items-center hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?= $is_open === '1' ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                                <div class="flex items-center space-x-3">
                                    <i class="<?= $item['icon'] ?> text-primary text-xl"></i>
                                    <span><?= esc($item['label']) ?></span>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" :style="dropDown === 1 ? 'transform: rotate(180deg);' : ''"></i>
                            </button>
                            <ul x-cloak x-show="dropDown === 1" x-transition class="pl-12 space-y-1 mt-1">
                                <?php foreach ($item['children'] as $c): ?>
                                    <li>
                                        <a href="<?= base_url($c['uri']) ?>"
                                            class="w-full p-3 flex items-center rounded-md hover:bg-secondary <?= is_active($current_uri, $c['uri']) ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500 hover:text-gray-800' ?>">
                                            <i class="<?= $c['icon'] ?> mr-2 text-primary"></i>
                                            <span class="text-sm"><?= esc($c['label']) ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= base_url($item['uri']) ?>"
                            class="w-full py-4 px-6 flex items-center space-x-3 hover:bg-secondary hover:font-semibold rounded-md transition-all duration-300 ease-in-out <?= is_active($current_uri, $item['uri']) ? 'bg-secondary text-gray-800 font-semibold' : 'text-gray-500' ?>">
                            <i class="<?= $item['icon'] ?> text-primary text-xl"></i>
                            <span><?= esc($item['label']) ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
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
                    class="absolute bottom-12 lg:bottom-14 right-0 w-40 lg:w-48 bg-white shadow-md rounded-md z-50">
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