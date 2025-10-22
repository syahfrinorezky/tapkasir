<?php

$success = session()->getFlashdata('success');
$error = session()->getFlashdata('error');

?>

<?php if ($error) : ?>
    <div class="alert py-2 px-3 flex items-center space-x-2 border border-red-700 bg-red-200 rounded-lg">
        <i class="fas fa-exclamation-circle text-red-700 text-sm"></i>
        <p class="text-red-700 text-sm"><?= esc($error) ?></p>
    </div>
<?php endif; ?>

<?php if ($success) : ?>
    <div class="alert py-2 px-3 flex items-center space-x-2 border border-green-500 bg-green-200 rounded-lg">
        <i class="fas fa-check-circle text-green-700 text-sm"></i>
        <p class="text-green-700 text-sm"><?= esc($success) ?></p>
    </div>
<?php endif; ?>


<script>
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.display = 'none';
        });
    }, 5000);
</script>