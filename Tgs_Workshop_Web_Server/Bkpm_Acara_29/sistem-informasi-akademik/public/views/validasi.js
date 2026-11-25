// Tugas Praktik Mandiri 2 (Acara 27): validasi sisi View sebelum data dikirim.
// Ini lapisan tambahan; validasi yang sebenarnya tetap dilakukan Controller di server.

// Mengembalikan objek { namaField: 'pesan' }; kosong berarti valid.
function validasiMahasiswa({ nim, nama, prodi, foto }) {
    const err = {};

    if (!nim) err.nim = 'NIM wajib diisi';
    else if (!/^\d{9}$/.test(nim)) err.nim = 'NIM harus terdiri dari tepat 9 digit angka';

    if (!nama) err.nama = 'Nama wajib diisi';
    else if (nama.length > 100) err.nama = 'Nama maksimal 100 karakter';

    if (!prodi) err.prodi = 'Program studi wajib diisi';
    else if (prodi.length > 50) err.prodi = 'Program studi maksimal 50 karakter';

    // Foto bersifat opsional, tetapi bila dipilih harus JPG/PNG dan <= 2 MB.
    if (foto) {
        const ok = ['image/jpeg', 'image/png'].includes(foto.type);
        if (!ok) err.foto = 'Foto harus berformat JPG atau PNG';
        else if (foto.size > 2 * 1024 * 1024) err.foto = 'Ukuran foto maksimal 2MB';
    }
    return err;
}

function bersihkanError(form) {
    form.querySelectorAll('.err').forEach((el) => { el.textContent = ''; });
    form.querySelectorAll('.invalid').forEach((el) => el.classList.remove('invalid'));
}

// Menampilkan pesan di bawah masing-masing input (dari View maupun dari server).
function tampilkanError(form, errors) {
    let pertama = null;
    Object.entries(errors).forEach(([field, pesan]) => {
        const teks = Array.isArray(pesan) ? pesan.join('; ') : pesan;
        const wadah = form.querySelector(`.err[data-for="${field}"]`);
        const input = form.querySelector(`#${field}`);
        if (wadah) wadah.textContent = teks;
        if (input) {
            input.classList.add('invalid');
            pertama = pertama || input;
        }
    });
    if (pertama) pertama.focus();
}
