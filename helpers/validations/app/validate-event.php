<?php

function validateEvent($req)
{
    foreach ($req as $key => $value) {
        $req[$key] = trim($req[$key]);
    }

    $errors = [];

    if (empty($req['name']) || strlen($req['name']) < 3 || strlen($req['name']) > 255) {
        $errors['name'] = 'The Name field cannot be empty and must be between 3 and 255 characters.';
    }

    if (empty($req['description']) || strlen($req['description']) < 3) {
        $errors['description'] = 'The Description field cannot be empty and must be at least 3 characters long.';
    }

    // Check if there are any errors
    if (!empty($errors)) {
        return ['invalid' => $errors];
    }

    return $req;
}
?>
