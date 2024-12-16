<?php
require_once 'config/variable.php';

$pageTitle = "Dashboard";
ob_start();
?>

<main class="max-w-6xl mx-auto mt-6 bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-semibold mb-4">Available Classes</h2>
    <table class="min-w-full table-auto mt-4">
        <thead>
        <tr>
            <th class="px-4 py-2">Class Name</th>
            <th class="px-4 py-2">Start</th>
            <th class="px-4 py-2">End</th>
            <th class="px-4 py-2">Capacity</th>
            <th class="px-4 py-2">Available Slots</th>
            <th class="px-4 py-2 text-center" colspan="2">
                <div class="flex justify-center space-x-4">
                    <span>Action</span>
                </div>
            </th>
        </tr>
        </thead>
        <tbody id="class-list">
        </tbody>
    </table>
</main>
<script src="scripts/class.js"></script>
<?php
$pageContent = ob_get_clean();
require_once "layout/wrapper.php";
?>
