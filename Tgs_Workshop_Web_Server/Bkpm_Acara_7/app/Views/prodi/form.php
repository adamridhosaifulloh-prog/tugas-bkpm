<?php require __DIR__ . '/../header.php'; ?>

<h1 class="h3 mb-3"><?= $data['id'] ? 'Edit' : 'Tambah' ?> Prodi</h1>

<form method="POST" action="<?= e($action) ?>" class="card card-body">
    <input type="hidden" name="id" value="<?= (int) $data['id'] ?>">

    <div class="mb-3">
        <label class="form-label">Kode</label>
        <input type="text" name="kode" class="form-control" value="<?= e($data['kode']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Nama Prodi</label>
        <input type="text" name="nama" class="form-control" value="<?= e($data['nama']) ?>" required>
    </div>

    <div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= url('/prodi') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>

<?php require __DIR__ . '/../footer.php'; ?>
