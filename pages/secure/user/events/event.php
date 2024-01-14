<?php
require_once __DIR__ . '/../../../../infra/middlewares/middleware-user.php';
require_once __DIR__ . '/../../../../templates/header.php';
require_once __DIR__ . '/../../../../infra/repositories/categoryRepository.php';
require_once __DIR__ . '/../../../../infra/repositories/userEventRepository.php';

$categories = getAllCategories();
$eventId = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$invitedUsers = getInvitedUsers($eventId);
$title = ' - Event';
?>

<main>
    <section class="py-4">
        <a href="../events">
            <button type="button" class="btn btn-secondary px-5">Back</button>
        </a>
    </section>
    <section>
        <?php
        if (isset($_SESSION['success'])):
            ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success'] ?><br>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
            unset($_SESSION['success']);
        endif;

        if (isset($_SESSION['errors'])):
            ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php
                foreach ($_SESSION['errors'] as $error):
                    echo $error . '<br>';
                endforeach;
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
            unset($_SESSION['errors']);
        endif;
        ?>
    </section>
    <section class="pb-4">
        <form enctype="multipart/form-data" action="/crud/controllers/auth/event.php" method="post"
              class="form-control py-3">
            <div class="input-group mb-3">
                <span class="input-group-text">Name</span>
                <input type="text" class="form-control" name="name" maxlength="255" size="255" required
                       value="<?= isset($_REQUEST['name']) ? $_REQUEST['name'] : null ?>">
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text">Description</span>
                <textarea class="form-control" name="description" rows="3"
                          required><?= isset($_REQUEST['description']) ? $_REQUEST['description'] : null ?></textarea>
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text">Event Date</span>
                <input type="datetime-local" class="form-control" name="event_at" required
                       value="<?= isset($_REQUEST['event_at']) ? date("Y-m-d\TH:i", strtotime($_REQUEST['event_at'])) : null ?>">
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text">Location</span>
                <input type="text" class="form-control" name="location" maxlength="255"
                       value="<?= isset($_REQUEST['location']) ? $_REQUEST['location'] : null ?>" required>
            </div>
            <!-- Add a dropdown for selecting categories -->
            <div class="input-group mb-3">
                <label class="input-group-text" for="category">Category</label>
                <select class="form-select" id="category" name="category_id" required>
                    <!-- Loop through categories and generate options -->
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-grid col-4 mx-auto">
                <input type="hidden" name="id" value="<?= isset($_REQUEST['id']) ? $_REQUEST['id'] : null ?>">
                <button type="submit" class="btn btn-success" name="event"
                        value="<?= isset($_REQUEST['action']) && $_REQUEST['action'] == 'update' ? 'update' : 'create' ?>">
                    <?= isset($_REQUEST['action']) && $_REQUEST['action'] == 'update' ? 'Update' : 'Create' ?>
                </button>
            </div>
        </form>
    </section>

    <section class='pb-4'>
        <!-- Button to trigger the modal -->
        <div class="d-flex justify-content">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inviteModal">
                Invite
            </button>
        </div>

        <!-- Modal for inviting users -->
        <div class="modal fade" id="inviteModal" tabindex="-1" aria-labelledby="inviteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="inviteModalLabel">Invite a Friend</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../../../../controllers/userEvent.php" method="post">
                            <input type="hidden" name="event_id" value="<?= $eventId ?>">
                            <input type="hidden" name="return_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

                            <div class="mb-3">
                                <label for="inviteEmail" class="form-label">Friend's Email Address</label>
                                <input type="email" class="form-control" id="inviteEmail" name="invite_email" required>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" name="event" value="invite">Invite</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="table-responsive">
            <table class="table">
                <thead class="table-secondary">
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Manage</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($eventId) {
                    foreach ($invitedUsers as $user) {
                        ?>
                        <tr>
                            <th scope="row">
                                <?= $user['name'] ?>
                            </th>
                            <th scope="row">
                                <?= $user['email'] ?>
                            </th>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </section>

</main>

<?php
require_once __DIR__ . '/../../../../templates/footer.php';
?>

