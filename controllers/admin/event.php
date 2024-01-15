<?php

require_once __DIR__ . '/../../infra/repositories/eventRepository.php';
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

    if ($success) {
        $_SESSION['success'] = 'Event created successfully!';
        header('location: /crud/pages/secure/admin/userEvents.php');
    } else {
        // Display an error message on the same page
        $_SESSION['errors'] = ['Failed to create the event. Please try again.'];
        $params = '?' . http_build_query($req);
        header('location: /crud/pages/secure/admin/event.php' . $params);
    }
}


function update($req)
{
    // Assuming you have a function to get the current user from the session
    $currentUser = user();

    $data = validateEvent($req);

    if (isset($data['invalid'])) {
        $_SESSION['errors'] = $data['invalid'];
        $_SESSION['action'] = 'update';
        $params = '?' . http_build_query($req);
        header('location: /crud/pages/secure/admin/event.php' . $params);

        return false;
    }

    // Set the created_by field based on the current user
    $data['created_by'] = $currentUser['id'];

    $success = updateEvent($data);

    if ($success) {
        $_SESSION['success'] = 'Event successfully changed!';
        $data['action'] = 'update';
        $params = '?' . http_build_query($data);
        header('location: /crud/pages/secure/admin/event.php' . $params);
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

    header('location: /crud/pages/secure/admin/userEvents.php');
}