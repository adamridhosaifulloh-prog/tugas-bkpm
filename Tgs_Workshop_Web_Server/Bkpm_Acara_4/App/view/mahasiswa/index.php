<?php

require_once __DIR__ . '/../../model/Mahasiswa.php';

$mahasiswa1 = new Mahasiswa(
    '2301001',
    'Andi',
    'Teknik Informatika'
);

$mahasiswa2 = new Mahasiswa(
    '2301002',
    'Budi',
    'Sistem Informasi'
);

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <h1>Data Mahasiswa</h1>


</div>

<table class="table table-bordered table-striped">

    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Nama</th>
            <th>Prodi</th>
            <th>Angkatan</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>

        <tr>
            <td>1</td>
            <td><?= $mahasiswa1->getNim() ?></td>
            <td><?= $mahasiswa1->getNama() ?></td>
            <td><?= $mahasiswa1->getProdi() ?></td>
            <td><?= $mahasiswa1->getAngkatan() ?></td>
            <td>
                </a>
            </td>
        </tr>

        <tr>
            <td>2</td>
            <td><?= $mahasiswa2->getNim() ?></td>
            <td><?= $mahasiswa2->getNama() ?></td>
            <td><?= $mahasiswa2->getProdi() ?></td>
            <td><?= $mahasiswa2->getAngkatan() ?></td>
            <td>
            </td>
        </tr>

    </tbody>

</table>

<?php
$content = ob_get_clean();

require_once __DIR__ . '/../layout/main.php';
?>