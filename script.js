
// ADD TO CART FUNCTIONALITY

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


// ==========================================
// DOM INITIALIZATION & THEME TOGGLE
// ==========================================

// Wait until the HTML document has fully loaded.
document.addEventListener("DOMContentLoaded", () => {

    // Find the theme toggle button in the HTML.
    const themeToggle = document.getElementById('theme-toggle');

    // Get the previously saved theme from localStorage.
    const currentTheme = localStorage.getItem('theme');

    // Apply the saved theme when the page loads.
    if (currentTheme) {
        document.documentElement.setAttribute('data-theme', currentTheme);

        // Display the sun icon when dark mode is active.
        if (themeToggle && currentTheme === 'dark') {
            themeToggle.textContent = '☀️';
        }
    }

    // Check whether the theme toggle button exists.
    if (themeToggle) {

        // Listen for clicks on the theme toggle button.
        themeToggle.addEventListener('click', () => {

            // Find out which theme is currently active.
            let activeTheme = document.documentElement.getAttribute('data-theme');

            // If dark mode is active, switch to light mode.
            if (activeTheme === 'dark') {

                document.documentElement.removeAttribute('data-theme');

                // Save the light theme preference.
                localStorage.setItem('theme', 'light');

                // Display the moon icon.
                themeToggle.textContent = '🌙';

            } else {

                // Otherwise, activate dark mode.
                document.documentElement.setAttribute('data-theme', 'dark');

                // Save the dark theme preference.
                localStorage.setItem('theme', 'dark');

                // Display the sun icon.
                themeToggle.textContent = '☀️';
            }
        });
    }
});


// ==========================================
// HERO SLIDER & SCROLL ANIMATIONS
// ==========================================

// Wait until the HTML document has fully loaded.
document.addEventListener("DOMContentLoaded", () => {

    // ------------------------------
    // 1. HERO SLIDER
    // ------------------------------

    // Find all hero slides on the page.
    const slides = document.querySelectorAll('.hero-slide');

    // Start with the first slide.
    let currentSlide = 0;

    // Check whether hero slides exist.
    if (slides.length > 0) {

        // Show the first slide initially.
        slides[currentSlide].classList.add('active');

        // Change the slide every 5 seconds.
        setInterval(() => {

            // Hide the current slide.
            slides[currentSlide].classList.remove('active');

            // Move to the next slide.
            currentSlide = (currentSlide + 1) % slides.length;

            // Show the next slide.
            slides[currentSlide].classList.add('active');

        }, 5000);
    }


    // ------------------------------
    // 2. SCROLL REVEAL ANIMATIONS
    // ------------------------------

    // Find all elements that should animate when scrolling.
    const revealElements = document.querySelectorAll('.slide-up');

    // Check whether the browser supports IntersectionObserver.
    if ('IntersectionObserver' in window) {

        // Create an observer to detect visible elements.
        const observer = new IntersectionObserver((entries) => {

            // Check each observed element.
            entries.forEach(entry => {

                // If the element enters the screen...
                if (entry.isIntersecting) {

                    // Add the visible class to start the animation.
                    entry.target.classList.add('visible');

                    // Stop observing this element after animation.
                    observer.unobserve(entry.target);
                }
            });

        }, {
            threshold: 0.1
        });

        // Observe every reveal element.
        revealElements.forEach(element => {
            observer.observe(element);
        });

    } else {

        // Show elements normally if IntersectionObserver is unsupported.
        revealElements.forEach(element => {
            element.classList.add('visible');
        });
    }
});
    


// ==========================================
// PRODUCT MODAL & QUANTITY CONTROLS
// ==========================================

document.addEventListener("DOMContentLoaded", () => {

    // ------------------------------
    // 1. PRODUCT MODAL POPUP
    // ------------------------------

    // Get the modal elements from the HTML.
    const modal = document.getElementById("productModal");
    const modalTitle = document.getElementById("modalTitle");
    const modalDescription = document.getElementById("modalDescription");
    const closeBtn = document.querySelector(".close-btn");

    // Find all product cards.
    document.querySelectorAll('.product-card').forEach(card => {

        // Open the modal when a product card is clicked.
        card.addEventListener('click', function () {

            // Get the product title.
            const title = this.querySelector('h3')
                ? this.querySelector('h3').innerText
                : '';

            // Get the product description.
            const desc = this.getAttribute('data-description') || '';

            // Check that all required modal elements exist.
            if (modal && modalTitle && modalDescription) {

                // Display the product information.
                modalTitle.innerText = title;
                modalDescription.innerText = desc;

                // Show the modal.
                modal.style.display = "flex";
            }
        });
    });

    // Close the modal when the close button is clicked.
    if (closeBtn && modal) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = "none";
        });
    }

    // Close the modal when the user clicks outside its content.
    window.addEventListener('click', (e) => {
        if (modal && e.target === modal) {
            modal.style.display = "none";
        }
    });


    // ------------------------------
    // 2. QUANTITY INCREASE / DECREASE
    // ------------------------------

    // Find all quantity buttons.
    document.querySelectorAll('.qty-btn').forEach(btn => {

        btn.addEventListener('click', function (e) {

            // Prevent the product modal from opening.
            e.stopPropagation();

            // Check whether this is the plus button.
            const isPlus = this.classList.contains('plus');

            // Find the quantity input in the same container.
            const input = this.parentElement.querySelector('.qty-input');

            // Stop if the input does not exist.
            if (!input) return;

            // Get the current quantity.
            let val = parseInt(input.value) || 1;

            // Increase or decrease the quantity.
            if (isPlus) {
                input.value = val + 1;
            } else if (val > 1) {
                input.value = val - 1;
            }
        });
    });


    // Prevent clicks on quantity inputs and Add to Cart buttons
    // from opening the product modal.
    document.querySelectorAll('.qty-input, .add-cart-btn').forEach(el => {
        el.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });


    // ------------------------------
    // 3. ADD TO CART FALLBACK
    // ------------------------------

    // Find all Add to Cart buttons.
    document.querySelectorAll('.add-cart-btn').forEach(button => {

        // Only add an event listener if inline onclick is not present.
        if (!button.hasAttribute('onclick')) {

            button.addEventListener('click', function (e) {

                // Prevent the product modal from opening.
                e.stopPropagation();

                // Find the product card.
                const card = this.closest('.product-card');

                // Get the product ID from the card.
                const productId = card
                    ? card.getAttribute('data-id')
                    : null;

                // Call the global Add to Cart function.
                if (productId) {
                    addToCart(productId, this);
                }
            });
        }
    });

});

