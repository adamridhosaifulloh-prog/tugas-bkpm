<?php require_once __DIR__ . '/../partials/header.php'; ?>

<?php require_once __DIR__ . '/../partials/navbar.php'; ?>

<div class="container mt-5">

    <div class="d-flex justify-content-between mb-3">

        <h2>Data Mahasiswa</h2>

        <a
            href="<?= BASE_URL ?>/mahasiswa/create"
            class="btn btn-primary"
        >
            Tambah Mahasiswa
        </a>

    </div>

    <?php if ($error !== ''): ?>

        <div class="alert alert-danger">
            <?= e($error) ?>
        </div>

    <?php endif; ?>

    <table class="table table-bordered table-striped">

        <thead class="table-dark">

            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Program Studi</th>
                <th>Angkatan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            <?php
            // Warna badge sesuai status mahasiswa
            $warnaStatus = [
                'aktif' => 'success',
                'cuti'  => 'warning',
                'lulus' => 'secondary',
            ];
            ?>

            <?php foreach ($mahasiswa as $i => $m): ?>

                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= e($m['nim']) ?></td>
                    <td><?= e($m['nama']) ?></td>
                    <td><?= e($m['email']) ?></td>
                    <td><?= e($m['prodi']) ?></td>
                    <td><?= e($m['angkatan']) ?></td>
                    <td>
                        <span class="badge text-bg-<?= $warnaStatus[$m['status']] ?? 'light' ?>">
                            <?= e(ucfirst($m['status'])) ?>
                        </span>
                    </td>

                    <td>
                        <a
                            href="<?= BASE_URL ?>/mahasiswa/edit?id=<?= (int) $m['id'] ?>"
                            class="btn btn-warning btn-sm"
                        >
                            Edit
                        </a>
                    </td>
                </tr>

            <?php endforeach; ?>

            <?php if (empty($mahasiswa) && $error === ''): ?>

                <tr>
                    <td colspan="8" class="text-center">Belum ada data.</td>
                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
