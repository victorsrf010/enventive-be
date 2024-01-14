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
