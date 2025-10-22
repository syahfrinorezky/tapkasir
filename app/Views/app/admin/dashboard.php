<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
Dashboard Admin
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<main class="">
    <div class="fixed top-0 w-full">
        <?= $this->include('components/admin/header'); ?>
    </div>

    <div class="flex">
        <?= $this->include('components/admin/sidebar'); ?>
    </div>

</main>
<?= $this->endSection() ?>