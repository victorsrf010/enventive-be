<?php
require_once __DIR__ . '/../../../../infra/middlewares/middleware-user.php';
require_once __DIR__ . '/../../../../templates/header.php';
require_once __DIR__ . '/../../../../infra/repositories/categoryRepository.php';
require_once __DIR__ . '/../../../../infra/repositories/attachmentsRepository.php';
require_once __DIR__ . '/../../../../infra/repositories/eventRepository.php';
require_once __DIR__ . '/../../../../infra/repositories/userEventRepository.php';

$categories = getAllCategories();
$eventId = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$invitedUsers = getInvitedUsers($eventId);

if (isset($_GET['id'])) {
    $eventId = $_GET['id'];
    $event = getEventById($eventId);
    $attachments = getAttachmentsByEventId($eventId);
} else {
    $attachments = [];
    $event = null;
}

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
            <div class="input-group mb-3">
                <label class="input-group-text" for="category">Category</label>
                <select class="form-select" id="category" name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (isset($event['id'])): ?>
                <div class="input-group mb-3">
                    <input type="file" class="form-control" name="attachment[]" id="fileInput" multiple style="display:none;" onchange="updateFileNames(this)">
                    <label class="input-group-text rounded-start" for="fileInput">Add attachment</label>
                    <input type="text" class="form-control rounded-end" readonly placeholder="No file selected" id="file-chosen">
                </div>

                <script>
                    function updateFileNames(input) {
                        var fileNames = Array.from(input.files).map(function(file) { return file.name; }).join(', ');
                        document.getElementById('file-chosen').value = fileNames;
                    }
                </script>
            <?php endif; ?>

            <div class="d-grid col-4 mx-auto">
                <input type="hidden" name="id" value="<?= isset($_REQUEST['id']) ? $_REQUEST['id'] : null ?>">
                <button type="submit" class="btn btn-success" name="event"
                        value="<?= isset($_REQUEST['action']) && $_REQUEST['action'] == 'update' ? 'update' : 'create' ?>">
                    <?= isset($_REQUEST['action']) && $_REQUEST['action'] == 'update' ? 'Update' : 'Create' ?>
                </button>
            </div>
        </form>
    </section>

    <?php if ($eventId): ?>
        <section class='pb-4'>

            <div class="d-flex justify-content">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inviteModal">
                    Invite
                </button>
            </div>


            <div class="modal fade" id="inviteModal" tabindex="-1" aria-labelledby="inviteModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="inviteModalLabel">Invite a Friend</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="../../../../controllers/userEvent.php" method="post">
                                <input type="hidden" name="event_id" value="<?= $eventId ?>">
                                <input type="hidden" name="return_url"
                                       value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

                                <div class="mb-3">
                                    <label for="inviteEmail" class="form-label">Friend's Email Address</label>
                                    <input type="email" class="form-control" id="inviteEmail" name="invite_email"
                                           required>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary" name="event" value="invite">Invite
                                    </button>
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
                    <?php foreach ($invitedUsers as $user): ?>
                        <tr>
                            <th scope="row">
                                <?= $user['name'] ?>
                            </th>
                            <td>
                                <?= $user['email'] ?>
                            </td>
                            <td>
                                <div class="d-flex justify-content">
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#remove<?= $user['id'] ?>">
                                        Remove
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="remove<?= $user['id'] ?>" tabindex="-1"
                             aria-labelledby="removeModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="removeModalLabel">Remove User</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to remove <?= $user['name'] ?> from the event?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel
                                        </button>
                                        <a href="/crud/controllers/userEvent.php?<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>&event=removeInvite&user_id=<?= $user['id'] ?>&event_id=<?= $eventId ?>">
                                            <button type="button" class="btn btn-danger">Confirm</button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>

    <section class="pb-4">
        <?php if (!empty($attachments)): // Check if attachments array is not empty ?>
            <form enctype="multipart/form-data" action="/crud/controllers/auth/event.php" method="post"
                  class="form-control py-3">

                <section>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-secondary">
                            <tr>
                                <th scope="col">File Name</th>
                                <th scope="col">File Type</th>
                                <th scope="col">Manage</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($attachments as $attachment) {
                                $event = getEventById($attachment['event_id']);
                                ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars(basename($attachment['file_path'])) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($attachment['file_type']) ?>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content">
                                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#deleteAttachment<?= $attachment['id'] ?>">Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <div class="modal fade" id="deleteAttachment<?= $attachment['id'] ?>" tabindex="-1"
                                     aria-labelledby="deleteAttachmentLabel"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteAttachmentLabel">Delete
                                                    Attachment</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this attachment?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                                <a href="/crud/controllers/attachments.php?<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>&attachment=delete&id=<?= $attachment['id'] ?>">
                                                    <button type="button" class="btn btn-danger">Confirm</button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="d-grid col-4 mx-auto">
                    <input type="hidden" name="id" value="<?= isset($_REQUEST['id']) ? $_REQUEST['id'] : null ?>">
                </div>
            </form>
        <?php else: ?>

        <?php endif; ?>
    </section>
</main>

<?php
require_once __DIR__ . '/../../../../templates/footer.php';
?>
