<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Dashboard';
$appName = config('app_name', 'Hospital Management System');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle); ?> | <?= e($appName); ?></title>
    <link rel="stylesheet" href="<?= e(asset_url('css/output.css')); ?>">
</head>
<body>
    <div class="min-h-screen lg:flex">

