<?php
require_once __DIR__ . '/../db/connection.php';

function addUserToEvent($user_id, $event_id)
{
    $sqlCreate = "INSERT INTO 
    users_events (
        user_id,
        event_id
    ) 
    VALUES (
        :user_id,
        :event_id
    )";

    $PDOStatement = $GLOBALS['pdo']->prepare($sqlCreate);

    $success = $PDOStatement->execute([
        ':user_id' => $user_id,
        ':event_id' => $event_id,
    ]);

    return $success;
}

function getUsersByEventId($event_id)
{
    $PDOStatement = $GLOBALS['pdo']->prepare('SELECT user_id FROM users_events WHERE event_id = ?;');
    $PDOStatement->bindValue(1, $event_id, PDO::PARAM_INT);
    $PDOStatement->execute();
    $user_ids = [];
    while ($row = $PDOStatement->fetch()) {
        $user_ids[] = $row['user_id'];
    }
    return $user_ids;
}

function getEventsByUserId($user_id)
{
    $PDOStatement = $GLOBALS['pdo']->prepare('SELECT event_id FROM users_events WHERE user_id = ?;');
    $PDOStatement->bindValue(1, $user_id, PDO::PARAM_INT);
    $PDOStatement->execute();
    $event_ids = [];
    while ($row = $PDOStatement->fetch()) {
        $event_ids[] = $row['event_id'];
    }
    return $event_ids;
}

function removeUserFromEvent($user_id, $event_id)
{
    $PDOStatement = $GLOBALS['pdo']->prepare('DELETE FROM users_events WHERE user_id = ? AND event_id = ?;');
    $PDOStatement->bindValue(1, $user_id, PDO::PARAM_INT);
    $PDOStatement->bindValue(2, $event_id, PDO::PARAM_INT);
    return $PDOStatement->execute();
}

function getInvitedUsers($eventId) {
    $userIds = getUsersByEventId($eventId);

    // Check if $userIds is not empty
    if (empty($userIds)) {
        return [];
    }

    // Create a string of placeholders for the IN clause
    $placeholders = implode(',', array_fill(0, count($userIds), '?'));

    // Prepare the SQL query
    $sql = "SELECT * FROM users WHERE id IN ($placeholders)";
    $PDOStatement = $GLOBALS['pdo']->prepare($sql);

    // Bind values to placeholders
    foreach ($userIds as $key => $userId) {
        $PDOStatement->bindValue($key + 1, $userId, PDO::PARAM_INT);
    }

    $PDOStatement->execute();
    return $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
}


function isUserInvited($eventId, $userId) {
    $PDOStatement = $GLOBALS['pdo']->prepare('SELECT * FROM users_events WHERE user_id = ? AND event_id = ?;');
    $PDOStatement->bindValue(1, $userId, PDO::PARAM_INT);
    $PDOStatement->bindValue(2, $eventId, PDO::PARAM_INT);
    $PDOStatement->execute();
    return $PDOStatement->fetch();
}

function getUserInvitedEvents($userId)
{
    $eventIds = getEventsByUserId($userId);

    if (empty($eventIds)) {
        return [];
    }

    $result = [];
    $sql = "SELECT * FROM events WHERE id = ?"; // Query for a single userId
    $PDOStatement = $GLOBALS['pdo']->prepare($sql);

    foreach ($eventIds as $id) {
        $PDOStatement->bindValue(1, $id, PDO::PARAM_INT); // Bind each userId
        $PDOStatement->execute();

        $fetchedData = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($fetchedData)) {
            $result = array_merge($result, $fetchedData); // Merge results
        }
    }

    return $result;
}
