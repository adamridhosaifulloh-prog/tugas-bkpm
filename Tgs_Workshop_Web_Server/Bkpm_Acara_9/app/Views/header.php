<?php
$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SI Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">SI Akademik</span>
        <div class="navbar-nav">
            <a class="nav-link" href="<?= url('/mahasiswa') ?>">Mahasiswa</a>
        </div>
        <div class="ms-auto d-flex align-items-center">
            <span class="navbar-text me-3"><?= e($_SESSION['user_name'] ?? '') ?></span>
            <a class="btn btn-outline-light btn-sm" href="<?= url('/logout') ?>">Logout</a>
        </div>
    </div>
</nav>

<main class="container pb-5">
    <?php if ($alert): ?>
        <div class="alert alert-<?= e($alert['type']) ?>"><?= e($alert['message']) ?></div>
    <?php endif; ?>
