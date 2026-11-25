// Langkah 2 (Acara 27): View edit data.
if (!isAdmin) {
    setFlash('Akses ditolak, hanya admin yang boleh mengubah data', 'error');
    window.location.replace('dashboard.html');
}

const form = document.querySelector('#form-mhs');
const btnSimpan = document.querySelector('#btn-simpan');
const id = new URLSearchParams(window.location.search).get('id');

if (!id || !/^[1-9]\d*$/.test(id)) {
    setFlash('ID data tidak valid', 'error');
    window.location.replace('dashboard.html');
}

// Memuat data lama ke form (ditangani MahasiswaController::index() dengan ?id=).
async function muatDataUntukEdit() {
    btnSimpan.textContent = 'Memuat data...';
    try {
        const res = await fetchWithRetry(`${BASE_URL}/mahasiswa?id=${encodeURIComponent(id)}`);
        const hasil = await res.json();
        if (!res.ok || !hasil.success) {
            throw new Error(hasil.message || `HTTP ${res.status}`);
        }
        form.nim.value = hasil.data.nim;
        form.nama.value = hasil.data.nama;
        form.prodi.value = hasil.data.prodi;

        if (hasil.data.foto) {
            const img = document.createElement('img');
            img.className = 'foto-lama';
            img.alt = `Foto ${hasil.data.nama}`;
            img.src = `${BASE_URL}/uploads/${encodeURIComponent(hasil.data.foto)}`;
            document.querySelector('#foto-sekarang').appendChild(img);
        }
        btnSimpan.disabled = false;
        btnSimpan.textContent = 'Simpan Perubahan';
    } catch (error) {
        console.error(error);
        setFlash(`Gagal memuat data: ${error.message}`, 'error');
        window.location.replace('dashboard.html');
    }
}

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    bersihkanError(form);

    const nilai = {
        nim: form.nim.value.trim(),
        nama: form.nama.value.trim(),
        prodi: form.prodi.value.trim(),
        foto: form.foto.files[0] || null,
    };

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
        // PHP tidak membaca multipart pada method PUT, jadi dikirim sebagai
        // POST + _method=PUT; Router meneruskannya ke MahasiswaController::update($id).
        const { status, hasil } = await api(`/mahasiswa?id=${id}&_method=PUT`, { method: 'POST', body });
        if (hasil.success) {
            setFlash(hasil.message, 'success');
            window.location.href = 'dashboard.html';
            return;
        }
        tampilkanError(form, hasil.errors || {});
        notif(pesanGagal(status, hasil), 'error');
    } catch (error) {
        console.error(error);
        notif('Tidak dapat menghubungi server API.', 'error');
    } finally {
        btnSimpan.disabled = false;
        btnSimpan.textContent = 'Simpan Perubahan';
    }
});

muatDataUntukEdit();
