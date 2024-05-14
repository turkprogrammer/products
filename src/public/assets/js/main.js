document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded and parsed");
    const clientId = 1;  // Здесь можно изменить на нужный ID клиента

    fetch(`/src/client_orders.php?id=${clientId}`)
        .then(response => {
            console.log('Response received:', response);
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            if (data.error) {
                throw new Error(data.error);
            }
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = '';

            data.forEach(order => {
                const orderDiv = document.createElement('div');
                orderDiv.className = 'order';
                orderDiv.innerHTML = `
                    <p>Client: ${order.first_name} ${order.second_name}</p>
                    <p>Product: ${order.title}</p>
                    <p>Price: ${order.price}</p>
                `;
                resultsDiv.appendChild(orderDiv);
            });
        })
        .catch(error => {
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = `<p>Error: ${error.message}</p>`;
            console.error('Error fetching client orders:', error);
        });
});