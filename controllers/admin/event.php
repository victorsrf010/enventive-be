<?php

require_once __DIR__ . '/../../infra/repositories/eventRepository.php';
require_once __DIR__ . '/../../infra/repositories/attachmentsRepository.php';
require_once __DIR__ . '/../../helpers/validations/admin/validate-event.php';
require_once __DIR__ . '/../../helpers/session.php';

if (isset($_POST['event'])) {
    if ($_POST['event'] == 'create') {
        create($_POST);
    }

    if ($_POST['event'] == 'update') {
        update($_POST);
    }

    if ($_POST['event'] == 'delete') {
        delete($_POST);
    }
}

if (isset($_GET['event'])) {
    if ($_GET['event'] == 'update') {
        $event = getEventById($_GET['id']);
        $event['action'] = 'update';
        $params = '?' . http_build_query($event);
        header('location: /crud/pages/secure/admin/event.php' . $params);
    }

    if ($_GET['event'] == 'delete') {
        delete($_GET);
    }
}

function create($req)
{
    $data = validateEvent($req);

    if (isset($data['invalid'])) {
        $_SESSION['errors'] = $data['invalid'];
        $params = '?' . http_build_query($req);
        header('location: /crud/pages/secure/admin/event.php' . $params);
        return false;
    }

    $currentUser = user();
    $success = createEvent($data, $currentUser);

    if ($success && isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        try {

            $attachmentContent = uploadAttachements($_FILES['attachment']);

            $filePath = '/crud/assets/images/uploads' . $_FILES['attachment']['name'];
            file_put_contents($filePath, $attachmentContent);

            $eventId = $GLOBALS['pdo']->lastInsertId();
            $attachmentData = [
                'event_id' => $eventId,
                'file_path' => $filePath,
                'file_type' => $_FILES['attachment']['type']
            ];

            // Save the attachment in the database
            createAttachment($attachmentData);
        } catch (Exception $e) {
            // Handle errors
            $_SESSION['errors'][] = "File upload error: " . $e->getMessage();
        }
    }

    if ($success) {
        $_SESSION['success'] = 'Event created successfully!';
        header('location: /crud/pages/secure/admin/events');
    } else {
        // Display an error message on the same page
        $_SESSION['errors'] = ['Failed to create the event. Please try again.'];
        $params = '?' . http_build_query($req);
        header('location: /crud/pages/secure/admin/event.php' . $params);
    }
}


function update($req)
{

    $currentUser = user();

    $data = validateEvent($req);

    if (isset($data['invalid'])) {
        $_SESSION['errors'] = $data['invalid'];
        $_SESSION['action'] = 'update';
        $params = '?' . http_build_query($req);
        header('location: /crud/pages/secure/admin/events/event.php' . $params);

        return false;
    }

    if (isset($_FILES['attachment']) && count($_FILES['attachment']['name']) > 0) {
        for ($i = 0; $i < count($_FILES['attachment']['name']); $i++) {
            if ($_FILES['attachment']['error'][$i] === UPLOAD_ERR_OK) {
                try {
                    $currentFile = [
                        'name' => $_FILES['attachment']['name'][$i],
                        'tmp_name' => $_FILES['attachment']['tmp_name'][$i],
                        'type' => $_FILES['attachment']['type'][$i],
                        'error' => $_FILES['attachment']['error'][$i],
                        'size' => $_FILES['attachment']['size'][$i]
                    ];
                    $attachmentContent = uploadAttachements($currentFile);
                    $filePath = '/crud/assets/images/uploads/' . $_FILES['attachment']['name'][$i];
                    file_put_contents($filePath, $attachmentContent);

                    $eventId = isset($req['id']) ? $req['id'] : $GLOBALS['pdo']->lastInsertId();
                    $attachmentData = [
                        'event_id' => $eventId,
                        'file_path' => $filePath,
                        'file_type' => $_FILES['attachment']['type'][$i]
                    ];

                    createAttachment($attachmentData);
                } catch (Exception $e) {
                    $_SESSION['errors'][] = "File upload error: " . $e->getMessage();
                }
            }
        }
    }

    // Set the created_by field based on the current user
    $data['created_by'] = $currentUser['id'];

    $success = updateEvent($data);

    if ($success) {
        $_SESSION['success'] = 'Event successfully changed!';
        $data['action'] = 'update';
        $params = '?' . http_build_query($data);
        header('location: /crud/pages/secure/user/events/event.php' . $params);
    }
}

function delete($req)
{
    $event = getEventById($req['id']);
    $success = deleteEvent($event['id']);

    if ($success) {
        $_SESSION['success'] = 'Event deleted successfully!';
    } else {
        $_SESSION['errors'][] = 'Error deleting event.';
    }

    header('location: /crud/pages/secure/admin/events');
}