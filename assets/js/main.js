document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenuClose = document.querySelector('.mobile-menu-close');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.add('active');
            document.body.classList.add('menu-open');
        });
    }
    
    if (mobileMenuClose && mobileMenu) {
        mobileMenuClose.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            document.body.classList.remove('menu-open');
        });
    }
    
    // Profile dropdown toggle
    const profileToggle = document.querySelector('.profile-toggle');
    const profileDropdown = document.querySelector('.profile-dropdown');
    
    if (profileToggle && profileDropdown) {
        profileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });
        
        document.addEventListener('click', function() {
            profileDropdown.classList.remove('active');
        });
    }
    
    // Animate elements on scroll
    const animatedElements = document.querySelectorAll('.animated:not(.fadeIn):not(.fadeInDown):not(.fadeInUp)');
    
    if (animatedElements.length > 0) {
        const animateOnScroll = function() {
            animatedElements.forEach(function(element) {
                const elementPosition = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                
                if (elementPosition < windowHeight - 50) {
                    const delay = element.getAttribute('data-delay') || 0;
                    setTimeout(function() {
                        element.classList.add('fadeIn');
                    }, delay * 1000);
                }
            });
        };
        
        // Initial check
        animateOnScroll();
        
        // Check on scroll
        window.addEventListener('scroll', animateOnScroll);
    }
    
    // Video slider functionality
    const videoSlider = document.querySelector('.video-slider');
    
    if (videoSlider && videoSlider.children.length > 3) {
        let currentPosition = 0;
        const slideWidth = videoSlider.children[0].offsetWidth + 20; // Width + margin
        const maxPosition = videoSlider.children.length - 3;
        
        // Create navigation buttons
        const prevButton = document.createElement('button');
        prevButton.className = 'slider-nav prev';
        prevButton.innerHTML = '<i class="fas fa-chevron-left"></i>';
        
        const nextButton = document.createElement('button');
        nextButton.className = 'slider-nav next';
        nextButton.innerHTML = '<i class="fas fa-chevron-right"></i>';
        
        videoSlider.parentNode.appendChild(prevButton);
        videoSlider.parentNode.appendChild(nextButton);
        
        // Navigation functionality
        prevButton.addEventListener('click', function() {
            if (currentPosition > 0) {
                currentPosition--;
                videoSlider.style.transform = `translateX(-${currentPosition * slideWidth}px)`;
            }
        });
        
        nextButton.addEventListener('click', function() {
            if (currentPosition < maxPosition) {
                currentPosition++;
                videoSlider.style.transform = `translateX(-${currentPosition * slideWidth}px)`;
            }
        });
    }
});