<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card">

                <div class="card-header text-center">
                    <h4>Login</h4>
                </div>

                <div class="card-body">

                    <?php if (isset($_SESSION['flash'])): ?>

                        <div class="alert alert-success">
                            <?= $_SESSION['flash']; ?>
                        </div>

                        <?php unset($_SESSION['flash']); ?>

                    <?php endif; ?>


                    <?php if (isset($_SESSION['flash_error'])): ?>

                        <div class="alert alert-danger">
                            <?= $_SESSION['flash_error']; ?>
                        </div>

                        <?php unset($_SESSION['flash_error']); ?>

                    <?php endif; ?>


                    <form
                        action="<?= BASE_URL ?>/login"
                        method="POST"
                    >

                        <div class="mb-3">

                            <label class="form-label">
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Login
                        </button>

                    </form>

                    <div class="mt-3 text-center">

                        <small>
                            Username: <b>admin</b><br>
                            Password: <b>12345</b>
                        </small>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>