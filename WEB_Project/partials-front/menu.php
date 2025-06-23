<?php include('config/constants.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodieHub - Delicious Food Delivered</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <nav class="navbar" id="navbar">
        <div class="container">
            <div class="logo">
                <img src="images/main.png" alt="FoodieHub Logo">
                <span>FoodieHub</span>
            </div>

            <div class="menu">
                <ul>
                    <li><a href="<?php echo SITEURL; ?>" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Home
                    </a></li>
                    <li><a href="<?php echo SITEURL; ?>categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                        <i class="fas fa-th-large"></i> Categories
                    </a></li>
                    <li><a href="<?php echo SITEURL; ?>foods.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'foods.php' ? 'active' : ''; ?>">
                        <i class="fas fa-utensils"></i> Menu
                    </a></li>
                    <li><a href="<?php echo SITEURL; ?>customer-dashboard.php">
                        <i class="fas fa-user"></i> Dashboard
                    </a></li>
                    <li><a href="#contact">
                        <i class="fas fa-phone"></i> Contact
                    </a></li>
                </ul>
            </div>
        </div>
    </nav>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.querySelector('.menu');
            menu.classList.toggle('active');
        }
    </script>
</body>
</html>
```