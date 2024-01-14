<?php
require_once __DIR__ . '/../db/connection.php';

function createAttachment($attachment)
{
    $sqlCreate = "INSERT INTO 
    attachments (
        event_id,
        file_path,
        file_type
    ) 
    VALUES (
        :event_id,
        :file_path,
        :file_type
    )";

    $PDOStatement = $GLOBALS['pdo']->prepare($sqlCreate);

    $success = $PDOStatement->execute([
        ':event_id' => $attachment['event_id'],
        ':file_path' => $attachment['file_path'],
        ':file_type' => $attachment['file_type'],
    ]);

    if ($success) {
        $attachment['id'] = $GLOBALS['pdo']->lastInsertId();
    }
    return $success;
}

function getAttachmentById($id)
{
    $PDOStatement = $GLOBALS['pdo']->prepare('SELECT * FROM attachments WHERE id = ?;');
    $PDOStatement->bindValue(1, $id, PDO::PARAM_INT);
    $PDOStatement->execute();
    return $PDOStatement->fetch();
}

function getAttachmentsByEventId($event_id)
{
    $PDOStatement = $GLOBALS['pdo']->prepare('SELECT * FROM attachments WHERE event_id = ?;');
    $PDOStatement->bindValue(1, $event_id, PDO::PARAM_INT);
    $PDOStatement->execute();
    $attachments = [];
    while ($attachment = $PDOStatement->fetch()) {
        $attachments[] = $attachment;
    }
    return $attachments;
}

function updateAttachment($attachment)
{
    $sqlUpdate = "UPDATE  
    attachments SET
        event_id = :event_id,
        file_path = :file_path,
        file_type = :file_type
    WHERE id = :id;";

    $PDOStatement = $GLOBALS['pdo']->prepare($sqlUpdate);

    return $PDOStatement->execute([
        ':id' => $attachment['id'],
        ':event_id' => $attachment['event_id'],
        ':file_path' => $attachment['file_path'],
        ':file_type' => $attachment['file_type'],
    ]);
}

function deleteAttachment($id)
{
    $PDOStatement = $GLOBALS['pdo']->prepare('DELETE FROM attachments WHERE id = ?;');
    $PDOStatement->bindValue(1, $id, PDO::PARAM_INT);
    return $PDOStatement->execute();
}
