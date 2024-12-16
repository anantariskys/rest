<header class="bg-gray-800 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <span id="welcomeMessage" class="text-xl font-semibold"></span>
        <a href="../logout.php" class="bg-red-600 px-4 py-2 rounded hover:bg-red-700">Logout</a>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const username = localStorage.getItem("username");

        if (username) {
            document.getElementById('welcomeMessage').textContent = `Welcome, ${username}!`;
        } else {
            window.location.href = 'login.php';
        }
    });
    document.getElementById("welcomeMessage").addEventListener("click", function () {
        window.location.href = "index.php";
    });
</script>
