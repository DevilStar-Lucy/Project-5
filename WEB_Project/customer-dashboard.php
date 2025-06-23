<?php include('partials-front/menu.php'); ?>

<?php
// Check if customer is logged in (simple session check)
if(!isset($_SESSION['customer_id']) && !isset($_GET['demo'])) {
    // For demo purposes, we'll create a demo session
    $_SESSION['customer_id'] = 1;
    $_SESSION['customer_name'] = 'John Doe';
    $_SESSION['customer_email'] = 'john@example.com';
}

$customer_id = $_SESSION['customer_id'] ?? 1;
$customer_name = $_SESSION['customer_name'] ?? 'Demo User';
$customer_email = $_SESSION['customer_email'] ?? 'demo@foodiehub.com';

// Get customer statistics
$sql_orders = "SELECT COUNT(*) as total_orders FROM tbl_order WHERE customer_email = '$customer_email'";
$res_orders = mysqli_query($conn, $sql_orders);
$total_orders = mysqli_fetch_assoc($res_orders)['total_orders'];

$sql_spent = "SELECT SUM(total) as total_spent FROM tbl_order WHERE customer_email = '$customer_email' AND status = 'Delivered'";
$res_spent = mysqli_query($conn, $sql_spent);
$total_spent = mysqli_fetch_assoc($res_spent)['total_spent'] ?? 0;

$sql_reviews = "SELECT COUNT(*) as total_reviews FROM tbl_review WHERE customer_name = '$customer_name'";
$res_reviews = mysqli_query($conn, $sql_reviews);
$total_reviews = mysqli_fetch_assoc($res_reviews)['total_reviews'];
?>

<section class="dashboard">
    <div class="dashboard-container">
        <aside class="dashboard-sidebar">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="width: 80px; height: 80px; background: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem;">
                    <i class="fas fa-user"></i>
                </div>
                <h3><?php echo $customer_name; ?></h3>
                <p style="color: var(--text-light); font-size: 0.9rem;"><?php echo $customer_email; ?></p>
            </div>
            
            <nav class="dashboard-nav">
                <ul>
                    <li><a href="#overview" class="nav-link active" onclick="showSection('overview')">
                        <i class="fas fa-tachometer-alt"></i> Overview
                    </a></li>
                    <li><a href="#orders" class="nav-link" onclick="showSection('orders')">
                        <i class="fas fa-shopping-bag"></i> My Orders
                    </a></li>
                    <li><a href="#favorites" class="nav-link" onclick="showSection('favorites')">
                        <i class="fas fa-heart"></i> Favorites
                    </a></li>
                    <li><a href="#reviews" class="nav-link" onclick="showSection('reviews')">
                        <i class="fas fa-star"></i> My Reviews
                    </a></li>
                    <li><a href="#profile" class="nav-link" onclick="showSection('profile')">
                        <i class="fas fa-user-edit"></i> Profile
                    </a></li>
                    <li><a href="#addresses" class="nav-link" onclick="showSection('addresses')">
                        <i class="fas fa-map-marker-alt"></i> Addresses
                    </a></li>
                    <li><a href="#payments" class="nav-link" onclick="showSection('payments')">
                        <i class="fas fa-credit-card"></i> Payment Methods
                    </a></li>
                </ul>
            </nav>
        </aside>

        <main class="dashboard-main">
            <!-- Overview Section -->
            <div id="overview" class="dashboard-section active">
                <div class="dashboard-header">
                    <h2><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>
                    <p>Welcome back, <?php echo $customer_name; ?>!</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_orders; ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--success-color), #229954);">
                        <div class="stat-number">৳<?php echo number_format($total_spent); ?></div>
                        <div class="stat-label">Total Spent</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, var(--accent-color), #d68910);">
                        <div class="stat-number"><?php echo $total_reviews; ?></div>
                        <div class="stat-label">Reviews Given</div>
                    </div>
                    <div class="stat-card" style="background: linear-gradient(135deg, #8e44ad, #7d3c98);">
                        <div class="stat-number">Gold</div>
                        <div class="stat-label">Member Status</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-top: 2rem;">
                    <div style="background: var(--light-bg); padding: 2rem; border-radius: var(--border-radius);">
                        <h3>Recent Orders</h3>
                        <?php
                        $sql_recent = "SELECT * FROM tbl_order WHERE customer_email = '$customer_email' ORDER BY order_date DESC LIMIT 3";
                        $res_recent = mysqli_query($conn, $sql_recent);
                        
                        if(mysqli_num_rows($res_recent) > 0) {
                            while($order = mysqli_fetch_assoc($res_recent)) {
                                ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: white; margin: 1rem 0; border-radius: 8px; box-shadow: var(--shadow);">
                                    <div>
                                        <h4><?php echo $order['food']; ?></h4>
                                        <p style="color: var(--text-light); margin: 0;">Order #<?php echo $order['id']; ?></p>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-weight: 600;">৳<?php echo $order['total']; ?></div>
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>" style="font-size: 0.8rem; padding: 4px 8px;">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<p>No recent orders found.</p>";
                        }
                        ?>
                    </div>
                    
                    <div style="background: var(--light-bg); padding: 2rem; border-radius: var(--border-radius);">
                        <h3>Quick Actions</h3>
                        <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
                            <a href="<?php echo SITEURL; ?>foods.php" class="btn btn-primary">
                                <i class="fas fa-utensils"></i> Order Food
                            </a>
                            <a href="#orders" class="btn btn-secondary" onclick="showSection('orders')">
                                <i class="fas fa-history"></i> View Orders
                            </a>
                            <a href="#favorites" class="btn btn-secondary" onclick="showSection('favorites')">
                                <i class="fas fa-heart"></i> My Favorites
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Section -->
            <div id="orders" class="dashboard-section">
                <div class="dashboard-header">
                    <h2><i class="fas fa-shopping-bag"></i> My Orders</h2>
                    <a href="<?php echo SITEURL; ?>foods.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Order
                    </a>
                </div>

                <div style="background: var(--light-bg); padding: 2rem; border-radius: var(--border-radius);">
                    <?php
                    $sql_all_orders = "SELECT * FROM tbl_order WHERE customer_email = '$customer_email' ORDER BY order_date DESC";
                    $res_all_orders = mysqli_query($conn, $sql_all_orders);
                    
                    if(mysqli_num_rows($res_all_orders) > 0) {
                        while($order = mysqli_fetch_assoc($res_all_orders)) {
                            ?>
                            <div style="background: white; padding: 1.5rem; margin: 1rem 0; border-radius: var(--border-radius); box-shadow: var(--shadow);">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                    <div>
                                        <h4><?php echo $order['food']; ?></h4>
                                        <p style="color: var(--text-light); margin: 0.5rem 0;">Order #<?php echo $order['id']; ?></p>
                                        <p style="color: var(--text-light); margin: 0;">
                                            <i class="fas fa-calendar"></i> <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?>
                                        </p>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 1.2rem; font-weight: 600; color: var(--primary-color);">৳<?php echo $order['total']; ?></div>
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                                    <a href="<?php echo SITEURL; ?>track-order.php?order_id=<?php echo $order['id']; ?>" class="btn btn-secondary" style="font-size: 0.9rem; padding: 8px 16px;">
                                        <i class="fas fa-truck"></i> Track Order
                                    </a>
                                    <a href="<?php echo SITEURL; ?>order.php?food_id=1" class="btn btn-primary" style="font-size: 0.9rem; padding: 8px 16px;">
                                        <i class="fas fa-redo"></i> Reorder
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<div style='text-align: center; padding: 3rem;'>
                                <i class='fas fa-shopping-bag' style='font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;'></i>
                                <h3>No orders yet</h3>
                                <p>Start exploring our delicious menu!</p>
                                <a href='" . SITEURL . "foods.php' class='btn btn-primary'>Browse Menu</a>
                              </div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Favorites Section -->
            <div id="favorites" class="dashboard-section">
                <div class="dashboard-header">
                    <h2><i class="fas fa-heart"></i> My Favorites</h2>
                </div>

                <div class="food-grid">
                    <?php
                    // For demo, show some popular foods as favorites
                    $sql_favorites = "SELECT * FROM tbl_food WHERE featured='Yes' LIMIT 4";
                    $res_favorites = mysqli_query($conn, $sql_favorites);
                    
                    if(mysqli_num_rows($res_favorites) > 0) {
                        while($food = mysqli_fetch_assoc($res_favorites)) {
                            ?>
                            <div class="food-card">
                                <div class="food-card-image">
                                    <img src="<?php echo SITEURL; ?>images/food/<?php echo $food['image_name']; ?>" alt="<?php echo $food['title']; ?>">
                                    <div class="food-badge" style="background: var(--error-color);">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                </div>
                                <div class="food-card-content">
                                    <h4><?php echo $food['title']; ?></h4>
                                    <p class="food-description"><?php echo substr($food['description'], 0, 80) . '...'; ?></p>
                                    <div class="food-price">৳<?php echo $food['price']; ?></div>
                                    <a href="<?php echo SITEURL; ?>order.php?food_id=<?php echo $food['id']; ?>" class="btn btn-primary" style="width: 100%;">
                                        <i class="fas fa-shopping-cart"></i> Order Now
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<div style='text-align: center; padding: 3rem; grid-column: 1/-1;'>
                                <i class='fas fa-heart' style='font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;'></i>
                                <h3>No favorites yet</h3>
                                <p>Add foods to your favorites by clicking the heart icon!</p>
                              </div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Reviews Section -->
            <div id="reviews" class="dashboard-section">
                <div class="dashboard-header">
                    <h2><i class="fas fa-star"></i> My Reviews</h2>
                </div>

                <div style="background: var(--light-bg); padding: 2rem; border-radius: var(--border-radius);">
                    <?php
                    $sql_user_reviews = "SELECT r.*, f.title as food_title FROM tbl_review r 
                                        JOIN tbl_food f ON r.food_id = f.id 
                                        WHERE r.customer_name = '$customer_name' 
                                        ORDER BY r.review_date DESC";
                    $res_user_reviews = mysqli_query($conn, $sql_user_reviews);
                    
                    if(mysqli_num_rows($res_user_reviews) > 0) {
                        while($review = mysqli_fetch_assoc($res_user_reviews)) {
                            ?>
                            <div style="background: white; padding: 1.5rem; margin: 1rem 0; border-radius: var(--border-radius); box-shadow: var(--shadow);">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                    <div>
                                        <h4><?php echo $review['food_title']; ?></h4>
                                        <div class="food-rating">
                                            <div class="stars">
                                                <?php 
                                                for($i = 1; $i <= 5; $i++) {
                                                    if($i <= $review['rating']) {
                                                        echo '<i class="fas fa-star"></i>';
                                                    } else {
                                                        echo '<i class="far fa-star"></i>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <span style="color: var(--text-light); font-size: 0.9rem;">
                                        <?php echo date('M d, Y', strtotime($review['review_date'])); ?>
                                    </span>
                                </div>
                                <?php if($review['comment']): ?>
                                <p style="color: var(--text-light);"><?php echo $review['comment']; ?></p>
                                <?php endif; ?>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<div style='text-align: center; padding: 3rem;'>
                                <i class='fas fa-star' style='font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;'></i>
                                <h3>No reviews yet</h3>
                                <p>Share your experience by reviewing the foods you've ordered!</p>
                              </div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Profile Section -->
            <div id="profile" class="dashboard-section">
                <div class="dashboard-header">
                    <h2><i class="fas fa-user-edit"></i> Profile Settings</h2>
                </div>

                <div style="background: var(--light-bg); padding: 2rem; border-radius: var(--border-radius);">
                    <form style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" value="<?php echo $customer_name; ?>">
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" value="<?php echo $customer_email; ?>">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" class="form-control" value="+880 1234 567890">
                        </div>
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" class="form-control" value="1990-01-01">
                        </div>
                        <div class="form-group" style="grid-column: 1/-1;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Addresses Section -->
            <div id="addresses" class="dashboard-section">
                <div class="dashboard-header">
                    <h2><i class="fas fa-map-marker-alt"></i> Delivery Addresses</h2>
                    <button class="btn btn-primary" onclick="showAddAddressForm()">
                        <i class="fas fa-plus"></i> Add Address
                    </button>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                    <div style="background: white; padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow); border: 2px solid var(--primary-color);">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <h4><i class="fas fa-home"></i> Home</h4>
                            <span style="background: var(--primary-color); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">Default</span>
                        </div>
                        <p style="color: var(--text-light); margin-bottom: 1rem;">
                            123 Main Street, Apartment 4B<br>
                            Dhaka 1000, Bangladesh
                        </p>
                        <div style="display: flex; gap: 1rem;">
                            <button class="btn btn-secondary" style="font-size: 0.9rem; padding: 8px 16px;">Edit</button>
                            <button class="btn btn-danger" style="font-size: 0.9rem; padding: 8px 16px;">Delete</button>
                        </div>
                    </div>

                    <div style="background: white; padding: 1.5rem; border-radius: var(--border-radius); box-shadow: var(--shadow);">
                        <h4><i class="fas fa-briefcase"></i> Office</h4>
                        <p style="color: var(--text-light); margin-bottom: 1rem;">
                            456 Business District<br>
                            Gulshan, Dhaka 1212, Bangladesh
                        </p>
                        <div style="display: flex; gap: 1rem;">
                            <button class="btn btn-secondary" style="font-size: 0.9rem; padding: 8px 16px;">Edit</button>
                            <button class="btn btn-danger" style="font-size: 0.9rem; padding: 8px 16px;">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods Section -->
            <div id="payments" class="dashboard-section">
                <div class="dashboard-header">
                    <h2><i class="fas fa-credit-card"></i> Payment Methods</h2>
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Payment Method
                    </button>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: var(--border-radius); position: relative;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem;">
                            <div>
                                <h4>Visa Card</h4>
                                <p style="opacity: 0.8;">**** **** **** 1234</p>
                            </div>
                            <i class="fab fa-cc-visa" style="font-size: 2rem;"></i>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: end;">
                            <div>
                                <p style="opacity: 0.8; margin: 0;">John Doe</p>
                                <p style="opacity: 0.8; margin: 0;">12/25</p>
                            </div>
                            <span style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">Default</span>
                        </div>
                    </div>

                    <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--shadow); display: flex; align-items: center; justify-content: center; border: 2px dashed var(--border-color);">
                        <div style="text-align: center;">
                            <i class="fas fa-plus" style="font-size: 2rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                            <p style="color: var(--text-light);">Add New Payment Method</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</section>

<script>
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.dashboard-section').forEach(section => {
        section.classList.remove('active');
        section.style.display = 'none';
    });
    
    // Show selected section
    const targetSection = document.getElementById(sectionId);
    if (targetSection) {
        targetSection.style.display = 'block';
        targetSection.classList.add('active');
    }
    
    // Update navigation
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });
    
    event.target.classList.add('active');
}

function showAddAddressForm() {
    alert('Add Address form would open here in a modal or new page');
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    showSection('overview');
});
</script>

<style>
.dashboard-section {
    display: none;
}

.dashboard-section.active {
    display: block;
}

.status-ordered {
    background: #fff3cd;
    color: #856404;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-confirmed-cod {
    background: #d4edda;
    color: #155724;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-paid {
    background: #d4edda;
    color: #155724;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-delivered {
    background: #d4edda;
    color: #155724;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
}
</style>

<?php include('partials-front/footer.php'); ?>
```