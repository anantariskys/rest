document.addEventListener('DOMContentLoaded', function () {
    localStorage.clear();
    document.getElementById('loginForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        const data = {
            username: username, password: password
        };


        fetch("http://localhost/siber/rest/api/auth/login", {
            method: 'POST', headers: {
                'Content-Type': 'application/json',
            }, body: JSON.stringify(data),
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === '200') {
                    // localStorage.setItem("id", data.data.id);
                    // localStorage.setItem("username", data.data.username);
                    console.log(data);

                    // window.location.href = 'index.php';
                } else {
                    console.log(data);
                    alert(data.message || 'Login failed');
                }
            })
            .catch(error => {
                console.error(error);
                alert(error);
            });
    });
});