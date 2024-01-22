<?php

require_once __DIR__ . '/../infra/repositories/attachmentsRepository.php';

if (isset($_GET['attachment'])) {
    if ($_GET['attachment'] == 'delete') {
        delete($_GET);
    }
}

function delete($req)
{
    $attachment = getAttachmentById($req['id']);
    $success = deleteAttachment($attachment['id']);

    if ($success) {
        $_SESSION['success'] = 'Attachment deleted successfully!';
    } else {
        $_SESSION['errors'][] = 'Error deleting attachment.';
    }

    // Redirect back to the same page
    header('location: ' . $_SERVER['HTTP_REFERER']);
}
