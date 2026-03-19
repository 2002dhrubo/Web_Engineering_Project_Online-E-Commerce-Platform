// Smart Buy Store - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    
    // Add to Cart - Product Grid
    document.querySelectorAll('.btn-add-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productId = this.dataset.id;
            addToCart(productId, 1);
        });
    });

    // Add to Cart - Product Detail Page
    const detailBtn = document.querySelector('.btn-add-cart-large');
    if (detailBtn) {
        detailBtn.addEventListener('click', function() {
            const productId = this.dataset.id;
            const quantity = document.getElementById('quantity').value;
            addToCart(productId, quantity);
        });
    }

    // Quantity Controls on Product Detail
    const qtyMinus = document.getElementById('qtyMinus');
    const qtyPlus = document.getElementById('qtyPlus');
    const qtyInput = document.getElementById('quantity');

    if (qtyMinus && qtyPlus && qtyInput) {
        qtyMinus.addEventListener('click', function() {
            let val = parseInt(qtyInput.value);
            if (val > 1) qtyInput.value = val - 1;
        });

        qtyPlus.addEventListener('click', function() {
            let val = parseInt(qtyInput.value);
            if (val < 10) qtyInput.value = val + 1;
        });
    }

    // Search Functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const brand = card.querySelector('.brand')?.textContent.toLowerCase() || '';
                if (name.includes(query) || brand.includes(query)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});

// Add to Cart Function
function addToCart(productId, quantity) {
    fetch('api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            product_id: productId,
            quantity: parseInt(quantity)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.cart_count);
            showNotification('Added to cart!');
        } else if (data.require_login) {
            showNotification('Please login to add items to cart', 'error');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 1500);
        } else {
            showNotification('Failed to add to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding to cart', 'error');
    });
}

// Update Cart Badge
function updateCartBadge(count) {
    const badge = document.querySelector('.cart-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.transform = 'scale(1.3)';
        setTimeout(() => badge.style.transform = 'scale(1)', 200);
    }
}

// Show Notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = 'notification ' + type;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === 'success' ? 'linear-gradient(135deg, #4CAF50, #8BC34A)' : 'linear-gradient(135deg, #e74c3c, #c0392b)'};
        color: white;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 2500);
}

// Add CSS animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
