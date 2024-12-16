document.addEventListener("DOMContentLoaded", function () {
    const classId = new URLSearchParams(window.location.search).get('id');
    const rest2apiUrl = document.getElementById("rest2ApiUrl").getAttribute("data-api-url");
    const restapiUrl = document.getElementById("restApiUrl").getAttribute("data-api-url");

    if (!classId) {
        alert("Class ID is required.");
        window.location.href = "dashboard.php";
        return;
    }

    fetch(`${rest2apiUrl}classes/${classId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === "200" && data.data) {
                const classData = data.data;
                const startDate = new Date(classData.start);
                const endDate = new Date(classData.end);
                const startDateFormatted = startDate.toLocaleDateString('en-US', {
                    weekday: 'long',
                    hour: '2-digit',
                    minute: '2-digit',
                });

                const endDateFormatted = endDate.toLocaleDateString('en-US', {
                    weekday: 'long',
                    hour: '2-digit',
                    minute: '2-digit',
                });
                document.getElementById("class-detail").innerHTML = `
                    <p><strong>Class Name:</strong> ${classData.name}</p>
                    <p><strong>Start Time:</strong> ${startDateFormatted}</p>
                    <p><strong>End Time:</strong> ${endDateFormatted}</p>
                    <p><strong>Capacity:</strong> ${classData.capacity}</p>
                    <p><strong>Available Slots:</strong> ${classData.capacity - classData["quantity"]}</p>
                `;
            } else {
                alert("Failed to load class data.");
            }
        })
        .catch(error => {
            console.error(error);
            alert("There was an error fetching the class data.");
        });

    fetch(`${rest2apiUrl}classes/${classId}/reservations`)
        .then(response => response.json())
        .then(data => {
            if (data.status === "200" && data.data) {
                const reservationList = data.data;
                const reservationTable = document.getElementById("reservation-list");
                reservationTable.innerHTML = '';
                const userReservations = {};

                reservationList.forEach(reservation => {
                    const userId = reservation["member_id"];
                    const reservationDate = new Date(reservation["reservation_date"]).toLocaleString();

                    if (!userReservations[userId]) {
                        userReservations[userId] = {
                            count: 0,
                            reservations: []
                        };
                    }
                    userReservations[userId].count += 1;
                    userReservations[userId].reservations.push(reservationDate);
                });

                for (const userId in userReservations) {
                    fetch(`${restapiUrl}user/${userId}`)
                        .then(userResponse => userResponse.json())
                        .then(userData => {
                            if (userData.status === "200" && userData.data) {
                                const user = userData.data;
                                const {count, reservations} = userReservations[userId];
                                const row = document.createElement("tr");
                                row.innerHTML = `
                                    <td class="px-4 py-2">${user.username}</td>
                                    <td class="px-4 py-2">${user["membership_status"]}</td>
                                    <td class="px-4 py-2">${count}</td>
                                `;
                                reservationTable.appendChild(row);
                            } else {
                                console.error("Failed to load user data for member ID:", userId);
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            alert("There was an error fetching user data.");
                        });
                }
            } else {
                alert("Failed to load reservations.");
            }
        })
        .catch(error => {
            console.error(error);
            alert("There was an error fetching the reservations.");
        });
});
