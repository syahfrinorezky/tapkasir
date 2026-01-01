<?= $this->extend('layout/main') ?>
<?= $this->section('title') ?> Lupa Password <?= $this->endSection() ?>
<?= $this->section('content') ?>
<main
    class="flex flex-col space-y-4 items-center justify-center min-h-screen font-secondary bg-gradient-to-br from-gray-50 to-gray-100 px-4">
    <a href="<?= base_url('/') ?>" class="flex items-center justify-center space-x-2">
        <img src="<?= base_url('images/logo/tapkasir.png') ?>" alt="logo tapkasir" class="w-8 md:w-10">
        <h1 class="text-lg md:text-xl font-bold text-gray-800 font-primary">Tapkasir</h1>
    </a>

    <div class="bg-white shadow-2xl border border-gray-200 flex flex-col w-full max-w-md rounded-2xl overflow-hidden p-6 md:p-8"
        x-data="passwordApp()">

        <div class="text-center mb-5">
            <div
                class="inline-flex items-center justify-center w-12 h-12 md:w-14 md:h-14 bg-gradient-to-br from-primary to-accent rounded-full mb-3">
                <i class="fas fa-key text-white text-lg md:text-xl"></i>
            </div>
            <h1 class="font-primary text-lg md:text-xl text-primary font-bold mb-2">Reset Password</h1>
            <p class="text-gray-600 text-xs md:text-sm">
                Masukkan email Anda dan kami akan mengirimkan link untuk reset password
            </p>
        </div>

        <template x-if="message">
            <div
                class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-3 py-2.5 rounded-lg text-xs md:text-sm flex items-start gap-2 mb-4">
                <i class="fas fa-check-circle text-base mt-0.5"></i>
                <span x-text="message" class="flex-1"></span>
            </div>
        </template>

        <template x-if="error">
            <div
                class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 text-red-700 px-3 py-2.5 rounded-lg text-xs md:text-sm flex items-start gap-2 mb-4">
                <i class="fas fa-exclamation-circle text-base mt-0.5"></i>
                <span x-text="error" class="flex-1"></span>
            </div>
        </template>

        <form @submit.prevent="sendLink" class="space-y-4">
            <?= csrf_field() ?>

            <div class="flex flex-col space-y-1">
                <label class="flex items-center space-x-1 font-semibold">
                    <i class="fas fa-envelope text-accent-2"></i>
                    <span class="text-gray-700">Email</span>
                </label>
                <input type="email" x-model="email" placeholder="Masukkan email"
                    class="border-2 border-gray-300 hover:ring-2 hover:ring-accent p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition-all duration-300"
                    :class="errors.email ? 'ring-2 ring-red-500 border-red-500' : ''" required>
                <p x-show="errors.email" x-text="errors.email" class="text-red-500 text-xs italic"></p>
            </div>

            <button type="submit" :disabled="loading || !email"
                class="w-full py-2.5 md:py-3 flex items-center justify-center bg-gradient-to-r from-primary to-accent text-white rounded-xl font-semibold hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 gap-2 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100 text-sm">
                <i class="fas fa-circle-notch fa-spin" x-show="loading"></i>
                <i class="fas fa-paper-plane" x-show="!loading"></i>
                <span x-text="loading ? 'Mengirim...' : 'Kirim Link Reset'"></span>
            </button>
        </form>

        <div class="flex items-center gap-3 my-5">
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
            <span class="text-xs text-gray-500 font-medium">ATAU</span>
            <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
        </div>

        <a href="<?= base_url('/') ?>"
            class="text-center text-xs md:text-sm font-semibold text-primary hover:text-accent transition-colors flex items-center justify-center gap-1.5 py-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Login</span>
        </a>
    </div>

    <p class="text-xs text-gray-500 text-center max-w-md">
        Link reset password akan berlaku selama 1 jam demi keamanan akun Anda
    </p>
</main>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="<?= base_url('js/main/auth/auth.js') ?>"></script>
<?= $this->endSection() ?>