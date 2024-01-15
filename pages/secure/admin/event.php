<?php
require_once __DIR__ . '/../../../infra/middlewares/middleware-administrator.php';
require_once __DIR__ . '/../../../templates/header.php';
require_once __DIR__ . '/../../../infra/repositories/categoryRepository.php';
require_once __DIR__ . '/../../../infra/repositories/attachmentsRepository.php';
require_once __DIR__ . '/../../../infra/repositories/eventRepository.php';

$categories = getAllCategories();
$attachments = getAttachmentsByEventId(isset($_REQUEST['id']) ? $_REQUEST['id'] : null);
$title = ' - Event';
if (isset($_REQUEST['id'])) {
    $event = getEventById($_REQUEST['id']);
    // Now $event contains the event data which can be used to repopulate the form
}
?>

<main>
    <section class="py-4">
        <a href="/crud/pages/secure/admin/events/index.php">
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
        <form enctype="multipart/form-data" action="/crud/controllers/admin/event.php" method="post"
              class="form-control py-3">
            <div class="input-group mb-3">
                <span class="input-group-text">Name</span>
                <input type="text" class="form-control" name="name" maxlength="255" size="255" required
                       value="<?= isset($event['name']) ? $event['name'] : '' ?>">
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text">Description</span>
                <textarea class="form-control" name="description" rows="3"
                          required><?= isset($event['description']) ? $event['description'] : '' ?></textarea>
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text">Event Date</span>
                <input type="datetime-local" class="form-control" name="event_at" required
                       value="<?= isset($event['event_at']) ? date("Y-m-d\TH:i", strtotime($event['event_at'])) : '' ?>">
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text">Location</span>
                <input type="text" class="form-control" name="location" maxlength="255"
                       value="<?= isset($event['location']) ? $event['location'] : '' ?>" required>
            </div>
            <div class="input-group mb-3">
                <label class="input-group-text" for="category">Category</label>
                <select class="form-select" id="category" name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= isset($event['category_id']) && $event['category_id'] == $category['id'] ? 'selected' : '' ?>>
                            <?= $category['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="input-group mb-3">
                <input type="file" class="form-control" name="attachment" id="fileInput" style="display:none;"
                       onchange="updateFileName(this)">
                <label class="input-group-text rounded-start" for="fileInput">Add attachment</label>
                <input type="text" class="form-control rounded-end" readonly placeholder="No file selected"
                       id="file-chosen">
            </div>

            <script>
                function updateFileName(input) {
                    var fileName = input.files[0].name;
                    document.getElementById('file-chosen').value = fileName;
                }
            </script>

            <div class="d-grid col-4 mx-auto">
                <input type="hidden" name="id" value="<?= isset($event['id']) ? $event['id'] : '' ?>">
                <button type="submit" class="btn btn-success" name="event"
                        value="<?= isset($event['id']) ? 'update' : 'create' ?>">
                    <?= isset($event['id']) ? 'Update' : 'Create' ?>
                </button>
            </div>
        </form>
    </section>

    <section class="pb-4">
        <?php if (!empty($attachments)): // Check if attachments array is not empty ?>
            <form enctype="multipart/form-data" action="/crud/controllers/admin/event.php" method="post"
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
require_once __DIR__ . '/../../../templates/footer.php';
?>

