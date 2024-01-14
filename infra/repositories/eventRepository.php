<?php
require_once __DIR__ . '/../db/connection.php';

function createEvent($event, $created_by)
{
    // Insert event into the events table
    $sqlCreateEvent = "INSERT INTO 
    events (
        name,
        description, 
        event_at, 
        location, 
        category_id, 
        created_by
    ) 
    VALUES (
        :name, 
        :description, 
        :event_at, 
        :location, 
        :category_id, 
        :created_by
    )";

    $PDOStatementEvent = $GLOBALS['pdo']->prepare($sqlCreateEvent);

    $successEvent = $PDOStatementEvent->execute([
        ':name' => $event['name'],
        ':description' => $event['description'],
        ':event_at' => $event['event_at'],
        ':location' => $event['location'],
        ':category_id' => $event['category_id'],
        ':created_by' => $created_by['id']
    ]);

    if ($successEvent) {
        // If the event was created successfully, get the event ID
        $event['id'] = $GLOBALS['pdo']->lastInsertId();

        // Insert a record into users_events table to connect the user to the event
        $sqlCreateUserEvent = "INSERT INTO 
        users_events (
            user_id,
            event_id
        ) 
        VALUES (
            :user_id,
            :event_id
        )";

        $PDOStatementUserEvent = $GLOBALS['pdo']->prepare($sqlCreateUserEvent);

        // Assuming $created_by is the user ID
        $successUserEvent = $PDOStatementUserEvent->execute([
            ':user_id' => $created_by['id'],
            ':event_id' => $event['id']
        ]);

        // Return success only if both event and user_event records were created successfully
        return $successEvent && $successUserEvent;
    }

    return false;
}

function getEventById($id)
{
    $PDOStatement = $GLOBALS['pdo']->prepare('SELECT * FROM events WHERE id = ?;');
    $PDOStatement->bindValue(1, $id, PDO::PARAM_INT);
    $PDOStatement->execute();
    return $PDOStatement->fetch();
}

function getAllEvents()
{
    $PDOStatement = $GLOBALS['pdo']->query('SELECT * FROM events;');
    $events = [];
    while ($event = $PDOStatement->fetch()) {
        $events[] = $event;
    }
    return $events;
}

function updateEvent($event)
{
    $sqlUpdate = "UPDATE  
    events SET
        name = :name, 
        description = :description, 
        event_at = :event_at, 
        location = :location, 
        category_id = :category_id, 
        created_by = :created_by
    WHERE id = :id;";

    $PDOStatement = $GLOBALS['pdo']->prepare($sqlUpdate);

    return $PDOStatement->execute([
        ':id' => $event['id'],
        ':name' => $event['name'],
        ':description' => $event['description'],
        ':event_at' => $event['event_at'],
        ':location' => $event['location'],
        ':category_id' => $event['category_id'],
        ':created_by' => $event['created_by']
    ]);
}

function deleteEvent($id)
{
    $GLOBALS['pdo']->beginTransaction();

    try {
        // First, delete entries from the user_events table
        $PDOStatementUserEvent = $GLOBALS['pdo']->prepare('DELETE FROM users_events WHERE event_id = ?;');
        $PDOStatementUserEvent->bindValue(1, $id, PDO::PARAM_INT);
        $PDOStatementUserEvent->execute();

        // Second, delete entries from the attachments table (if applicable)
        $PDOStatementAttachments = $GLOBALS['pdo']->prepare('DELETE FROM attachments WHERE event_id = ?;');
        $PDOStatementAttachments->bindValue(1, $id, PDO::PARAM_INT);
        $PDOStatementAttachments->execute();

        // Finally, delete the event itself
        $PDOStatementEvent = $GLOBALS['pdo']->prepare('DELETE FROM events WHERE id = ?;');
        $PDOStatementEvent->bindValue(1, $id, PDO::PARAM_INT);
        $PDOStatementEvent->execute();

        // Commit the transaction if all queries are successful
        $GLOBALS['pdo']->commit();

        return true;
    } catch (PDOException $e) {
        // An error occurred, rollback the transaction
        $GLOBALS['pdo']->rollBack();
        return false;
    }
}

function getUserEvents($user_id) {
    $PDOStatement = $GLOBALS['pdo']->prepare('SELECT * FROM events WHERE created_by = ?;');
    $PDOStatement->bindValue(1, $user_id, PDO::PARAM_INT);
    $PDOStatement->execute();

    $events = [];
    while ($event = $PDOStatement->fetch(PDO::FETCH_ASSOC)) {
        $events[] = $event;
    }

    return $events;
}

