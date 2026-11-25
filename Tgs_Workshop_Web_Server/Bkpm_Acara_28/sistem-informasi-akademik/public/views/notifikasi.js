// Tugas Praktik Mandiri 1 (Acara 27): notifikasi visual (toast).
// Pemakaian: notif('Data berhasil disimpan', 'success') | 'error' | 'info'
(function () {
    const gaya = `
    #toast-wadah { position: fixed; top: 16px; right: 16px; z-index: 9999; display: flex;
        flex-direction: column; gap: 10px; max-width: min(380px, calc(100vw - 32px)); }
    .toast { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border-radius: 8px;
        color: #fff; font: 14px/1.4 Arial, sans-serif; box-shadow: 0 4px 14px rgba(0,0,0,.25);
        border-left: 6px solid rgba(0,0,0,.25); animation: toast-masuk .2s ease-out; }
    .toast.success { background: #2e7d32; }
    .toast.error   { background: #c62828; }
    .toast.info    { background: #1565c0; }
    .toast .ikon { font-weight: bold; }
    .toast .isi { flex: 1; word-break: break-word; }
    .toast button { background: transparent; border: 0; color: #fff; font-size: 18px; line-height: 1;
        cursor: pointer; padding: 0 2px; }
    .toast.keluar { opacity: 0; transform: translateX(20px); transition: all .25s; }
    @keyframes toast-masuk { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: none; } }`;

    const style = document.createElement('style');
    style.textContent = gaya;
    document.head.appendChild(style);

    let wadah = null;
    function pastikanWadah() {
        if (!wadah) {
            wadah = document.createElement('div');
            wadah.id = 'toast-wadah';
            wadah.setAttribute('aria-live', 'polite');
            document.body.appendChild(wadah);
        }
        return wadah;
    }

    window.notif = function (pesan, tipe = 'info', durasi = 4500) {
        const ikon = { success: 'OK', error: '!', info: 'i' }[tipe] || 'i';
        const el = document.createElement('div');
        el.className = `toast ${tipe}`;
        el.setAttribute('role', tipe === 'error' ? 'alert' : 'status');

        const i = document.createElement('span');
        i.className = 'ikon';
        i.textContent = ikon;
        const isi = document.createElement('span');
        isi.className = 'isi';
        isi.textContent = pesan; // textContent: aman dari XSS
        const tutup = document.createElement('button');
        tutup.type = 'button';
        tutup.setAttribute('aria-label', 'Tutup notifikasi');
        tutup.textContent = '\u00d7';

        const hilang = () => {
            el.classList.add('keluar');
            setTimeout(() => el.remove(), 250);
        };
        tutup.addEventListener('click', hilang);
        el.append(i, isi, tutup);
        pastikanWadah().appendChild(el);
        if (durasi > 0) setTimeout(hilang, durasi);
    };
})();
