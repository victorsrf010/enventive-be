<?php
require_once __DIR__ . '/../db/connection.php';

function createEvent($event, $created_by)
{
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

    return $PDOStatementEvent->execute([
        ':name' => $event['name'],
        ':description' => $event['description'],
        ':event_at' => $event['event_at'],
        ':location' => $event['location'],
        ':category_id' => $event['category_id'],
        ':created_by' => $created_by['id']
    ]);
}

function getEventById($id)
{
    $PDOStatement = $GLOBALS['pdo']->prepare('SELECT * FROM events WHERE id = ?;');
    $PDOStatement->bindValue(1, $id, PDO::PARAM_INT);
    $PDOStatement->execute();
    return $PDOStatement->fetch();
}

function getAllEvents($category_id = null, $search = null)
{
    $query = 'SELECT * FROM events';
    $whereClauses = [];
    $parameters = [];

    // Add category filtering if a category_id is provided and it's not 'all'
    if ($category_id && $category_id !== 'all') {
        $whereClauses[] = 'category_id = :category_id';
        $parameters[':category_id'] = $category_id;
    }

    // Add search filtering for name or location
    if (!empty($search)) {
        $searchTerm = '%' . $search . '%';
        $whereClauses[] = '(name LIKE :search OR location LIKE :search)';
        $parameters[':search'] = $searchTerm;
    }

    if (count($whereClauses) > 0) {
        $query .= ' WHERE ' . implode(' AND ', $whereClauses);
    }

    // Add ORDER BY clause
    $query .= ' ORDER BY event_at DESC'; // or ASC for ascending order

    $PDOStatement = $GLOBALS['pdo']->prepare($query);

    // Bind parameters
    foreach ($parameters as $key => $value) {
        $PDOStatement->bindValue($key, $value);
    }

    $PDOStatement->execute();
    return $PDOStatement->fetchAll();
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

function updateEventWithoutOwner($event)
{
    $sqlUpdate = "UPDATE  
    events SET
        name = :name, 
        description = :description, 
        event_at = :event_at, 
        location = :location, 
        category_id = :category_id
    WHERE id = :id;";

    $PDOStatement = $GLOBALS['pdo']->prepare($sqlUpdate);

    return $PDOStatement->execute([
        ':id' => $event['id'],
        ':name' => $event['name'],
        ':description' => $event['description'],
        ':event_at' => $event['event_at'],
        ':location' => $event['location'],
        ':category_id' => $event['category_id'],
    ]);
}


function deleteEvent($id)
{
    $GLOBALS['pdo']->beginTransaction();

    try {
        $PDOStatementUserEvent = $GLOBALS['pdo']->prepare('DELETE FROM users_events WHERE event_id = ?;');
        $PDOStatementUserEvent->bindValue(1, $id, PDO::PARAM_INT);
        $PDOStatementUserEvent->execute();


        $PDOStatementAttachments = $GLOBALS['pdo']->prepare('DELETE FROM attachments WHERE event_id = ?;');
        $PDOStatementAttachments->bindValue(1, $id, PDO::PARAM_INT);
        $PDOStatementAttachments->execute();


        $PDOStatementEvent = $GLOBALS['pdo']->prepare('DELETE FROM events WHERE id = ?;');
        $PDOStatementEvent->bindValue(1, $id, PDO::PARAM_INT);
        $PDOStatementEvent->execute();


        $GLOBALS['pdo']->commit();

        return true;
    } catch (PDOException $e) {
        $GLOBALS['pdo']->rollBack();
        return false;
    }
}

function getUserEvents($user_id, $category_id = null, $search = null) {
    $query = 'SELECT * FROM events WHERE created_by = :user_id';
    $parameters = [':user_id' => $user_id];

    if ($category_id && $category_id !== 'all') {
        $query .= ' AND category_id = :category_id';
        $parameters[':category_id'] = $category_id;
    }

    if (!empty($search)) {
        $searchTerm = '%' . $search . '%';
        $query .= ' AND (name LIKE :search OR location LIKE :search)';
        $parameters[':search'] = $searchTerm;
    }

    $query .= ' ORDER BY event_at DESC';

    $PDOStatement = $GLOBALS['pdo']->prepare($query);

    foreach ($parameters as $key => $value) {
        $PDOStatement->bindValue($key, $value);
    }

    $PDOStatement->execute();
    return $PDOStatement->fetchAll();
}

