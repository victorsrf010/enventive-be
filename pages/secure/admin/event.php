<?php
require_once __DIR__ . '/../../../infra/middlewares/middleware-administrator.php';
require_once __DIR__ . '/../../../templates/header.php';
require_once __DIR__ . '/../../../infra/repositories/categoryRepository.php';

$categories = getAllCategories();
$title = ' - Event';
?>

<main>
    <section class="py-4">
        <a href="/crud/pages/secure/admin/index.php">
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
        <form enctype="multipart/form-data" action="/crud/controllers/admin/event.php" method="post" class="form-control py-3">
            <div class="input-group mb-3">
                <span class="input-group-text">Name</span>
                <input type="text" class="form-control" name="name" maxlength="255" size="255" required
                       value="<?= isset($_REQUEST['name']) ? $_REQUEST['name'] : null ?>">
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text">Description</span>
                <textarea class="form-control" name="description" rows="3" required><?= isset($_REQUEST['description']) ? $_REQUEST['description'] : null ?></textarea>
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
                <button type="submit" class="btn btn-success" name="event" value="<?= isset($_REQUEST['action']) && $_REQUEST['action'] == 'update' ? 'update' : 'create' ?>">
                    <?= isset($_REQUEST['action']) && $_REQUEST['action'] == 'update' ? 'Update' : 'Create' ?>
                </button>
            </div>
        </form>
    </section>
</main>

<?php
require_once __DIR__ . '/../../../templates/footer.php';
?>

