
// PART 1: ADD TO CART FUNCTIONALITY

// This function runs when the customer clicks the Add to Cart button.
function addToCart(productId, btnElement) {

    // Display the selected product ID in the browser console.
    console.log("Add to cart clicked for product ID:", productId);

    // Find the product card containing the clicked button.
    const card = btnElement.closest('.product-card');

    // Find the quantity input inside that product card.
    const qtyInput = card ? card.querySelector('.qty-input') : null;

    // Get the selected quantity. If no input exists, use 1.
    const qty = qtyInput ? qtyInput.value : 1;

    // Create a FormData object to send information to PHP.
    const formData = new FormData();

    // Add the product ID to the FormData.
    formData.append('product_id', productId);

    // Add the selected quantity to the FormData.
    formData.append('quantity', qty);

    // Send the data to the PHP file using Fetch API.
    fetch('cart/add_to_cart.php', {
        method: 'POST',
        body: formData
    })

        // Receive the response from PHP and convert it into JSON.
        .then(res => {
            console.log("Response received from server");
            return res.json();
        })

        // Handle the data returned by PHP.
        .then(data => {
            console.log("Parsed JSON data:", data);

            // Display the server's message.
            alert(data.message);

            // Redirect the customer to login if they are not logged in.
            if (
                data.status === 'error' &&
                String(data.message || '').toLowerCase().includes('log in')
            ) {
                window.location.href = 'login/login.php';
            }
        })

        // Handle errors if the request fails.
        .catch(err => {
            console.error('Fetch Error:', err);
            alert('An error occurred. Check your browser console (F12) for details.');
        });
}
