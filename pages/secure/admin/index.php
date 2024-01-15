<?php
require_once __DIR__ . '/../../../infra/middlewares/middleware-user.php';
require_once __DIR__ . '/../../../infra/middlewares/middleware-administrator.php';
@require_once __DIR__ . '/../../../helpers/session.php';
include_once __DIR__ . '/../../../templates/header.php';

$user = user();
$title = '- App';
?>

<main>
    <header class="pb-3 mb-4 border-bottom">
        <div class='d-flex justify-content-between align-items-center'>
            <a style="text-decoration: none;">
                <h1 class='logo' style="font-size: 2em; color: black; font-weight: bold;">
                    event<span style="color: orange;">ive</span>
                </h1>
            </a>
            <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarSupportedContent' aria-controls='navbarSupportedContent' aria-expanded='false' aria-label='Toggle navigation'>
                <span class='navbar-toggler-icon'></span>
            </button>
        </div>
    </header>
    <section class="py-4">
        <div class="d-flex justify-content">
            <a href="/crud/pages/secure/">
                <button class="btn btn-secondary px-5 me-2">Back</button>
            </a>
        </div>
    </section>
    <div class="row align-items-md-stretch">
        <?php
        if (isAuthenticated() && $user['administrator']) {
            echo '<div class="col-md-6">
            <div class="h-100 p-5 text-bg-dark rounded-3">
                <h2>Events</h2>
                <a href="/crud/pages/secure/admin/events/"><button class="btn btn-outline-light px-5"
                                                                      type="button">See more</button></a>
            </div>
        </div>';
        }
        ?>

        <?php
        if (isAuthenticated() && $user['administrator']) {
            echo '<div class="col-md-6">
                <div class="h-100 p-5 bg-body-tertiary border rounded-3">
                    <h2>Users</h2>
                    <a href="/crud/pages/secure/admin/users/"><button class="btn btn-outline-success" type="button">See more</button></a>
                </div>
            </div>';
        }
        ?>
    </div>
</main>

<?php
include_once __DIR__ . '/../../../templates/footer.php';
?>