<?php
require_once __DIR__ . '/../db/connection.php';

function createCategory($category)
{
    $sqlCreate = "INSERT INTO 
    categories (
        name
    ) 
    VALUES (
        :name
    )";

    $PDOStatement = $GLOBALS['pdo']->prepare($sqlCreate);

    $success = $PDOStatement->execute([
        ':name' => $category['name'],
    ]);

    if ($success) {
        $category['id'] = $GLOBALS['pdo']->lastInsertId();
    }
    return $success;
}

function getCategoryById($id)
{
    $PDOStatement = $GLOBALS['pdo']->prepare('SELECT * FROM categories WHERE id = ?;');
    $PDOStatement->bindValue(1, $id, PDO::PARAM_INT);
    $PDOStatement->execute();
    return $PDOStatement->fetch();
}

function getAllCategories()
{
    $PDOStatement = $GLOBALS['pdo']->query('SELECT * FROM categories;');
    $categories = [];
    while ($category = $PDOStatement->fetch()) {
        $categories[] = $category;
    }
    return $categories;
}

function updateCategory($category)
{
    $sqlUpdate = "UPDATE  
    categories SET
        name = :name
    WHERE id = :id;";

    $PDOStatement = $GLOBALS['pdo']->prepare($sqlUpdate);

    return $PDOStatement->execute([
        ':id' => $category['id'],
        ':name' => $category['name'],
    ]);
}

function deleteCategory($id)
{
    $PDOStatement = $GLOBALS['pdo']->prepare('DELETE FROM categories WHERE id = ?;');
    $PDOStatement->bindValue(1, $id, PDO::PARAM_INT);
    return $PDOStatement->execute();
}
