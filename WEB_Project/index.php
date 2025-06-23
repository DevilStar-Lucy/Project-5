<?php include('partials-front/menu.php'); ?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Delicious Food Delivered to Your Door</h1>
            <p>Discover amazing dishes from the best restaurants in your area. Fast delivery, fresh ingredients, unforgettable taste.</p>
            
            <form action="<?php echo SITEURL; ?>food-search.php" method="POST" class="search-form">
                <input type="search" name="search" placeholder="Search for your favorite food..." required>
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>
</section>

<?php 
if (isset($_SESSION['review'])) {
    echo $_SESSION['review'];
    unset($_SESSION['review']);
}
if (isset($_SESSION['order'])) {
    echo $_SESSION['order'];
    unset($_SESSION['order']);
}
?>

<section class="categories">
    <div class="container">
        <h2>Explore Our Categories</h2>
        <p class="text-center" style="margin-bottom: 3rem; color: var(--text-light);">
            Discover a world of flavors with our carefully curated food categories
        </p>
        
        <div class="categories-grid">
            <?php 
                $sql = "SELECT * FROM tbl_category WHERE active='Yes' AND featured='Yes' LIMIT 6";
                $res = mysqli_query($conn, $sql);
                $count = mysqli_num_rows($res);
                
                if($count > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $id = $row['id'];
                        $title = $row['title'];
                        $image_name = $row['image_name'];
                        ?>
                        <a href="<?php echo SITEURL; ?>category-foods.php?category_id=<?php echo $id; ?>" class="category-card">
                            <?php 
                                if($image_name == "") {
                                    echo "<div class='error'>Image not Available</div>";
                                } else {
                                    ?>
                                    <img src="<?php echo SITEURL; ?>images/category/<?php echo $image_name; ?>" alt="<?php echo $title; ?>">
                                    <?php
                                }
                            ?>
                            <div class="category-overlay">
                                <h3><?php echo $title; ?></h3>
                                <p>Explore delicious <?php echo strtolower($title); ?> options</p>
                            </div>
                        </a>
                        <?php
                    }
                } else {
                    echo "<div class='error'>No categories available.</div>";
                }
            ?>
        </div>
        
        <div class="text-center" style="margin-top: 3rem;">
            <a href="<?php echo SITEURL; ?>categories.php" class="btn btn-secondary">
                <i class="fas fa-th-large"></i> View All Categories
            </a>
        </div>
    </div>
</section>

<section class="food-menu">
    <div class="container">
        <h2>Featured Dishes</h2>
        <p class="text-center" style="margin-bottom: 3rem; color: var(--text-light);">
            Hand-picked favorites that our customers love the most
        </p>

        <div class="swiper-container mySwiper">
            <div class="swiper-wrapper">
                <?php 
                $sql2 = "SELECT * FROM tbl_food WHERE active='Yes' AND featured='Yes' LIMIT 8";
                $res2 = mysqli_query($conn, $sql2);
                $count2 = mysqli_num_rows($res2);
                
                if($count2 > 0) {
                    while($row = mysqli_fetch_assoc($res2)) {
                        $id = $row['id'];
                        $title = $row['title'];
                        $price = $row['price'];
                        $description = $row['description'];
                        $image_name = $row['image_name'];
                        
                        // Get average rating
                        $sql_rating = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM tbl_review WHERE food_id = $id";
                        $res_rating = mysqli_query($conn, $sql_rating);
                        $row_rating = mysqli_fetch_assoc($res_rating);
                        $avg_rating = round($row_rating['avg_rating'], 1);
                        $review_count = $row_rating['review_count'];
                        ?>
                        <div class="swiper-slide">
                            <div class="food-card">
                                <div class="food-card-image">
                                    <?php 
                                        if($image_name == "") {
                                            echo "<div class='error'>Image not available.</div>";
                                        } else {
                                            ?>
                                            <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" alt="<?php echo $title; ?>">
                                            <?php
                                        }
                                    ?>
                                    <div class="food-badge">Featured</div>
                                </div>
                                
                                <div class="food-card-content">
                                    <h4><?php echo $title; ?></h4>
                                    <p class="food-description"><?php echo substr($description, 0, 100) . '...'; ?></p>
                                    
                                    <div class="food-rating">
                                        <div class="stars">
                                            <?php 
                                            for($i = 1; $i <= 5; $i++) {
                                                if($i <= $avg_rating) {
                                                    echo '<i class="fas fa-star"></i>';
                                                } else {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <span class="rating-text">
                                            <?php echo $avg_rating > 0 ? "$avg_rating ($review_count reviews)" : "No reviews yet"; ?>
                                        </span>
                                    </div>
                                    
                                    <div class="food-price">৳<?php echo $price; ?></div>
                                    
                                    <a href="<?php echo SITEURL; ?>order.php?food_id=<?php echo $id; ?>" class="btn btn-primary" style="width: 100%;">
                                        <i class="fas fa-shopping-cart"></i> Order Now
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<div class='error'>No featured foods available.</div>";
                }
                ?>
            </div>
            
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>

        <div class="text-center" style="margin-top: 3rem;">
            <a href="<?php echo SITEURL; ?>foods.php" class="btn btn-secondary">
                <i class="fas fa-utensils"></i> View Full Menu
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section style="padding: 80px 0; background: var(--light-bg);">
    <div class="container">
        <h2 class="text-center">Why Choose FoodieHub?</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 3rem;">
            <div style="text-align: center; padding: 2rem;">
                <div style="width: 80px; height: 80px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem;">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3>Fast Delivery</h3>
                <p>Get your food delivered in 30 minutes or less</p>
            </div>
            <div style="text-align: center; padding: 2rem;">
                <div style="width: 80px; height: 80px; background: var(--success-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem;">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3>Fresh Ingredients</h3>
                <p>We use only the freshest, highest quality ingredients</p>
            </div>
            <div style="text-align: center; padding: 2rem;">
                <div style="width: 80px; height: 80px; background: var(--accent-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem;">
                    <i class="fas fa-star"></i>
                </div>
                <h3>Top Rated</h3>
                <p>Highly rated by thousands of satisfied customers</p>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    var swiper = new Swiper('.mySwiper', {
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 'auto',
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        coverflowEffect: {
            rotate: 50,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows: true,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            320: {
                slidesPerView: 1,
                coverflowEffect: {
                    rotate: 30,
                    depth: 80,
                },
            },
            768: {
                slidesPerView: 2,
                coverflowEffect: {
                    rotate: 40,
                    depth: 90,
                },
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });

    // Add scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.food-card, .category-card').forEach(el => {
        observer.observe(el);
    });
</script>

<?php include('partials-front/footer.php'); ?>
```