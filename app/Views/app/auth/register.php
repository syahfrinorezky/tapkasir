<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Daftar
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="flex items-center justify-center min-h-screen font-secondary bg-white">
    <div class="bg-white shadow-lg shadow-accent border border-gray-100 flex flex-col md:flex-row w-full max-w-xs md:max-w-2xl lg:max-w-4xl rounded-xl overflow-hidden">

        <div class="hidden md:flex items-center justify-center md:w-1/2 bg-gray-50">
            <img src="<?= base_url('images/illustration/register-illustration.png') ?>" alt="register" class="w-full max-w-sm">
        </div>

        <div class="w-full md:w-1/2 p-6 lg:p-8 flex flex-col space-y-4">
            <h1 class="text-center font-primary text-xl text-primary font-bold">Daftar</h1>

            <?= $this->include('components/alert') ?>

            <p class="text-center text-sm text-gray-600">
                Kamu sudah memiliki akun?
                <a href="<?= base_url('/') ?>" class="text-accent hover:text-primary font-semibold transition-colors duration-300 ease-in-out">Masuk</a>
            </p>

            <form action="<?= base_url('daftar') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="flex flex-col space-y-3">
                    <!-- nama lengkap -->
                    <div class="flex flex-col space-y-1">
                        <label for="nama_lengkap" class="flex items-center space-x-1 font-semibold">
                            <i class="fas fa-user text-accent-2"></i>
                            <span class="text-gray-700">Nama Lengkap</span>
                        </label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap') ?>" placeholder="Masukkan nama lengkap" class="border border-gray-300 hover:ring-2 hover:ring-secondary p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition-all duration-300 ease-in-out <?= validation_show_error('nama_lengkap') ? 'ring-2 ring-red-500' : '' ?>" required>
                        <p class="text-red-500 text-xs italic"><?= validation_show_error('nama_lengkap') ?></p>
                    </div>

                    <!-- email -->
                    <div class="flex flex-col space-y-1">
                        <label for="email" class="flex items-center space-x-1 font-semibold">
                            <i class="fas fa-envelope text-accent-2"></i>
                            <span class="text-gray-700">Email</span>
                        </label>
                        <input type="email" id="email" name="email" value="<?= old('email') ?>" placeholder="Masukkan email" class="border border-gray-300 hover:ring-2 hover:ring-accent p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition-all duration-300 ease-in-out <?= validation_show_error('email') ? 'ring-2 ring-red-500' : '' ?>" required>
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
                        <input x-bind:type="show ? 'text' : 'password'" id="password" name="password" placeholder="Masukkan password" class="w-full border border-gray-300 hover:ring-2 hover:ring-accent p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent transition-all duration-300 ease-in-out <?= validation_show_error('password') ? 'ring-2 ring-red-500' : '' ?>" required>
                        <button type="button" @click="show = !show" class="w-10 p-2 bg-primary text-white rounded-lg hover:bg-shadow-accent">
                            <span x-show="!show"><i class="fas fa-eye"></i></span>
                            <span x-show="show"><i class="fas fa-eye-slash"></i></span>
                        </button>
                    </div>
                    <p class="text-red-500 text-xs italic"><?= validation_show_error('password') ?></p>
                </div>

                <button type="submit" class="mt-5 py-2 w-full flex items-center justify-center bg-primary hover:brightness-90 text-white rounded-lg cursor-pointer font-semibold transition-all duration-300 ease-in-out">
                    Daftar
                </button>
            </form>
        </div>
    </div>
</main>
<?= $this->endSection() ?>