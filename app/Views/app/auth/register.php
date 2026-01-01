<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Daftar
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main
    class="flex flex-col space-y-3 items-center justify-center min-h-screen font-secondary bg-gradient-to-br from-gray-50 to-gray-100 px-4">
    <a href="<?= base_url('/') ?>" class="flex items-center justify-center space-x-2">
        <img src="<?= base_url('images/logo/tapkasir.png') ?>" alt="logo tapkasir" class="w-8 md:w-10">
        <h1 class="text-lg md:text-xl font-bold text-gray-800 font-primary">Tapkasir</h1>
    </a>

    <div
        class="bg-white shadow-2xl border border-gray-200 flex flex-col md:flex-row w-full md:max-w-2xl lg:max-w-4xl rounded-2xl overflow-hidden">

        <div class="hidden md:flex items-center justify-center md:w-1/2 bg-gradient-to-br from-primary to-accent-2">
            <img src="<?= base_url('images/illustration/register-illustration.png') ?>" alt="register"
                class="w-full max-w-sm">
        </div>

        <div class="w-full md:w-1/2 p-6 lg:p-8 flex flex-col space-y-4">
            <h1 class="text-center font-primary text-lg md:text-xl text-primary font-bold">Daftar</h1>

            <p class="text-center text-sm text-gray-600">
                Kamu sudah memiliki akun?
                <a href="<?= base_url('/') ?>"
                    class="text-accent hover:text-primary font-semibold transition-colors duration-300">Masuk</a>
            </p>

            <div x-data="registerApp()" class="w-full">
                <?= $this->include('components/notifications') ?>

                <form @submit.prevent="submit">
                    <?= csrf_field() ?>
                    <div class="flex flex-col space-y-3">
                        <div class="flex flex-col space-y-1">
                            <label for="nama_lengkap" class="flex items-center space-x-1 font-semibold">
                                <i class="fas fa-user text-accent-2"></i>
                                <span class="text-gray-700">Nama Lengkap</span>
                            </label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" x-model="nama_lengkap"
                                placeholder="Masukkan nama lengkap"
                                class="border-2 border-gray-300 hover:ring-2 hover:ring-accent p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition-all duration-300"
                                :class="errors.nama_lengkap ? 'ring-2 ring-red-500 border-red-500' : ''"
                                required>
                            <p x-show="errors.nama_lengkap" x-text="errors.nama_lengkap" class="text-red-500 text-xs italic"></p>
                        </div>

                        <div class="flex flex-col space-y-1">
                            <label for="email" class="flex items-center space-x-1 font-semibold">
                                <i class="fas fa-envelope text-accent-2"></i>
                                <span class="text-gray-700">Email</span>
                            </label>
                            <input type="email" id="email" name="email" x-model="email" placeholder="Masukkan email"
                                class="border-2 border-gray-300 hover:ring-2 hover:ring-accent p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition-all duration-300"
                                :class="errors.email ? 'ring-2 ring-red-500 border-red-500' : ''"
                                required>
                            <p x-show="errors.email" x-text="errors.email" class="text-red-500 text-xs italic"></p>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-1 mt-3">
                        <label for="password" class="flex items-center space-x-1 font-semibold">
                            <i class="fas fa-lock text-accent-2"></i>
                            <span class="text-gray-700">Password</span>
                        </label>
                        <div class="flex space-x-2">
                            <input x-bind:type="show ? 'text' : 'password'" id="password" name="password" x-model="password"
                                placeholder="Masukkan password"
                                class="w-full border-2 border-gray-300 hover:ring-2 hover:ring-accent p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition-all duration-300"
                                :class="errors.password ? 'ring-2 ring-red-500 border-red-500' : ''"
                                required>
                            <button type="button" @click="show = !show"
                                class="w-10 p-2 bg-primary text-white rounded-lg hover:brightness-90">
                                <span x-show="!show"><i class="fas fa-eye"></i></span>
                                <span x-show="show"><i class="fas fa-eye-slash"></i></span>
                            </button>
                        </div>
                        <p x-show="errors.password" x-text="errors.password" class="text-red-500 text-xs italic"></p>
                    </div>

                    <button type="submit" :disabled="loading || !(nama_lengkap && nama_lengkap.trim()) || !email || !password"
                        :class="(loading || !(nama_lengkap && nama_lengkap.trim()) || !email || !password) ? 'opacity-60 cursor-not-allowed' : ''"
                        class="mt-5 py-2.5 w-full flex items-center justify-center bg-gradient-to-r from-primary to-accent hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] text-white rounded-xl font-semibold transition-all duration-300 gap-2 disabled:hover:scale-100">
                        <i class="fas fa-circle-notch fa-spin" x-show="loading"></i>
                        <span x-text="loading ? 'Memproses…' : 'Daftar'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>
<script src="<?= base_url('js/main/auth/auth.js') ?>"></script>
<?= $this->endSection() ?>