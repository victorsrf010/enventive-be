<?php
require_once __DIR__ . '/infra/middlewares/middleware-not-authenticated.php';
//require_once __DIR__ . './setupdatabase.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>SIR 2023/24</title>
</head>

<body>

<main>
    <div class="container py-4">
        <header class="pb-3 mb-4 border-bottom">
            <div class='d-flex justify-content-between align-items-center'>
                <a href="./index.html" style="text-decoration: none;">
                    <h1 class='logo' style="font-size: 2em; color: black; font-weight: bold;">
                        event<span style="color: orange;">ive</span>
                    </h1>
                </a>
                <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarSupportedContent' aria-controls='navbarSupportedContent' aria-expanded='false' aria-label='Toggle navigation'>
                    <span class='navbar-toggler-icon'></span>
                </button>
            </div>
        </header>
            <div class="p-5 mb-4 bg-body-tertiary rounded">
                <div class="container-fluid py-5 ">
                    <h1 class="display-5 fw-bold">Olá👋</h1>
                    <div class="d-flex justify-content">
                        <a href="/crud/pages/public/signin.php"><button class="btn btn-success btn-lg px-5 me-2">Sign
                                In</button></a>
                        <a href="/crud/pages/public/signup.php"><button class="btn btn-info btn-lg px-4">Sign
                                Up</button></a>
                    </div>
                </div>
            </div>

            <footer class="p-3 bg-dark text-white mt-4">
                <article>Eventive &copy; - 2023-2024</article>
            </footer>
        </div>
    </main>
</body>

</html>