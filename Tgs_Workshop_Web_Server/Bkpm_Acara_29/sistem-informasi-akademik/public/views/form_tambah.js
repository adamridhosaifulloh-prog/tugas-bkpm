// Langkah 1 (Acara 27): View tambah data memakai FormData + header Authorization.
if (!isAdmin) {
    setFlash('Akses ditolak, hanya admin yang boleh menambah data', 'error');
    window.location.replace('dashboard.html');
}

const form = document.querySelector('#form-mhs');
const btnSimpan = document.querySelector('#btn-simpan');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    bersihkanError(form);

    const nilai = {
        nim: form.nim.value.trim(),
        nama: form.nama.value.trim(),
        prodi: form.prodi.value.trim(),
        foto: form.foto.files[0] || null,
    };

    // Validasi sisi View: bila gagal, data TIDAK dikirim ke Controller.
    const errors = validasiMahasiswa(nilai);
    if (Object.keys(errors).length > 0) {
        tampilkanError(form, errors);
        notif('Periksa kembali isian formulir', 'error');
        return;
    }

    const body = new FormData();
    body.append('nim', nilai.nim);
    body.append('nama', nilai.nama);
    body.append('prodi', nilai.prodi);
    if (nilai.foto) body.append('foto', nilai.foto);

    btnSimpan.disabled = true;
    btnSimpan.textContent = 'Menyimpan...';
    try {
        const { status, hasil } = await api('/mahasiswa', { method: 'POST', body });
        if (hasil.success) {
            setFlash(hasil.message, 'success'); // ditampilkan di dashboard
            window.location.href = 'dashboard.html';
            return;
        }
        tampilkanError(form, hasil.errors || {});          // error per field dari server
        notif(pesanGagal(status, hasil), 'error');
    } catch (error) {
        console.error(error);
        notif('Tidak dapat menghubungi server API.', 'error');
    } finally {
        btnSimpan.disabled = false;
        btnSimpan.textContent = 'Simpan';
    }
});
