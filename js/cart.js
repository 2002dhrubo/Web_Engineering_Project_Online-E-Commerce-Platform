// Smart Buy Store - Cart Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    
    // Quantity Plus Buttons
    document.querySelectorAll('.qty-btn.plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.closest('.cart-item');
            const productId = item.dataset.id;
            const qtySpan = item.querySelector('.qty');
            const newQty = parseInt(qtySpan.textContent) + 1;
            updateCartItem(productId, newQty);
        });
    });

    // Quantity Minus Buttons
    document.querySelectorAll('.qty-btn.minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.closest('.cart-item');
            const productId = item.dataset.id;
            const qtySpan = item.querySelector('.qty');
            const newQty = parseInt(qtySpan.textContent) - 1;
            if (newQty >= 1) {
                updateCartItem(productId, newQty);
            }
        });
    });

    // Remove Item Buttons
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = this.closest('.cart-item');
            const productId = item.dataset.id;
            
            if (confirm('Remove this item from cart?')) {
                removeFromCart(productId);
            }
        });
    });

    // Place Order Button
    const placeOrderBtn = document.getElementById('placeOrder');
    if (placeOrderBtn) {
        placeOrderBtn.addEventListener('click', placeOrder);
    }
});

// Update Cart Item Quantity
function updateCartItem(productId, quantity) {
    fetch('api/cart.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else if (data.require_login) {
            window.location.href = 'login.php';
        }
    })
    .catch(error => console.error('Error:', error));
}

// Remove Item from Cart
function removeFromCart(productId) {
    fetch('api/cart.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else if (data.require_login) {
            window.location.href = 'login.php';
        }
    })
    .catch(error => console.error('Error:', error));
}

// Place Order
function placeOrder() {
    const btn = document.getElementById('placeOrder');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    fetch('api/order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'order-success.php?id=' + data.order_id;
        } else if (data.require_login) {
            window.location.href = 'login.php';
        } else {
            alert(data.message || 'Failed to place order');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-lock"></i> Place Order';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error placing order');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-lock"></i> Place Order';
    });
}
