<?php
// Variabel: $m (objek Mahasiswa), $action, $prodiList, $errors, $old (input sebelumnya jika ada error)
$invalid = fn(string $f) => isset($errors[$f]) ? 'is-invalid' : '';
require __DIR__ . '/../header.php';
?>

<h1 class="h3 mb-3"><?= $m->getId() ? 'Edit' : 'Tambah' ?> Mahasiswa</h1>

<form method="POST" action="<?= e($action) ?>" class="card card-body" novalidate>
    <input type="hidden" name="id" value="<?= $m->getId() ?>">

    <div class="mb-3">
        <label class="form-label">NIM</label>
        <input type="text" name="nim" class="form-control <?= $invalid('nim') ?>"
               value="<?= e($old['nim'] ?? $m->getNim()) ?>">
        <div class="invalid-feedback"><?= e($errors['nim'] ?? '') ?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="nama" class="form-control <?= $invalid('nama') ?>"
               value="<?= e($old['nama'] ?? $m->getNama()) ?>">
        <div class="invalid-feedback"><?= e($errors['nama'] ?? '') ?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="text" name="email" class="form-control <?= $invalid('email') ?>"
               value="<?= e($old['email'] ?? $m->getEmail()) ?>">
        <div class="invalid-feedback"><?= e($errors['email'] ?? '') ?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Program Studi</label>
        <?php $prodiTerpilih = (int) ($old['prodi_id'] ?? $m->getProdiId()); ?>
        <select name="prodi_id" class="form-select <?= $invalid('prodi_id') ?>">
            <option value="">-- Pilih Prodi --</option>
            <?php foreach ($prodiList as $p): ?>
                <option value="<?= (int) $p['id'] ?>" <?= $prodiTerpilih === (int) $p['id'] ? 'selected' : '' ?>>
                    <?= e($p['kode'] . ' - ' . $p['nama']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="invalid-feedback"><?= e($errors['prodi_id'] ?? '') ?></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Angkatan</label>
        <input type="number" name="angkatan" class="form-control <?= $invalid('angkatan') ?>"
               value="<?= e($old['angkatan'] ?? $m->getAngkatan()) ?>">
        <div class="invalid-feedback"><?= e($errors['angkatan'] ?? '') ?></div>
    </div>

    <div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= url('/mahasiswa') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>

<?php require __DIR__ . '/../footer.php'; ?>
