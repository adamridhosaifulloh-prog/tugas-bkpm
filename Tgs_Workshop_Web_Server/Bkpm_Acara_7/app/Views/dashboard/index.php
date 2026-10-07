<?php require_once __DIR__ . '/../partials/header.php'; ?>

<?php require_once __DIR__ . '/../partials/navbar.php'; ?>

<div class="container mt-5">

    <?php if (isset($_SESSION['flash'])): ?>

        <div class="alert alert-success">
            <?= $_SESSION['flash']; ?>
        </div>

        <?php unset($_SESSION['flash']); ?>

    <?php endif; ?>


    <div class="card">

        <div class="card-body">

            <h1>Dashboard</h1>

            <p>
                Selamat datang,
                <b><?= $_SESSION['user']['nama']; ?></b>
            </p>

            <p>
                Anda berhasil login ke Sistem Akademik.
            </p>

            <a
                href="<?= BASE_URL ?>/mahasiswa"
                class="btn btn-primary"
            >
                Data Mahasiswa
            </a>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>