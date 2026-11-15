<?php include 'database/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CeylonLeaf - Premium Tea</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- Include Navbar -->
    <?php include 'components/navbar.php'; ?>

    <!-- Hero Section -->

    <section class="hero" id="hero">
        <div class="slider">

            <!--  First Slide: Image -->
            <div class="slide active">
                <img src="others/images.jpg" alt="Tea Estate">
            </div>

            <!-- Second Slide: Video -->
            <div class="slide">
                <video src="others/tea.mp4" autoplay muted loop></video>
            </div>

            <div class="slide">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15845.261068327623!2d79.89328622817995!3d6.852762014175248!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae25a8936886579%3A0xa8c769f4b86d85b5!2sUniversity%20of%20Sri%20Jayewardenepura!5e0!3m2!1sen!2slk!4v1794757964915!5m2!1sen!2slk" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>
        </div>
    </section>

    <!-- Category Section -->
    <section class="category" id="category">
        <h2 class="section-title">Our Products</h2>
        <div class="product-grid">
            <?php
            // Fetch products from database
            $sql = "SELECT * FROM products ORDER BY id DESC";
            $stmt = $pdo->query($sql);
            $products = $stmt->fetchAll();

            if (count($products) > 0):
                foreach ($products as $row):
            ?>
                    <div class="product-card slide-up" data-description="<?php echo htmlspecialchars($row['description']); ?>">
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="price">
                            Rs.<?php echo number_format($row['price'], 2); ?>
                        </p>

                        <!-- Action Area: Quantity and Add to Cart -->
                        <div class="action-area" onclick="event.stopPropagation();">
                            <div class="qty-control">
                                <button type="button" class="qty-btn minus">-</button>
                                <input type="number" class="qty-input" value="1" min="1">
                                <button type="button" class="qty-btn plus">+</button>
                            </div>

                            <!-- Direct onclick calling the global addToCart function -->
                            <button type="button" class="add-cart-btn" onclick="addToCart(<?php echo $row['id']; ?>, this)">Add to Cart</button>
                        </div>
                    </div>
                <?php
                endforeach;
            else:
                ?>
                <p>No products available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>



    <!-- About Us Section -->
    <section class="about-us" id="aboutus">
        <h2 class="section-title">About Us</h2>
        <p>Welcome to CeylonLeaf! Inspired by Sri Lanka's rich tea heritage, we bring you the finest Ceylon teas with exceptional quality and refreshing flavours. Experience the true taste of Sri Lanka in every cup!</p>
    </section>

    <!-- Include Footer -->
    <?php include 'components/footer.php'; ?>

    <!-- Popup Modal for Description -->

    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3 id="modalTitle"></h3>
            <p id="modalDescription"></p>
        </div>
    </div>

    <script src="script.js"></script>


</body>

</html>