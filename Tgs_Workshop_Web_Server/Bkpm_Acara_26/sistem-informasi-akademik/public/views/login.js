// Alamat API diturunkan dari lokasi halaman (.../public/views/login.html -> .../public).
const BASE_URL = window.location.pathname.replace(/\/views\/[^/]*$/, '');

const form = document.querySelector('#form-login');
const pesan = document.querySelector('#pesan');
const btnLogin = document.querySelector('#btn-login');

// Pengguna yang sudah login langsung diarahkan ke dashboard.
if (localStorage.getItem('token')) {
    window.location.replace('dashboard.html');
}

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    pesan.textContent = '';
    btnLogin.disabled = true;
    btnLogin.textContent = 'Memproses...';

    try {
        const res = await fetch(`${BASE_URL}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                username: document.querySelector('#username').value,
                password: document.querySelector('#password').value,
            }),
        });
        const hasil = await res.json();

        if (hasil.success) {
            localStorage.setItem('token', hasil.data.token);
            localStorage.setItem('role', hasil.data.role);
            window.location.href = 'dashboard.html';
        } else {
            pesan.textContent = hasil.message;
        }
    } catch (error) {
        console.error('Login gagal:', error);
        pesan.textContent = 'Tidak dapat menghubungi server API.';
    } finally {
        btnLogin.disabled = false;
        btnLogin.textContent = 'Masuk';
    }
});
