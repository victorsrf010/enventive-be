<?php
#EASY DATABASE SETUP
global $pdo;
require __DIR__ . '/infra/db/connection.php';

# DROP TABLES IF THEY EXIST
$pdo->exec('DROP TABLE IF EXISTS attachments;');
echo 'Table attachments dropped!' . PHP_EOL;

$pdo->exec('DROP TABLE IF EXISTS users_events;');
echo 'Table users_events dropped!' . PHP_EOL;

$pdo->exec('DROP TABLE IF EXISTS events;');
echo 'Table events dropped!' . PHP_EOL;

$pdo->exec('DROP TABLE IF EXISTS users;');
echo 'Table users dropped!' . PHP_EOL;

$pdo->exec('DROP TABLE IF EXISTS categories;');
echo 'Table categories dropped!' . PHP_EOL;

#CREATE TABLE
$pdo->exec(
    'CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTO_INCREMENT, 
    name varchar(50)	, 
    lastname varchar(50)	, 
    phoneNumber varchar(50)	, 
    email varchar(50)	 NOT NULL, 
    foto varchar(50)	 NULL, 
    administrator bit, 
    password varchar(200)	);'
);

echo 'Tabela users created!' . PHP_EOL;

#DEFAULT USER TO ADD
$user = [
    'name' => 'Marcelo',
    'lastname' => 'Antunes Fernandes',
    'phoneNumber' => '987654321',
    'email' => 'fernandesmarcelo@estg.ipvc.pt',
    'foto' => null,
    'administrator' => true,
    'password' => '123456'
];

#HASH PWD
$user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);

#INSERT USER
$sqlCreate = "INSERT INTO 
    users (
        name, 
        lastname, 
        phoneNumber, 
        email, 
        foto, 
        administrator, 
        password) 
    VALUES (
        :name, 
        :lastname, 
        :phoneNumber, 
        :email, 
        :foto, 
        :administrator, 
        :password
    )";

#PREPARE QUERY
$PDOStatement = $GLOBALS['pdo']->prepare($sqlCreate);

#EXECUTE
$success = $PDOStatement->execute([
    ':name' => $user['name'],
    ':lastname' => $user['lastname'],
    ':phoneNumber' => $user['phoneNumber'],
    ':email' => $user['email'],
    ':foto' => $user['foto'],
    ':administrator' => $user['administrator'],
    ':password' => $user['password']
]);

echo 'Default user created!';

#DEFAULT USER TO ADD
$regularUser = [
    'name' => 'Regular',
    'lastname' => 'User',
    'phoneNumber' => '987654321',
    'email' => 'regular@gmail.com',
    'foto' => null,
    'administrator' => false,
    'password' => '123456'
];

#HASH PWD
$regularUser['password'] = password_hash($regularUser['password'], PASSWORD_DEFAULT);

#PREPARE QUERY
$PDOStatement = $GLOBALS['pdo']->prepare($sqlCreate);

#EXECUTE
$success = $PDOStatement->execute([
    ':name' => $regularUser['name'],
    ':lastname' => $regularUser['lastname'],
    ':phoneNumber' => $regularUser['phoneNumber'],
    ':email' => $regularUser['email'],
    ':foto' => $regularUser['foto'],
    ':administrator' => $regularUser['administrator'],
    ':password' => $regularUser['password']
]);

echo 'Regular user created!';

# CREATE CATEGORIES TABLE
$pdo->exec(
    'CREATE TABLE categories (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL
    );'
);
echo 'Table categories created!' . PHP_EOL;

# CREATE EVENTS TABLE
$pdo->exec(
    'CREATE TABLE events (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        event_at DATETIME NOT NULL,
        location VARCHAR(255),
        category_id INT,
        created_by INT,
        FOREIGN KEY (category_id) REFERENCES categories(id),
        FOREIGN KEY (created_by) REFERENCES users(id)
    );'
);
echo 'Table events created!' . PHP_EOL;

# CREATE USERS_EVENTS TABLE
$pdo->exec(
    'CREATE TABLE users_events (
        user_id INT,
        event_id INT,
        PRIMARY KEY (user_id, event_id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (event_id) REFERENCES events(id)
    );'
);
echo 'Table users_events created!' . PHP_EOL;

# CREATE ATTACHMENTS TABLE
$pdo->exec(
    'CREATE TABLE attachments (
        id INT PRIMARY KEY AUTO_INCREMENT,
        event_id INT,
        file_path VARCHAR(255),
        file_type VARCHAR(50),
        FOREIGN KEY (event_id) REFERENCES events(id)
    );'
);
echo 'Table attachments created!' . PHP_EOL;

# INSERT CATEGORIES
$categories = [
    'Music & Concerts', 'Sports & Fitness', 'Education & Workshops',
    'Arts & Theater', 'Social & Networking', 'Food & Drink',
    'Technology & Innovation', 'Health & Wellness', 'Family & Kids',
    'Charity & Causes', 'Entertainment & Lifestyle', 'Business & Professional',
    'Travel & Outdoor', 'Government & Politics', 'Religious & Spiritual'
];

foreach ($categories as $category) {
    $pdo->exec("INSERT INTO categories (name) VALUES (\"$category\");");
    echo "Category '$category' inserted!" . PHP_EOL;
}


# INSERT DEFAULT EVENT
$pdo->exec(
    'INSERT INTO events (name, description, event_at, location, category_id, created_by) VALUES ("Default Event", "Default Event Description", NOW(), "Default Location", 1, 1);'
);
echo 'Default event inserted!' . PHP_EOL;

# INSERT USER INTO USERS_EVENTS
$pdo->exec(
    'INSERT INTO users_events (user_id, event_id) VALUES (1, 1);'
);
echo 'User inserted into users_events!' . PHP_EOL;

# INSERT DEFAULT ATTACHMENT
$pdo->exec(
    'INSERT INTO attachments (event_id, file_path, file_type) VALUES (1, "/path/to/default_attachment.jpg", "image/jpeg");'
);
echo 'Default attachment inserted!' . PHP_EOL;
