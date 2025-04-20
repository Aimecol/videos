document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminContainer = document.querySelector('.admin-container');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            adminContainer.classList.toggle('sidebar-collapsed');
            
            // Save state to localStorage
            const isCollapsed = adminContainer.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
        
        // Check localStorage on page load
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            adminContainer.classList.add('sidebar-collapsed');
        }
    }
    
    // Profile dropdown functionality
    const profileDropdown = document.querySelector('.profile-dropdown');
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            dropdownToggle.classList.toggle('active');
            dropdownMenu.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target)) {
                dropdownToggle.classList.remove('active');
                dropdownMenu.classList.remove('active');
            }
        });
    }
    
    // Modal functionality
    const modalTriggers = document.querySelectorAll('[data-modal]');
    
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal');
            const modal = document.getElementById(modalId);
            
            if (modal) {
                modal.classList.add('active');
                
                // Close modal when clicking on close button or outside
                const closeButtons = modal.querySelectorAll('.modal-close');
                closeButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        modal.classList.remove('active');
                    });
                });
                
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.remove('active');
                    }
                });
            }
        });
    });
    
    // File input preview for thumbnails
    const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewId = this.getAttribute('data-preview');
            const previewElement = document.getElementById(previewId);
            
            if (previewElement && this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if (previewElement.tagName === 'IMG') {
                        previewElement.src = e.target.result;
                    } else {
                        previewElement.style.backgroundImage = `url(${e.target.result})`;
                    }
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    
    // Confirmation dialogs for delete actions
    const deleteButtons = document.querySelectorAll('.delete-confirm');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});