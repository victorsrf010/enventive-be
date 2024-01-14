<?php

require_once __DIR__ . '/../infra/repositories/eventRepository.php';
require_once __DIR__ . '/../infra/repositories/userEventRepository.php';
require_once __DIR__ . '/../helpers/validations/app/validate-event.php';
require_once __DIR__ . '/../helpers/session.php';

if (isset($_POST['event'])) {
    if ($_POST['event'] == 'invite') {
        invite($_POST);
    }
}

if (isset($_GET['event'])) {
    if ($_GET['event'] == 'removeInvite') {
        removeInvite($_GET);
    }
}

function invite($req)
{
    $eventId = $req['event_id'];
    $inviteEmail = $req['invite_email'];

    // Validate if the email exists and get user details
    $invitedUser = getByEmail($inviteEmail);

    if ($invitedUser) {
        // Check if the user is already invited to the event
        $isInvited = isUserInvited($invitedUser['id'], $eventId);

        if (!$isInvited) {
            // Invite the user to the event
            addUserToEvent($invitedUser['id'], $eventId);
            $_SESSION['success'] = 'User invited successfully!';
        } else {
            $_SESSION['errors'][] = 'User is already invited to the event.';
        }
    } else {
        $_SESSION['errors'][] = 'User with the provided email does not exist.';
    }

    $returnUrl = isset($_POST['return_url']) ? $_POST['return_url'] : null;

    header("location:" . returnHeader($req, $returnUrl));
}

function removeInvite($req)
{
    $eventId = $req['event_id'];
    $userId = $req['user_id'];

    // Remove the invitation for the user
    $success = removeUserFromEvent($userId, $eventId);

    if ($success) {
        $_SESSION['success'] = 'Invitation removed successfully!';
    } else {
        $_SESSION['errors'][] = 'Error removing invitation.';
    }

    $returnUrl = isset($_GET['return_url']) ? $_GET['return_url'] : null;

    header("location:" . returnHeader($req, $returnUrl));
}

function returnHeader($req, $returnUrl)
{
    if (!$returnUrl) {
        $params = '?' . http_build_query($req);

        return "/crud/pages/secure/user/events/event.php" . $params;
    }

    return $returnUrl;
}
