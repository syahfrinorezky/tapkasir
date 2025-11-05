<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Masuk
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="flex flex-col space-y-3 items-center justify-center min-h-screen font-secondary bg-white">
    <a href="<?= base_url('/') ?>" class="flex items-center justify-center space-x-2 md:space-x-3">
        <img src="<?= base_url('images/logo/tapkasir.png') ?>" alt="logo tapkasir" class="w-10 md:w-12 lg:w-14">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800 font-primary">Tapkasir</h1>
    </a>

    <div class="bg-white shadow-lg shadow-accent border border-gray-100 flex flex-col md:flex-row w-4/5 md:w-full md:max-w-2xl lg:max-w-4xl rounded-xl overflow-hidden">

        <div class="hidden md:flex items-center justify-center md:w-1/2 bg-gradient-to-br from-primary to-accent-2">
            <img src="<?= base_url('images/illustration/login-illustration.png') ?>" alt="register" class="w-full max-w-sm">
        </div>

        <div class="w-full md:w-1/2 p-6 lg:p-8 flex flex-col space-y-4">
            <h1 class="text-center font-primary text-xl lg:text-2xl text-primary font-bold">Masuk</h1>

            <?= $this->include('components/alert') ?>

            <form action="<?= base_url('masuk') ?>" method="POST" x-data="{ email: '<?= esc(old('email') ?? '', 'js') ?>', password: '', submitting: false }" @submit="submitting = true">
                <?= csrf_field() ?>
                <div class="flex flex-col space-y-3">
                    <!-- email -->
                    <div class="flex flex-col space-y-1">
                        <label for="email" class="flex items-center space-x-1 font-semibold">
                            <i class="fas fa-envelope text-accent-2"></i>
                            <span class="text-gray-700">Email</span>
                        </label>
                        <input type="email" id="email" name="email" x-model="email" placeholder="Masukkan email" class="border border-gray-300 hover:ring-2 hover:ring-accent p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition-all duration-300 ease-in-out <?= validation_show_error('email') ? 'ring-2 ring-red-500' : '' ?>" required>
                        <p class="text-red-500 text-xs italic"><?= validation_show_error('email') ?></p>
                    </div>
                </div>

                <!-- password -->
                <div x-data="{ show: false }" class="flex flex-col space-y-1 mt-3">
                    <label for="password" class="flex items-center space-x-1 font-semibold">
                        <i class="fas fa-lock text-accent-2"></i>
                        <span class="text-gray-700">Password</span>
                    </label>
                    <div class="flex space-x-2">
                        <input x-bind:type="show ? 'text' : 'password'" id="password" name="password" x-model="password" placeholder="Masukkan password" class="w-full border border-gray-300 hover:ring-2 hover:ring-accent p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition-all duration-300 ease-in-out <?= validation_show_error('password') ? 'ring-2 ring-red-500' : '' ?>" required>
                        <button type="button" @click="show = !show" class="w-10 p-2 bg-primary text-white rounded-lg hover:bg-shadow-accent">
                            <span x-show="!show"><i class="fas fa-eye"></i></span>
                            <span x-show="show"><i class="fas fa-eye-slash"></i></span>
                        </button>
                    </div>
                    <p class="text-red-500 text-xs italic"><?= validation_show_error('password') ?></p>
                </div>

                <button type="submit"
                    :disabled="submitting || !email || !password"
                    :class="(submitting || !email || !password) ? 'opacity-60 cursor-not-allowed' : ''"
                    class="mt-5 py-2 w-full flex items-center justify-center bg-primary hover:brightness-90 text-white rounded-lg cursor-pointer font-semibold transition-all duration-300 ease-in-out gap-2">
                    <i class="fas fa-circle-notch fa-spin" x-show="submitting"></i>
                    <span x-text="submitting ? 'Memproses…' : 'Masuk'"></span>
                </button>
            </form>

            <div class="mt-5 flex flex-col items-center justify-center w-full space-y-2">
                <p class="text-center text-sm text-gray-600">
                    Belum memiliki akun?
                    <a href="<?= base_url('daftar') ?>" class="text-accent hover:text-primary font-semibold transition-colors duration-300 ease-in-out">Daftar</a>
                </p>

                <div class="flex items-center w-1/2">
                    <hr class="flex-grow border-t border-gray-300">
                    <span class="mx-3 text-gray-400 font-semibold whitespace-nowrap">atau</span>
                    <hr class="flex-grow border-t border-gray-300">
                </div>

                <p class="text-center text-sm text-gray-600">
                    Lupa password?
                    <a href="<?= base_url('lupa-password') ?>" class="text-accent hover:text-primary font-semibold transition-colors duration-300 ease-in-out">Reset Password</a>
                </p>
            </div>

        </div>
    </div>
</main>
<?= $this->endSection() ?>