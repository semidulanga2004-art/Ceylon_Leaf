<?php include 'db_connect.php'; ?>
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
     <?php include 'navbar.php'; ?>

    <!-- Hero Section -->

    <section class="hero" id="hero">
       <div class="slider">

          <!--  First Slide: Image -->
              <div class="slide active">
                  <img src="image1.jpg" alt="Tea Estate">
              </div>

           <!-- Second Slide: Video -->
              <div class="slide">
                 <video src="video.mp4" autoplay muted loop></video>
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
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0): //Check whether the database return any product
                while ($row = $result->fetch_assoc()): // display each product
            ?>
                    <div class="product-card slide-up" data-description="<?php echo htmlspecialchars($row['description']); ?>">
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="price">
                            $<?php echo number_format($row['price'], 2); ?>
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
                endwhile;
            else:
                ?>
                <p>No products available at the moment.</p>
            <?php endif; ?>
        </div>
    </section>

    

     <!-- About Us Section -->
      <section class="about-us" id="aboutus">
        <h2 class="section-title">About Us</h2>
        <p>Welcome to CeylonLeaf. This journey began from a simple thought—a <strong>semis' idea forced to start it</strong>. We are dedicated to bringing you the finest, hand-picked tea leaves directly from the lush estates of our homeland to your cup. Quality and authenticity are the core of our business.</p>
    </section>

    <!-- Include Footer -->
     <?php include 'footer.php'; ?>

</body>   
</html> 