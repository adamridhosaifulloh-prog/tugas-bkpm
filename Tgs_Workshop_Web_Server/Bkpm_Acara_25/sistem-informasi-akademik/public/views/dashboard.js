// Alamat API diturunkan dari lokasi halaman (.../public/views/dashboard.html -> .../public).
const BASE_URL = window.location.pathname.replace(/\/views\/[^/]*$/, '');

const token = localStorage.getItem('token');
const role = localStorage.getItem('role');
const isAdmin = role === 'admin';

const tbody = document.querySelector('#tabel-mahasiswa tbody');
const pesan = document.querySelector('#pesan');
const formMhs = document.querySelector('#form-mhs');
const inputNim = document.querySelector('#nim');
const inputNama = document.querySelector('#nama');
const inputProdi = document.querySelector('#prodi');
let idDiubah = null; // null = mode tambah, angka = mode ubah

// ---------- Utilitas ----------
function tampilkanStatus(teks, tipe = '') {
    pesan.textContent = teks;
    pesan.className = tipe;
}

function barisPesan(teks, isError = false) {
    tbody.innerHTML = '';
    const tr = document.createElement('tr');
    tr.className = 'status' + (isError ? ' error' : '');
    const td = document.createElement('td');
    td.colSpan = isAdmin ? 4 : 3;
    td.textContent = teks;
    tr.appendChild(td);
    tbody.appendChild(tr);
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    window.location.href = 'login.html';
}

// Pembungkus fetch: menambahkan Bearer token dan menangani token tidak valid (401).
async function api(path, options = {}) {
    const res = await fetch(`${BASE_URL}${path}`, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`,
        },
    });
    const hasil = await res.json();

    if (res.status === 401) {
        logout(); // token kedaluwarsa atau tidak valid
    }
    return { status: res.status, hasil };
}

// ---------- Tugas Mandiri 2: sembunyikan kontrol admin untuk role user ----------
if (!isAdmin) {
    document.querySelectorAll('.aksi-admin').forEach((el) => { el.hidden = true; });
}
document.querySelector('#info-user').textContent = `Masuk sebagai: ${role || '-'}`;

// ---------- Tugas Mandiri 1: Logout ----------
document.querySelector('#btn-logout').addEventListener('click', logout);

// ---------- Menampilkan data dan ringkasan ----------
async function muatData() {
    barisPesan('Memuat data...');
    try {
        const res = await fetch(`${BASE_URL}/mahasiswa`);
        const hasil = await res.json();
        if (!res.ok || !hasil.success) {
            throw new Error(hasil.message || `HTTP ${res.status}`);
        }

        const data = Array.isArray(hasil.data) ? hasil.data : [];
        // Langkah 5: ringkasan jumlah total mahasiswa.
        document.querySelector('#total-mahasiswa').textContent = data.length;

        if (data.length === 0) {
            barisPesan('Belum ada data');
            return;
        }

        tbody.innerHTML = '';
        data.forEach((mhs) => {
            const tr = document.createElement('tr');
            [mhs.nim, mhs.nama, mhs.prodi].forEach((nilai) => {
                const td = document.createElement('td');
                td.textContent = nilai;
                tr.appendChild(td);
            });

            if (isAdmin) {
                const tdAksi = document.createElement('td');
                const btnUbah = document.createElement('button');
                btnUbah.type = 'button';
                btnUbah.className = 'ubah';
                btnUbah.textContent = 'Ubah';
                btnUbah.addEventListener('click', () => mulaiUbah(mhs));
                const btnHapus = document.createElement('button');
                btnHapus.type = 'button';
                btnHapus.className = 'hapus';
                btnHapus.textContent = 'Hapus';
                btnHapus.style.marginLeft = '6px';
                btnHapus.addEventListener('click', () => hapus(mhs));
                tdAksi.append(btnUbah, btnHapus);
                tr.appendChild(tdAksi);
            }
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error('Gagal memuat data:', error);
        document.querySelector('#total-mahasiswa').textContent = '-';
        barisPesan('Gagal memuat data. Periksa koneksi atau server API.', true);
    }
}

// ---------- CRUD (hanya admin; backend tetap memeriksa lewat Auth::requireAdmin) ----------
function bukaForm(mhs = null) {
    idDiubah = mhs ? mhs.id : null;
    inputNim.value = mhs ? mhs.nim : '';
    inputNama.value = mhs ? mhs.nama : '';
    inputProdi.value = mhs ? mhs.prodi : '';
    formMhs.hidden = false;
    inputNim.focus();
}

function tutupForm() {
    formMhs.hidden = true;
    idDiubah = null;
    formMhs.reset();
}

function mulaiUbah(mhs) { bukaForm(mhs); }

document.querySelector('#btn-tambah').addEventListener('click', () => bukaForm());
document.querySelector('#btn-batal').addEventListener('click', tutupForm);

// Menyusun pesan dari response gagal (termasuk rincian errors per field).
function pesanGagal(status, hasil) {
    const rincian = Object.values(hasil.errors || {}).flat().join('; ');
    return `Gagal (${status}): ${hasil.message}${rincian ? ' - ' + rincian : ''}`;
}

formMhs.addEventListener('submit', async (e) => {
    e.preventDefault();
    const body = JSON.stringify({
        nim: inputNim.value.trim(),
        nama: inputNama.value.trim(),
        prodi: inputProdi.value.trim(),
    });
    const mode = idDiubah === null
        ? { path: '/mahasiswa', method: 'POST' }
        : { path: `/mahasiswa?id=${idDiubah}`, method: 'PUT' };

    try {
        const { status, hasil } = await api(mode.path, { method: mode.method, body });
        if (hasil.success) {
            tampilkanStatus(hasil.message, 'ok');
            tutupForm();
            muatData();
        } else {
            tampilkanStatus(pesanGagal(status, hasil), 'error');
        }
    } catch (error) {
        console.error(error);
        tampilkanStatus('Tidak dapat menghubungi server API.', 'error');
    }
});

async function hapus(mhs) {
    if (!confirm(`Hapus data ${mhs.nama} (${mhs.nim})?`)) return;
    try {
        const { status, hasil } = await api(`/mahasiswa?id=${mhs.id}`, { method: 'DELETE' });
        if (hasil.success) {
            tampilkanStatus(hasil.message, 'ok');
            muatData();
        } else {
            tampilkanStatus(pesanGagal(status, hasil), 'error');
        }
    } catch (error) {
        console.error(error);
        tampilkanStatus('Tidak dapat menghubungi server API.', 'error');
    }
}

muatData();
