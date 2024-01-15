<?php
require_once __DIR__ . '/../../../../infra/repositories/eventRepository.php';
require_once __DIR__ . '/../../../../infra/repositories/userEventRepository.php';
require_once __DIR__ . '/../../../../helpers/session.php';
require_once __DIR__ . '/../../../../templates/header.php';

$user = userId();
$events = getUserInvitedEvents($user);
$title = ' - Your events';
?>

<div class="pt-1 ">
    <div class="p-5 mb-2 bg-dark text-white">
        <h1>Invited Events</h1>
    </div>

    <main class="bg-light">
        <section>
            <?php
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                echo $_SESSION['success'] . '<br>';
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['errors'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                foreach ($_SESSION['errors'] as $error) {
                    echo $error . '<br>';
                }
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                unset($_SESSION['errors']);
            }
            ?>
        </section>
        <section>
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-secondary">
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Event At</th>
                        <th scope="col">Location</th>
                        <th scope="col">Manage</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $modalCounter = 1; // Initialize the counter

                    if (empty($events)) {
                        // Display a friendly message when there are no events
                        echo '<tr><td colspan="4" class="text-center">Looks like there are no events yet. Why not create one?</td></tr>';
                    } else {
                        foreach ($events as $event) {
                            // Increment the counter for each iteration
                            $modalId = 'deleteModal' . $modalCounter;
                            ?>
                            <tr>
                                <th scope="row">
                                    <?= $event['name'] ?>
                                </th>
                                <td>
                                    <?= $event['event_at'] ?>
                                </td>
                                <td>
                                    <?= $event['location'] ?>
                                </td>
                                <td>
                                    <a>
                                        <button type="button" class="btn btn-primary me-2">See More</button>
                                    </a>
                                </td>
                            </tr>
                            <?php
                            // Increment the counter for the next iteration
                            $modalCounter++;
                        }
                    }
                    ?>

                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>