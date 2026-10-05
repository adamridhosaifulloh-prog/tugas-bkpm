<?php require __DIR__ . '/../header.php'; ?>

<h1 class="h3 mb-3"><?= $data['id'] ? 'Edit' : 'Tambah' ?> Mata Kuliah</h1>

<form method="POST" action="<?= e($action) ?>" class="card card-body">
    <input type="hidden" name="id" value="<?= (int) $data['id'] ?>">

    <div class="mb-3">
        <label class="form-label">Kode</label>
        <input type="text" name="kode" class="form-control" value="<?= e($data['kode']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Nama Mata Kuliah</label>
        <input type="text" name="nama" class="form-control" value="<?= e($data['nama']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">SKS</label>
        <input type="number" name="sks" class="form-control" value="<?= e($data['sks']) ?>" min="1" max="6" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Program Studi</label>
        <select name="prodi_id" class="form-select" required>
            <option value="">-- Pilih Prodi --</option>
            <?php foreach ($prodiList as $p): ?>
                <option value="<?= (int) $p['id'] ?>" <?= (int) $data['prodi_id'] === (int) $p['id'] ? 'selected' : '' ?>>
                    <?= e($p['kode'] . ' - ' . $p['nama']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= url('/matakuliah') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>

<?php require __DIR__ . '/../footer.php'; ?>
