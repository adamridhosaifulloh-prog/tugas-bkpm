<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Informasi Server</title>
</head>
<body>

    <h1>Informasi Server</h1>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Informasi</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td>Nama</td>
            <td>Nama Anda</td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>NIM Anda</td>
        </tr>
        <tr>
            <td>Waktu Server</td>
            <td><?php echo date("d-m-Y H:i:s"); ?></td>
        </tr>
        <tr>
            <td>Versi PHP</td>
            <td><?php echo phpversion(); ?></td>
        </tr>
        <tr>
            <td>Sistem Operasi Server</td>
            <td><?php echo PHP_OS; ?></td>
        </tr>
    </table>

</body>
</html>

