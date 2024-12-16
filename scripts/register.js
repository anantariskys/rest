document.getElementById('registerForm').addEventListener('submit', function (event) {
    event.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    const data = {
        username: username, password: password
    };
    const apiUrl = document.getElementById('apiUrl').getAttribute('data-api-url');

    fetch(apiUrl + 'auth/register', {
        method: 'POST', headers: {
            'Content-Type': 'application/json',
        }, body: JSON.stringify(data),
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === '201') {
                alert(data.message || 'Registration success');
                window.location.href = 'login.php';
            } else {
                alert(data.message || 'Registration failed');
            }
        })
        .catch(error => {
            console.error(error);
            alert('An error occurred. Please try again later.');
        });
});