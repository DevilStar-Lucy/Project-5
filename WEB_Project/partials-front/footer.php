<footer class="footer" id="contact">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>FoodieHub</h3>
                <p>Delivering happiness, one meal at a time. Experience the best flavors from top restaurants in your city.</p>
                <div class="social-links">
                    <a href="https://github.com/SparksFlash" target="_blank">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/sandipta-saha-b25455279" target="_blank">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="#" target="_blank">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="#" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" target="_blank">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="<?php echo SITEURL; ?>">Home</a></li>
                    <li><a href="<?php echo SITEURL; ?>categories.php">Categories</a></li>
                    <li><a href="<?php echo SITEURL; ?>foods.php">Menu</a></li>
                    <li><a href="<?php echo SITEURL; ?>customer-dashboard.php">Dashboard</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Customer Service</h3>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Track Your Order</a></li>
                    <li><a href="#">Returns & Refunds</a></li>
                    <li><a href="#">Contact Support</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Contact Info</h3>
                <ul>
                    <li><i class="fas fa-phone"></i> +880 1234 567890</li>
                    <li><i class="fas fa-envelope"></i> info@foodiehub.com</li>
                    <li><i class="fas fa-map-marker-alt"></i> Dhaka, Bangladesh</li>
                    <li><i class="fas fa-clock"></i> 24/7 Service</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 FoodieHub. All rights reserved. | Developed with ❤️ by <a href="https://github.com/SparksFlash" target="_blank">Sandipto Saha</a></p>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" style="position: fixed; bottom: 20px; right: 20px; background: var(--primary-color); color: white; border: none; border-radius: 50%; width: 50px; height: 50px; cursor: pointer; display: none; z-index: 1000; box-shadow: var(--shadow-hover); transition: var(--transition);">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
// Back to top functionality
window.addEventListener('scroll', function() {
    const backToTop = document.getElementById('backToTop');
    if (window.scrollY > 300) {
        backToTop.style.display = 'block';
    } else {
        backToTop.style.display = 'none';
    }
});

document.getElementById('backToTop').addEventListener('click', function() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add loading animation to buttons
document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (this.type === 'submit') {
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="loading"></span> Processing...';
            this.disabled = true;
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 2000);
        }
    });
});

// Add hover effects to cards
document.querySelectorAll('.food-card, .category-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-8px) scale(1.02)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});
</script>

</body>
</html>
```