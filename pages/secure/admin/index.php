<?php
require_once __DIR__ . '/../../../infra/middlewares/middleware-user.php';
@require_once __DIR__ . '/../../../helpers/session.php';
include_once __DIR__ . '/../../../templates/header.php';

$user = user();
$title = '- App';
?>

<main>
    <header class="pb-3 mb-4 border-bottom">
        <a href="/" class="d-flex align-items-center text-dark text-decoration-none"><img
                    src="/crud/assets/images/logo-estg.svg" alt="ESTG" class="mw-100"></a>
    </header>
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