document.addEventListener("DOMContentLoaded", function () {
    const rest2ApiUrl = document.getElementById("rest2ApiUrl").getAttribute("data-api-url");

    fetch(``)
        .then(response => response.json())
        .then(data => {
            if (data.status === "200" && data.data) {
                const classList = data.data;
                const classListTable = document.getElementById("class-list");
                classListTable.innerHTML = '';

                const now = new Date();

                const upcomingClasses = [];
                const ongoingClasses = [];
                const finishedClasses = [];

                classList.forEach(classData => {
                    const startDate = new Date(classData.start);
                    const endDate = new Date(classData.end);
                    const availableSlots = classData.capacity - classData["quantity"];
                    let rowColor = 'bg-green-100';
                    let buttonText = 'Reserve';
                    let buttonClass = 'bg-blue-500';
                    let buttonDisabled = '';
                    let isMemberOnly = classData.member_only === 1;

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

                    let status = 'Upcoming';
                    if (now > endDate) {
                        status = 'Finished';
                        rowColor = 'bg-gray-50';
                        buttonText = 'Ended';
                        buttonClass = 'bg-silver-500';
                        buttonDisabled = 'disabled';
                    } else if (now >= startDate && now <= endDate) {
                        status = 'Ongoing';
                        rowColor = 'bg-yellow-100';
                    }

                    if (classData["quantity"] >= classData.capacity) {
                        rowColor = 'bg-red-100';
                        buttonText = 'Fully Booked';
                        buttonClass = 'bg-red-500';
                        buttonDisabled = 'disabled';
                    } else if (availableSlots <= classData.capacity * 0.5) {
                        rowColor = 'bg-yellow-100';
                    }

                    const row = document.createElement("tr");
                    row.classList.add(rowColor);
                    row.innerHTML = `
                        <td class="px-4 py-2">${classData.name}</td>
                        <td class="px-4 py-2">${startDateFormatted}</td>
                        <td class="px-4 py-2">${endDateFormatted}</td>
                        <td class="px-4 py-2">${classData.capacity}</td>
                        <td class="px-4 py-2">${availableSlots}</td>
                        <td class="px-4 py-2 text-center">
                            <button class="text-white py-1 px-4 rounded ${buttonClass} ${buttonDisabled}" 
                                    onclick="reserveClass(${classData.id})" ${buttonDisabled}>${isMemberOnly ? 'Member Only' : buttonText}</button>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <a href="class_detail.php?id=${classData.id}" class="bg-green-500 text-white py-1 px-4 rounded">Detail</a>
                        </td>
                    `;
                    classListTable.appendChild(row);

                    if (status === 'Upcoming') upcomingClasses.push(row);
                    else if (status === 'Ongoing') ongoingClasses.push(row);
                    else finishedClasses.push(row);
                });

                classListTable.innerHTML = '';
                [...upcomingClasses, ...ongoingClasses, ...finishedClasses].forEach(row => {
                    classListTable.appendChild(row);
                });
            } else {
                alert("Failed to load classes.");
            }
        })
        .catch(_ => {
            alert("There was an error fetching the classes.");
        });
});

function reserveClass(classId) {
    const rest2ApiUrl = document.getElementById("rest2ApiUrl").getAttribute("data-api-url");

    const userId = localStorage.getItem("id");
    fetch(`${rest2ApiUrl}reservations`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            userId: userId,
            classId: classId
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "200") {
                alert("Reservation successful!");
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert(error.message);
        });
}
