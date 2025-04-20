</main>
    
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <a href="<?= SITE_URL ?>">
                        <i class="fas fa-play-circle"></i>
                        <span><?= SITE_NAME ?></span>
                    </a>
                    <p>Your ultimate video watching platform with progress tracking.</p>
                </div>
                
                <div class="footer-links">
                    <div class="footer-section">
                        <h3>Navigation</h3>
                        <ul>
                            <li><a href="<?= SITE_URL ?>">Home</a></li>
                            <li><a href="<?= SITE_URL ?>/dashboard.php">Explore</a></li>
                            <?php if (isLoggedIn()): ?>
                                <li><a href="<?= SITE_URL ?>/profile.php">Profile</a></li>
                            <?php else: ?>
                                <li><a href="<?= SITE_URL ?>/auth/login.php">Login</a></li>
                                <li><a href="<?= SITE_URL ?>/auth/register.php">Register</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="footer-section">
                        <h3>Categories</h3>
                        <ul>
                            <?php 
                            $footerCategories = getAllCategories();
                            $footerCategories = array_slice($footerCategories, 0, 5);
                            foreach ($footerCategories as $category): 
                            ?>
                                <li>
                                    <a href="<?= SITE_URL ?>/dashboard.php?category=<?= urlencode($category['name']) ?>">
                                        <?= $category['name'] ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="footer-section">
                        <h3>Support</h3>
                        <ul>
                            <li><a href="#">Help Center</a></li>
                            <li><a href="#">Contact Us</a></li>
                            <li><a href="#">FAQ</a></li>
                            <li><a href="#">Terms of Service</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= SITE_URL ?>/assets/js/main.js"></script>
    
    <?php if (isset($extraJS)): ?>
        <?php foreach ($extraJS as $js): ?>
            <script src="<?= SITE_URL ?>/assets/js/<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>