<?php require __DIR__ . '/../header.php'; ?>

<div class="d-flex justify-content-between mb-3">
    <h1 class="h3">Data Mata Kuliah</h1>
    <a href="<?= url('/matakuliah/create') ?>" class="btn btn-primary">+ Tambah Mata Kuliah</a>
</div>

<table class="table table-striped table-bordered bg-white">
    <thead class="table-dark">
        <tr><th>No</th><th>Kode</th><th>Nama</th><th>SKS</th><th>Prodi</th><th>Aksi</th></tr>
    </thead>
    <tbody>
        <?php foreach ($daftar as $i => $mk): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= e($mk['kode']) ?></td>
                <td><?= e($mk['nama']) ?></td>
                <td><?= e($mk['sks']) ?></td>
                <td><?= e($mk['prodi_nama']) ?></td>
                <td>
                    <a href="<?= url('/matakuliah/edit?id=' . $mk['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                    <form method="POST" action="<?= url('/matakuliah/destroy') ?>" class="d-inline"
                          onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                        <input type="hidden" name="id" value="<?= (int) $mk['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$daftar): ?>
            <tr><td colspan="6" class="text-center">Belum ada data.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../footer.php'; ?>
