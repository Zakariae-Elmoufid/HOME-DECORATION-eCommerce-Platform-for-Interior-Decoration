const checkoutForm = document.getElementById('checkout-form'); 
const subtotalElement = document.getElementById('subtotal');   
const shippingElement = document.getElementById('shipping');   
const totalElement = document.getElementById('total'); 
let items = Array.from(document.querySelectorAll('.item'));
console.log(items);
let cardElement;

let shippingValue = 0.00;



function calculSubtotal() {
    const subtotal = items.reduce((sum, item) => {
        const itemTotal = parseFloat(item.dataset.totalItem);
        return isNaN(itemTotal) ? sum : sum + itemTotal;
    }, 0);

    subtotalElement.textContent = "$ " + subtotal.toFixed(2);
    return subtotal;
}

function totalAmount() {
    const subtotal = calculSubtotal();
    return subtotal + shippingValue;
}

function updateTotalDisplay() {
    const total = totalAmount();
    totalElement.textContent = "$ " + total.toFixed(2);
}

function getDataItems() {
    return items.map(item => item.dataset.idItem);
}




function clearErrors() {
    document.querySelectorAll('[id^="error_"]').forEach(errorElement => {
        errorElement.textContent = '';
    });
}

function showErrors(errors) {
    clearErrors();
    
    Object.entries(errors).forEach(([field, messages]) => {
        const errorElement = document.querySelector(`#error_${field}`);
        if (errorElement) {
            errorElement.textContent = Array.isArray(messages) ? messages.join(', ') : messages;
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    calculSubtotal();
    updateTotalDisplay();
    setupStripeElements();
});

checkoutForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitButton = checkoutForm.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="animate-spin inline-block mr-2">⟳</span> Processing...';
    
    clearErrors();
    
    const formData = new FormData(checkoutForm);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    data.items = getDataItems();
    data.subTotal = calculSubtotal();
    data.totalAmount = totalAmount();
    
    // try {
        const orderResponse = await fetch('/payment/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });
        
        const orderResult = await orderResponse.json();
        if (orderResult.success && orderResult.url) {
            window.location.href = orderResult.url; // Redirection normale
        }
        
        if (orderResult.errors) {
            showErrors(orderResult.errors);
            submitButton.disabled = false;
            submitButton.textContent = 'Place Order';
            return;
        }
        
        if (!orderResult.success) {
            throw new Error(orderResult.message || 'Failed to create order');
        }
        
    
      
        
    // } catch (error) {
    //     console.error('Error:', error);
        
    //     // Afficher le message d'erreur
    //     const errorContainer = document.createElement('div');
    //     errorContainer.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4';
    //     errorContainer.textContent = error.message || 'An unexpected error occurred. Please try again.';
        
    //     checkoutForm.prepend(errorContainer);
        
    //     // Réactiver le bouton de soumission
    //     submitButton.disabled = false;
    //     submitButton.textContent = 'Place Order';
    //     // Défiler vers le haut pour montrer l'erreur
    //     window.scrollTo({ top: 0, behavior: 'smooth' });
    // }
});