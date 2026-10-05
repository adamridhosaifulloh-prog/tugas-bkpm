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


    <table class="table table-bordered">

        <thead class="table-dark">

            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Program Studi</th>
                <th>Aksi</th>
            </tr>

        </thead>

        <tbody>

            <tr>
                <td>1</td>
                <td>23001</td>
                <td>Andi</td>
                <td>Teknik Informatika</td>

                <td>
                    <a
                        href="<?= BASE_URL ?>/mahasiswa/edit"
                        class="btn btn-warning btn-sm"
                    >
                        Edit
                    </a>
                </td>
            </tr>


            <tr>
                <td>2</td>
                <td>23002</td>
                <td>Budi</td>
                <td>Sistem Informasi</td>

                <td>
                    <a
                        href="<?= BASE_URL ?>/mahasiswa/edit"
                        class="btn btn-warning btn-sm"
                    >
                        Edit
                    </a>
                </td>
            </tr>

        </tbody>

    </table>

</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>