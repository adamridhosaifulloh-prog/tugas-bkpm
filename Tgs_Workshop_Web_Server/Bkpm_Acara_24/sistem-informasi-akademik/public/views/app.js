// Alamat API diturunkan dari lokasi halaman ini (.../public/views/index.html -> .../public),
// sehingga tetap benar apa pun nama folder proyeknya.
// Contoh nilai: http://localhost/sistem-informasi-akademik/public
const BASE_URL = window.location.pathname.replace(/\/views\/[^/]*$/, '');

const tbody = document.querySelector('#tabel-mahasiswa tbody');

// Menampilkan satu baris pesan (memuat / kosong / error) pada tabel.
function tampilkanPesan(teks, isError = false) {
    tbody.innerHTML = '';
    const tr = document.createElement('tr');
    tr.className = 'status' + (isError ? ' error' : '');
    const td = document.createElement('td');
    td.colSpan = 3;
    td.textContent = teks;
    tr.appendChild(td);
    tbody.appendChild(tr);
}

async function muatDataMahasiswa() {
    // Tugas Mandiri 1: indikator sebelum data berhasil diambil.
    tampilkanPesan('Memuat data...');

    try {
        const response = await fetch(`${BASE_URL}/mahasiswa`);
        const hasil = await response.json();
        hasil.data = []; // SEMENTARA untuk uji data kosong
    
        if (!response.ok || !hasil.success) {
            throw new Error(hasil.message || `HTTP ${response.status}`);
        }

        // Tugas Mandiri 2: kondisi data kosong.
        if (!Array.isArray(hasil.data) || hasil.data.length === 0) {
            tampilkanPesan('Belum ada data');
            return;
        }

        tbody.innerHTML = '';
        hasil.data.forEach((mhs) => {
            const tr = document.createElement('tr');
            [mhs.nim, mhs.nama, mhs.prodi].forEach((nilai) => {
                const td = document.createElement('td');
                td.textContent = nilai; // textContent mencegah XSS dari data API
                tr.appendChild(td);
            });
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error('Gagal memuat data:', error);
        tampilkanPesan('Gagal memuat data. Periksa koneksi atau server API.', true);
    }
}

document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
