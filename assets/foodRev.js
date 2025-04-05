// foodRev.js - Main JavaScript for FoodRev application

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    console.log('FoodRev application initialized');
    
    // You can add global event listeners here
    // Or initialize specific page components if needed
    
    // Example: Initialize tooltips for restaurant cards
    const restaurantCards = document.querySelectorAll('.foodrev-restaurant-card');
    restaurantCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            // Could show additional info on hover
        });
    });
    
    // Initialize any other global functionality
});

// Add any global functions here that might be used across pages
function showToast(message, type = 'success') {
    // Implementation for toast notifications
    console.log(`Toast: ${message} (${type})`);
}