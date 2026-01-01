<?php

$success = session()->getFlashdata('success');
$error = session()->getFlashdata('error');

?>

<?php if ($error): ?>
    <div
        class="alert bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 text-red-700 px-3 py-2.5 rounded-lg text-xs md:text-sm flex items-start gap-2">
        <i class="fas fa-exclamation-circle text-base mt-0.5"></i>
        <p class="flex-1"><?= esc($error) ?></p>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div
        class="alert bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-3 py-2.5 rounded-lg text-xs md:text-sm flex items-start gap-2">
        <i class="fas fa-check-circle text-base mt-0.5"></i>
        <p class="flex-1"><?= esc($success) ?></p>
    </div>
<?php endif; ?>

<script>
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.display = 'none';
        });
    }, 5000);
</script>