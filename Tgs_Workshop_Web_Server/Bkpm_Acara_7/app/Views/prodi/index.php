<?php require __DIR__ . '/../header.php'; ?>

<div class="d-flex justify-content-between mb-3">
    <h1 class="h3">Data Program Studi</h1>
    <a href="<?= url('/prodi/create') ?>" class="btn btn-primary">+ Tambah Prodi</a>
</div>

<table class="table table-striped table-bordered bg-white">
    <thead class="table-dark">
        <tr><th>No</th><th>Kode</th><th>Nama Prodi</th><th>Aksi</th></tr>
    </thead>
    <tbody>
        <?php foreach ($daftar as $i => $p): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= e($p['kode']) ?></td>
                <td><?= e($p['nama']) ?></td>
                <td>
                    <a href="<?= url('/prodi/edit?id=' . $p['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                    <form method="POST" action="<?= url('/prodi/destroy') ?>" class="d-inline"
                          onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$daftar): ?>
            <tr><td colspan="4" class="text-center">Belum ada data.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../footer.php'; ?>
