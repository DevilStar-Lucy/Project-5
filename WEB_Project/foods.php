<?php include('partials-front/menu.php'); ?>

<section class="hero" style="padding: 120px 0 60px;">
    <div class="container">
        <div class="hero-content">
            <h1>Our Complete Menu</h1>
            <p>Explore our wide variety of delicious dishes, carefully crafted by expert chefs</p>
            
            <form action="<?php echo SITEURL; ?>food-search.php" method="POST" class="search-form">
                <input type="search" name="search" placeholder="Search for your favorite dish..." required>
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>
</section>

<section class="food-menu" style="padding: 60px 0;">
    <div class="container">
        <div class="food-grid">
            <?php
            $sql = "SELECT * FROM tbl_food WHERE active='Yes' ORDER BY featured DESC, id DESC";
            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);

            if ($count > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $id = $row['id'];
                    $title = $row['title'];
                    $description = $row['description'];
                    $price = $row['price'];
                    $image_name = $row['image_name'];
                    $featured = $row['featured'];
                    
                    // Get average rating
                    $sql_avg = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM tbl_review WHERE food_id = $id";
                    $res_avg = mysqli_query($conn, $sql_avg);
                    $row_avg = mysqli_fetch_assoc($res_avg);
                    $avg_rating = round($row_avg['avg_rating'], 1);
                    $review_count = $row_avg['review_count'];
            ?>
                    <div class="food-card">
                        <div class="food-card-image">
                            <?php
                            if ($image_name == "") {
                                echo "<div class='error'>Image not Available.</div>";
                            } else {
                            ?>
                                <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" alt="<?php echo $title; ?>">
                            <?php
                            }
                            ?>
                            <?php if($featured == 'Yes'): ?>
                            <div class="food-badge">Featured</div>
                            <?php endif; ?>
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

                            <a href="<?php echo SITEURL; ?>order.php?food_id=<?php echo $id; ?>" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                                <i class="fas fa-shopping-cart"></i> Order Now
                            </a>

                            <!-- Review Section -->
                            <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 1rem;">
                                <h5 style="margin-bottom: 1rem; color: var(--text-dark);">
                                    <i class="fas fa-star" style="color: var(--accent-color);"></i> Reviews
                                </h5>

                                <!-- Add Review Form -->
                                <form action="<?php echo SITEURL; ?>submit-review.php" method="POST" style="margin-bottom: 1rem;">
                                    <input type="hidden" name="food_id" value="<?php echo $id; ?>">
                                    
                                    <div style="display: grid; grid-template-columns: 1fr auto; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <input type="text" name="customer_name" placeholder="Your Name" style="padding: 8px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.9rem;" required>
                                        <select name="rating" style="padding: 8px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.9rem;" required>
                                            <option value="">Rate</option>
                                            <option value="5">5 ⭐</option>
                                            <option value="4">4 ⭐</option>
                                            <option value="3">3 ⭐</option>
                                            <option value="2">2 ⭐</option>
                                            <option value="1">1 ⭐</option>
                                        </select>
                                    </div>
                                    
                                    <textarea name="comment" rows="2" placeholder="Share your experience (optional)" style="width: 100%; padding: 8px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.9rem; margin-bottom: 0.5rem; resize: vertical;"></textarea>
                                    
                                    <button type="submit" name="submit" class="btn btn-secondary" style="width: 100%; padding: 8px; font-size: 0.9rem;">
                                        <i class="fas fa-paper-plane"></i> Submit Review
                                    </button>
                                </form>

                                <!-- Recent Reviews -->
                                <div style="max-height: 200px; overflow-y: auto;">
                                    <?php
                                    $sql_reviews = "SELECT customer_name, rating, comment, review_date 
                                                   FROM tbl_review WHERE food_id = $id 
                                                   ORDER BY review_date DESC LIMIT 3";
                                    $res_reviews = mysqli_query($conn, $sql_reviews);
                                    
                                    if(mysqli_num_rows($res_reviews) > 0) {
                                        while ($row_review = mysqli_fetch_assoc($res_reviews)) {
                                            $review_name = htmlspecialchars($row_review['customer_name']);
                                            $review_rating = $row_review['rating'];
                                            $review_comment = htmlspecialchars($row_review['comment']);
                                            $review_date = date("M d, Y", strtotime($row_review['review_date']));
                                            ?>
                                            <div style="background: var(--light-bg); padding: 0.75rem; margin: 0.5rem 0; border-radius: 6px; border-left: 3px solid var(--primary-color);">
                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                                                    <strong style="font-size: 0.9rem; color: var(--text-dark);"><?php echo $review_name; ?></strong>
                                                    <div style="display: flex; align-items: center; gap: 0.25rem;">
                                                        <div style="color: var(--accent-color); font-size: 0.8rem;">
                                                            <?php 
                                                            for($i = 1; $i <= 5; $i++) {
                                                                echo $i <= $review_rating ? '★' : '☆';
                                                            }
                                                            ?>
                                                        </div>
                                                        <span style="font-size: 0.8rem; color: var(--text-light);"><?php echo $review_date; ?></span>
                                                    </div>
                                                </div>
                                                <?php if ($review_comment): ?>
                                                <p style="margin: 0; font-size: 0.85rem; color: var(--text-light); line-height: 1.4;"><?php echo $review_comment; ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        echo "<p style='text-align: center; color: var(--text-light); font-size: 0.9rem; padding: 1rem;'>No reviews yet. Be the first to review!</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<div class='error' style='grid-column: 1/-1; text-align: center; padding: 3rem;'>
                        <i class='fas fa-utensils' style='font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;'></i>
                        <h3>No food items available</h3>
                        <p>Please check back later for delicious options!</p>
                      </div>";
            }
            ?>
        </div>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>
```