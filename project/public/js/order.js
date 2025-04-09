const checkoutForm = document.getElementById('checkout-form'); 
const subtotalElement = document.getElementById('subtotal');   
const shippingElement = document.getElementById('shipping');   
const totalElement = document.getElementById('total'); 
let items = Array.from(document.querySelectorAll('.item'));

const stripe = Stripe('pk_test_51RAgELH2nPPbXqXk68Ynch9G4PJaymfMp7d4fG5QlpM6aFwskq2VeIVQYa60f6yzmyysePWNOoH2c892RsuKULHL00cJOASWRW');
const elements = stripe.elements();
let cardElement;

let shippingValue = 0.00;

function getShippingValue() {
    const shippingMethode = document.querySelector('[name="shipping_method"]:checked');
    
    if (!shippingMethode) {
        console.warn('No shipping method selected');
        return 0.00;
    }
    const value = parseFloat(shippingMethode.value);

    shippingElement.textContent = "$ " + value.toFixed(2);
    
    if (isNaN(value)) {
        console.error('Invalid shipping value');
        return 0.00;
    }
    
    return value;
}

checkoutForm.addEventListener('change', (event) => {
    if (event.target.name === 'shipping_method') {
        shippingValue = getShippingValue();
        updateTotalDisplay();
    }
    
    if (event.target.name === 'payment_method') {
        const creditCardInfo = document.getElementById('credit-card-info');
        if (creditCardInfo) {
            creditCardInfo.style.display = event.target.value === 'credit_card' ? 'block' : 'none';
        }
    }
});

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


function setupStripeElements() {
    //create cart element 
    cardElement = elements.create('card', {
        style: {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        }
    });
    
    const creditCardInfo = document.getElementById('credit-card-info');
    if (creditCardInfo) {

        creditCardInfo.innerHTML = '';
        
        const cardLabel = document.createElement('label');
        cardLabel.htmlFor = 'card-element';
        cardLabel.className = 'block text-sm font-medium text-gray-700 mb-1';
        cardLabel.textContent = 'Credit Card Details*';
        
        const cardElementContainer = document.createElement('div');
        cardElementContainer.id = 'card-element';
        cardElementContainer.className = 'w-full px-4 py-2 border border-gray-300 rounded-md';
        
        const cardErrors = document.createElement('div');
        cardErrors.id = 'card-errors';
        cardErrors.className = 'text-red-500 text-sm mt-2';
        
        creditCardInfo.appendChild(cardLabel);
        creditCardInfo.appendChild(cardElementContainer);
        creditCardInfo.appendChild(cardErrors);
        
        cardElement.mount('#card-element');

        cardElement.addEventListener('change', (event) => {
            if (event.error) {
                cardErrors.textContent = event.error.message;
            } else {
                cardErrors.textContent = '';
            }
        });
    }
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
    
    try {
        // 1. create order
        const orderResponse = await fetch('/order/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });
        
        const orderResult = await orderResponse.json();
        
        if (orderResult.errors) {
            showErrors(orderResult.errors);
            submitButton.disabled = false;
            submitButton.textContent = 'Place Order';
            return;
        }
        
        if (!orderResult.success) {
            throw new Error(orderResult.message || 'Failed to create order');
        }
        
        const orderId = orderResult.order_id;
        const paymentMethod = data.payment_method;
        
        // 2. Process the payment according to the chosen method
        if (paymentMethod === 'credit_card') {
        
            const paymentIntentResponse = await fetch('/payment/create-intent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId,
                    email: data.email
                })
            });
            
            const paymentIntentResult = await paymentIntentResponse.json();
            
            if (!paymentIntentResult.success) {
                throw new Error(paymentIntentResult.error || 'Failed to create payment intent');
            }
            
            const { error, paymentIntent } = await stripe.confirmCardPayment(
                paymentIntentResult.client_secret,
                {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: `${data.first_name} ${data.last_name}`,
                            email: data.email,
                            phone: data.phone,
                            address: {
                                line1: data.address,
                                city: data.city,
                                postal_code: data.postal_code,
                                country: data.country
                            }
                        }
                    }
                }
            );
            
            if (error) {
                const cardErrors = document.getElementById('card-errors');
                if (cardErrors){
                    cardErrors.textContent = error.message;
                }
                
                submitButton.disabled = false;
                submitButton.textContent = 'Place Order';
                return;
            }
              
            await fetch('/payment/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    payment_intent_id: paymentIntent.id,
                    status: paymentIntent.status
                })
            });
            
            window.location.href = `/payment/confirmation?id=${orderId}`;
            
        } else if (paymentMethod === 'paypal') {
            window.location.href = `/payment/paypal?id=${orderId}`;

        }
        
    } catch (error) {
        console.error('Error:', error);
        
        // Afficher le message d'erreur
        const errorContainer = document.createElement('div');
        errorContainer.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4';
        errorContainer.textContent = error.message || 'An unexpected error occurred. Please try again.';
        
        checkoutForm.prepend(errorContainer);
        
        // Réactiver le bouton de soumission
        submitButton.disabled = false;
        submitButton.textContent = 'Place Order';
        
        // Défiler vers le haut pour montrer l'erreur
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});