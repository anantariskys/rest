<?php
require_once 'config/variable.php';
$pageTitle = "Class Detail";
ob_start();
?>

<main class="max-w-6xl mx-auto mt-6 bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Class Detail</h2>

    <div id="class-detail">
    </div>

    <h3 class="mt-4 text-xl font-semibold">Reservations</h3>
    <table class="min-w-full bg-gray-100 table-auto mt-4">
        <thead>
        <tr class="bg-gray-200">
            <th class="px-4 py-2">Member Username</th>
            <th class="px-4 py-2">Membership Status</th>
            <th class="px-4 py-2">Reservation Count</th>
        </tr>
        </thead>
        <tbody id="reservation-list">
        </tbody>
    </table>
</main>

<script src="scripts/class_detail.js" defer></script>
<?php
$pageContent = ob_get_clean();
require_once "layout/wrapper.php";
?>
