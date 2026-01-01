<div class="fixed top-4 right-4 left-4 md:left-auto md:right-5 z-50 flex flex-col gap-3 pointer-events-none">
    <?php 
    $phpSuccess = session()->getFlashdata('success');
    $phpError = session()->getFlashdata('error');
    ?>

    <?php if ($phpError): ?>
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-full"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full"
             class="pointer-events-auto bg-white border-l-4 border-red-500 shadow-lg rounded-lg p-4 flex items-center gap-3 w-full md:w-96 transform">
            <div class="text-red-500">
                <i class="fas fa-exclamation-circle text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-gray-800 font-semibold text-sm">Error</h3>
                <p class="text-gray-600 text-sm mt-1"><?= esc($phpError) ?></p>
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($phpSuccess): ?>
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-full"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full"
             class="pointer-events-auto bg-white border-l-4 border-green-500 shadow-lg rounded-lg p-4 flex items-center gap-3 w-full md:w-96 transform">
            <div class="text-green-500">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-gray-800 font-semibold text-sm">Success</h3>
                <p class="text-gray-600 text-sm mt-1"><?= esc($phpSuccess) ?></p>
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    <?php endif; ?>

    <template x-if="typeof message !== 'undefined' && message">
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="$watch('message', value => { if(value) { show = true; setTimeout(() => { show = false; setTimeout(() => message = '', 300) }, 5000) } })"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-full"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full"
             class="pointer-events-auto bg-white border-l-4 border-green-500 shadow-lg rounded-lg p-4 flex items-center gap-3 w-full md:w-96 transform">
            <div class="text-green-500">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-gray-800 font-semibold text-sm">Success</h3>
                <p class="text-gray-600 text-sm mt-1" x-text="message"></p>
            </div>
            <button @click="show = false; setTimeout(() => message = '', 300)" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </template>

    <template x-if="typeof error !== 'undefined' && error">
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="$watch('error', value => { if(value) { show = true; setTimeout(() => { show = false; setTimeout(() => error = '', 300) }, 5000) } })"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-full"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full"
             class="pointer-events-auto bg-white border-l-4 border-red-500 shadow-lg rounded-lg p-4 flex items-center gap-3 w-full md:w-96 transform">
            <div class="text-red-500">
                <i class="fas fa-exclamation-circle text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-gray-800 font-semibold text-sm">Error</h3>
                <p class="text-gray-600 text-sm mt-1" x-text="error"></p>
            </div>
            <button @click="show = false; setTimeout(() => error = '', 300)" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </template>
</div>
