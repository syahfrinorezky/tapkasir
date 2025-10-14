<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php $title = $this->renderSection('title'); ?>
    <title>
        <?= $title ? 'TapKasir - ' . $title : 'TapKasir' ?>
    </title>


    <link href="<?= base_url('css/output.css') ?>" rel="stylesheet">
</head>

<body>
    <?= $this->renderSection('content') ?>
</body>
<script src="<?= base_url('js/bundle.js') ?>"></script>

</html>