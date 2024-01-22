<?php
require_once __DIR__ . '/../../../../../helpers/session.php';
require_once __DIR__ . '/../../../../../templates/header.php';
require_once __DIR__ . '/../../../../../infra/repositories/categoryRepository.php';
require_once __DIR__ . '/../../../../../infra/repositories/attachmentsRepository.php';
require_once __DIR__ . '/../../../../../infra/repositories/eventRepository.php';
require_once __DIR__ . '/../../../../../infra/repositories/userEventRepository.php';

$eventId = isset($_GET['id']) ? $_GET['id'] : null;
$currentUserId = userId();

if ($eventId) {
    $event = getEventById($eventId);

    if ($event && $event['created_by'] !== $currentUserId) {
        header("Location: ../");
    }

    $attachments = getAttachmentsByEventId($eventId);
} else {
    header("Location: ../");
    exit();
}

$title = ' - Event';
?>

<main>
    <section class="py-4">
        <a href="../index.php" class="btn btn-secondary px-5">Back</a>
    </section>

    <section class="pb-4">
        <!-- Display event details -->
        <h2><?= $event['name'] ?></h2>
        <p>Description: <?= $event['description'] ?></p>
        <p>Event Date: <?= date("F j, Y, g:i a", strtotime($event['event_at'])) ?></p>
        <p>Location: <?= $event['location'] ?></p>
        <p>Category:
            <?php
            $category = getCategoryById($event['category_id']);

            echo $category['name'];
            ?>
        </p>

        <?php if ($attachments): ?>
            <section>
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-secondary">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Manage</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($attachments as $attachment): ?>
                            <tr>
                                <td><?= htmlspecialchars(basename($attachment['file_path'])) ?></td>
                                <td>
                                    <a href="/crud/controllers/auth/event.php?event=download&attachment_id=<?= $attachment['id'] ?>">
                                        <button type="button" class="btn btn-primary me-2">Download</button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php else: ?>
            <tr>
                <td colspan="2" class="text-center">No attachments available.</td>
            </tr>
        <?php endif; ?>
    </section>
</main>

<?php
include_once __DIR__ . '/../../../../../templates/footer.php';
?>
