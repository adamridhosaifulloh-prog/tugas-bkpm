// Dashboard admin (Acara 25-27). Utilitas (BASE_URL, token, role, api, logout, notif)
// berasal dari notifikasi.js dan common.js yang dimuat lebih dulu.
const tbody = document.querySelector('#tabel-mahasiswa tbody');

function barisPesan(teks, isError = false) {
    tbody.innerHTML = '';
    const tr = document.createElement('tr');
    tr.className = 'status' + (isError ? ' error' : '');
    const td = document.createElement('td');
    td.colSpan = isAdmin ? 5 : 4;
    td.textContent = teks;
    tr.appendChild(td);
    tbody.appendChild(tr);
}

// Role user hanya melihat data; kontrol admin disembunyikan (proteksi utama tetap di backend).
if (!isAdmin) {
    document.querySelectorAll('.aksi-admin').forEach((el) => { el.hidden = true; });
}
document.querySelector('#info-user').textContent = `Masuk sebagai: ${role || '-'}`;
document.querySelector('#btn-logout').addEventListener('click', logout);

async function muatData() {
    barisPesan('Memuat data...');
    try {
        const res = await fetch(`${BASE_URL}/mahasiswa`);
        const hasil = await res.json();
        if (!res.ok || !hasil.success) {
            throw new Error(hasil.message || `HTTP ${res.status}`);
        }

        const data = Array.isArray(hasil.data) ? hasil.data : [];
        document.querySelector('#total-mahasiswa').textContent = data.length;

        if (data.length === 0) {
            barisPesan('Belum ada data');
            return;
        }

        tbody.innerHTML = '';
        data.forEach((mhs) => {
            const tr = document.createElement('tr');

            const tdFoto = document.createElement('td');
            if (mhs.foto) {
                const img = document.createElement('img');
                img.className = 'foto';
                img.alt = `Foto ${mhs.nama}`;
                img.src = `${BASE_URL}/uploads/${encodeURIComponent(mhs.foto)}`;
                tdFoto.appendChild(img);
            } else {
                tdFoto.className = 'tanpa-foto';
                tdFoto.textContent = 'Tidak ada';
            }
            tr.appendChild(tdFoto);

            [mhs.nim, mhs.nama, mhs.prodi].forEach((nilai) => {
                const td = document.createElement('td');
                td.textContent = nilai;
                tr.appendChild(td);
            });

            if (isAdmin) {
                const tdAksi = document.createElement('td');
                // Langkah 2: tombol Ubah membuka form_edit.html?id=...
                const aUbah = document.createElement('a');
                aUbah.className = 'btn ubah';
                aUbah.href = `form_edit.html?id=${encodeURIComponent(mhs.id)}`;
                aUbah.textContent = 'Ubah';
                // Langkah 3: tombol Hapus
                const btnHapus = document.createElement('button');
                btnHapus.type = 'button';
                btnHapus.className = 'hapus';
                btnHapus.textContent = 'Hapus';
                btnHapus.style.marginLeft = '6px';
                btnHapus.addEventListener('click', () => hapusData(mhs));
                tdAksi.append(aUbah, btnHapus);
                tr.appendChild(tdAksi);
            }
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error('Gagal memuat data:', error);
        document.querySelector('#total-mahasiswa').textContent = '-';
        barisPesan('Gagal memuat data. Periksa koneksi atau server API.', true);
        notif('Gagal memuat data. Periksa koneksi atau server API.', 'error');
    }
}

// Langkah 3: hapus data + notifikasi berhasil/gagal (Tugas Mandiri 1).
async function hapusData(mhs) {
    if (!confirm(`Hapus data ${mhs.nama} (${mhs.nim})?`)) return;
    try {
        const { status, hasil } = await api(`/mahasiswa?id=${mhs.id}`, { method: 'DELETE' });
        if (hasil.success) {
            notif(hasil.message, 'success');
            muatData(); // tabel diperbarui tanpa reload penuh
        } else {
            notif(pesanGagal(status, hasil), 'error');
        }
    } catch (error) {
        console.error(error);
        notif('Tidak dapat menghubungi server API.', 'error');
    }
}

tampilkanFlash(); // notifikasi dari form_tambah / form_edit
muatData();
