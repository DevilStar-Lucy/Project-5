<?php include('partials-front/menu.php'); ?>

<section class="hero" style="padding: 120px 0 60px;">
    <div class="container">
        <div class="hero-content">
            <h1>Food Categories</h1>
            <p>Discover amazing dishes organized by categories. From appetizers to desserts, find exactly what you're craving.</p>
        </div>
    </div>
</section>

<section class="categories" style="padding: 60px 0;">
    <div class="container">
        <div class="categories-grid">
            <?php 
                $sql = "SELECT * FROM tbl_category WHERE active='Yes' ORDER BY title ASC";
                $res = mysqli_query($conn, $sql);
                $count = mysqli_num_rows($res);

                if($count > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $id = $row['id'];
                        $title = $row['title'];
                        $image_name = $row['image_name'];
                        $featured = $row['featured'];
                        
                        // Count foods in this category
                        $sql_count = "SELECT COUNT(*) as food_count FROM tbl_food WHERE category_id = $id AND active = 'Yes'";
                        $res_count = mysqli_query($conn, $sql_count);
                        $food_count = mysqli_fetch_assoc($res_count)['food_count'];
                        ?>
                        <a href="<?php echo SITEURL; ?>category-foods.php?category_id=<?php echo $id; ?>" class="category-card">
                            <?php 
                                if($image_name == "") {
                                    echo "<div class='error'>Image not found.</div>";
                                } else {
                                    ?>
                                    <img src="<?php echo SITEURL; ?>images/category/<?php echo $image_name; ?>" alt="<?php echo $title; ?>">
                                    <?php
                                }
                            ?>
                            
                            <?php if($featured == 'Yes'): ?>
                            <div style="position: absolute; top: 15px; right: 15px; background: var(--primary-color); color: white; padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: 600;">
                                Featured
                            </div>
                            <?php endif; ?>
                            
                            <div class="category-overlay">
                                <h3><?php echo $title; ?></h3>
                                <p><?php echo $food_count; ?> delicious items available</p>
                                <div style="margin-top: 1rem;">
                                    <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; font-weight: 500;">
                                        <i class="fas fa-arrow-right"></i> Explore Now
                                    </span>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                } else {
                    echo "<div class='error' style='grid-column: 1/-1; text-align: center; padding: 3rem;'>
                            <i class='fas fa-th-large' style='font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;'></i>
                            <h3>No categories available</h3>
                            <p>Please check back later for new categories!</p>
                          </div>";
                }
            ?>
        </div>
    </div>
</section>

<!-- Popular Categories Section -->
<section style="padding: 60px 0; background: var(--light-bg);">
    <div class="container">
        <h2 class="text-center">Most Popular Categories</h2>
        <p class="text-center" style="margin-bottom: 3rem; color: var(--text-light);">
            These categories are loved by our customers
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
            <?php 
                $sql_popular = "SELECT c.*, COUNT(o.id) as order_count 
                               FROM tbl_category c 
                               LEFT JOIN tbl_food f ON c.id = f.category_id 
                               LEFT JOIN tbl_order o ON f.title = o.food 
                               WHERE c.active = 'Yes' 
                               GROUP BY c.id 
                               ORDER BY order_count DESC 
                               LIMIT 4";
                $res_popular = mysqli_query($conn, $sql_popular);
                
                if(mysqli_num_rows($res_popular) > 0) {
                    while($row = mysqli_fetch_assoc($res_popular)) {
                        ?>
                        <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow); text-align: center; transition: var(--transition);" 
                             onmouseover="this.style.transform='translateY(-5px)'" 
                             onmouseout="this.style.transform='translateY(0)'">
                            <div style="width: 80px; height: 80px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem;">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <h4><?php echo $row['title']; ?></h4>
                            <p style="color: var(--text-light); margin-bottom: 1rem;"><?php echo $row['order_count']; ?> orders</p>
                            <a href="<?php echo SITEURL; ?>category-foods.php?category_id=<?php echo $row['id']; ?>" class="btn btn-secondary">
                                View Items
                            </a>
                        </div>
                        <?php
                    }
                }
            ?>
        </div>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>
```