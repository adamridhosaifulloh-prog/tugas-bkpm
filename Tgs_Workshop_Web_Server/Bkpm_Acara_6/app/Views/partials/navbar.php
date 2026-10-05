<nav class="navbar navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand" href="<?= BASE_URL ?>/dashboard">
            Sistem Akademik
        </a>

        <?php if (isset($_SESSION['user'])): ?>

            <div>
                <a
                    href="<?= BASE_URL ?>/dashboard"
                    class="btn btn-outline-light btn-sm"
                >
                    Dashboard
                </a>

                <a
                    href="<?= BASE_URL ?>/mahasiswa"
                    class="btn btn-outline-light btn-sm"
                >
                    Mahasiswa
                </a>

                <form
                    action="<?= BASE_URL ?>/logout"
                    method="POST"
                    style="display:inline;"
                >
                    <button
                        type="submit"
                        class="btn btn-danger btn-sm"
                    >
                        Logout
                    </button>
                </form>
            </div>

        <?php endif; ?>

    </div>
</nav>