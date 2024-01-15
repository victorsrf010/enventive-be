<?php
require_once __DIR__ . '../../../infra/middlewares/middleware-user.php';
@require_once __DIR__ . '/../../helpers/session.php';
include_once __DIR__ . '../../../templates/header.php';

$user = user();
$title = '- App';
?>

<main>
    <header class="pb-3 mb-4 border-bottom">
        <div class='d-flex justify-content-between align-items-center'>
            <a href="../../index.html" style="text-decoration: none;">
                <h1 class='logo' style="font-size: 2em; color: black; font-weight: bold;">
                    event<span style="color: orange;">ive</span>
                </h1>
            </a>
            <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarSupportedContent' aria-controls='navbarSupportedContent' aria-expanded='false' aria-label='Toggle navigation'>
                <span class='navbar-toggler-icon'></span>
            </button>
        </div>
    </header>

    <div class="p-5 mb-4 bg-body-tertiary rounded-3">
        <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">Hello,
                <?= $user['name'] ?? null ?>!
            </h1>
            <p class="col-md-8 fs-4">Ready for today?</p>
            <div class="d-flex justify-content">
                <form action="/crud/controllers/auth/signin.php" method="post">
                    <button class="btn btn-danger btn-lg px-4" type="submit" name="user" value="logout">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <div class="row align-items-md-stretch">
        <div class="col-md-6">
            <div class="h-100 p-5 text-bg-dark rounded-3">
                <h2>Profile</h2>
                <a href="/crud/pages/secure/user/profile.php"><button class="btn btn-outline-light px-5"
                        type="button">Change</button></a>
            </div>
        </div>

        <?php
        if (isAuthenticated()) {
            if ($user['administrator']) {
                echo '<div class="col-md-6">
                    <div class="h-100 p-5 bg-body-tertiary border rounded-3">
                        <h2>Admin</h2>
                        <a href="./admin"><button class="btn btn-outline-success" type="button">Admin</button></a>
                    </div>
                </div>';
            } else {
                echo '<div class="col-md-6">
                    <div class="h-100 p-5 bg-body-tertiary border rounded-3">
                        <h2>Events</h2>
                        <a href="user/events/"><button class="btn btn-outline-success" type="button">Events</button></a>
                    </div>
                </div>';
            }
        }
        ?>
    </div>
</main>

<?php
include_once __DIR__ . '../../../templates/footer.php';
?>