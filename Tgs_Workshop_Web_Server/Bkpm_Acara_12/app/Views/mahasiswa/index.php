<?php require __DIR__ . '/../header.php'; ?>

<div class="d-flex justify-content-between mb-3">
    <h1 class="h3">Data Mahasiswa</h1>
    <a href="<?= url('/mahasiswa/create') ?>" class="btn btn-primary">+ Tambah Mahasiswa</a>
</div>

<form method="GET" action="<?= url('/mahasiswa') ?>" class="row g-2 mb-3">
    <div class="col-md-6">
        <input type="text" name="q" value="<?= e($keyword) ?>" class="form-control"
               placeholder="Cari berdasarkan nama atau NIM...">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-primary">Cari</button>
        <?php if ($keyword !== ''): ?>
            <a href="<?= url('/mahasiswa') ?>" class="btn btn-outline-secondary">Reset</a>
        <?php endif; ?>
    </div>
</form>

<table class="table table-striped table-bordered bg-white">
    <thead class="table-dark">
        <tr><th>No</th><th>NIM</th><th>Nama</th><th>Email</th><th>Prodi</th><th>Angkatan</th><th>Aksi</th></tr>
    </thead>
    <tbody>
        <?php foreach ($daftar as $i => $m): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= e($m->getNim()) ?></td>
                <td><?= e($m->getNama()) ?></td>
                <td><?= e($m->getEmail()) ?></td>
                <td><?= e($m->getProdiNama()) ?></td>
                <td><?= e($m->getAngkatan()) ?></td>
                <td>
                    <a href="<?= url('/mahasiswa/edit?id=' . $m->getId()) ?>" class="btn btn-sm btn-warning">Edit</a>
                    <form method="POST" action="<?= url('/mahasiswa/destroy') ?>" class="d-inline"
                          onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                        <input type="hidden" name="id" value="<?= $m->getId() ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$daftar): ?>
            <tr><td colspan="7" class="text-center">
                <?= $keyword !== '' ? 'Tidak ada data yang cocok.' : 'Belum ada data.' ?>
            </td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../footer.php'; ?>
