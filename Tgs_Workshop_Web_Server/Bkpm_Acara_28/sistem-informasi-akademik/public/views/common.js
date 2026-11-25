// Utilitas bersama untuk halaman admin (dashboard, form_tambah, form_edit).
// Alamat API diturunkan dari lokasi halaman (.../public/views/x.html -> .../public).
const BASE_URL = window.location.pathname.replace(/\/views\/[^/]*$/, '');

const token = localStorage.getItem('token');
const role = localStorage.getItem('role');
const isAdmin = role === 'admin';

// Halaman admin wajib login.
if (!token) {
    window.location.replace('login.html');
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    window.location.href = 'login.html';
}

// Pembungkus fetch: Bearer token, tanpa Content-Type manual untuk FormData
// (browser mengisi boundary), dan penanganan token tidak valid (401).
async function api(path, options = {}) {
    const res = await fetchWithRetry(`${BASE_URL}${path}`, {
        ...options,
        headers: {
            ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
            Authorization: `Bearer ${token}`,
        },
    });

    let hasil;
    try {
        hasil = await res.json();
    } catch (e) {
        hasil = { success: false, message: `Respons server tidak valid (HTTP ${res.status})`, errors: {} };
    }

    if (res.status === 401) {
        logout();
    }
    return { status: res.status, hasil };
}

// Pesan gagal lengkap dengan rincian errors per field dari server.
function pesanGagal(status, hasil) {
    const rincian = Object.values(hasil.errors || {}).flat().join('; ');
    return `Gagal (${status}): ${hasil.message}${rincian ? ' - ' + rincian : ''}`;
}

// Notifikasi lintas halaman: disimpan sebelum redirect, ditampilkan di halaman tujuan.
function setFlash(pesan, tipe = 'success') {
    sessionStorage.setItem('flash', JSON.stringify({ pesan, tipe }));
}
function tampilkanFlash() {
    const mentah = sessionStorage.getItem('flash');
    if (!mentah) return;
    sessionStorage.removeItem('flash');
    try {
        const { pesan, tipe } = JSON.parse(mentah);
        notif(pesan, tipe);
    } catch (e) { /* abaikan flash rusak */ }
}
