import {displayMessage} from "./alert.js"
import updateCount from "./main.js"

    const cartElement = document.querySelector('[data-id-cart]');
    
        const cart = cartElement.dataset.idCart;
        export default cart;


const increaseBtn = document.querySelectorAll('.increase');
const decreaseBtn = document.querySelectorAll('.decrease');


const items = document.querySelectorAll('.item');
Array.from(increaseBtn).forEach(button => {
    
    button.addEventListener('click', function(e) {
        const input = this.parentElement.querySelector('input');
        const currentQuantity = parseInt(input.value);

        
        const stockQuantity  = input.dataset.stock;
        console.log(input.dataset.stock);
        
        ;
        if (currentQuantity < stockQuantity) {
            input.value = currentQuantity + 1;
            updateTotals();
            updateCount();
         }
    });
})

decreaseBtn.forEach(button => {
    button.addEventListener('click', function(e) {
        const input = this.parentElement.querySelector('input');
        const currentQuantity = parseInt(input.value);

        if (currentQuantity > 1) {
          input.value = currentQuantity - 1;
          updateTotals();
          updateCount();
        }
    });
});

const subtotalElement = document.querySelector('.sub-total');
const shippingElement = document.querySelector('.shipping');
const totalElement = document.querySelector('.total');


let cartSubtotal = 0;
const shippingCost = 0;

function updateTotals() {
    cartSubtotal = 0;
    

items.forEach(item => {
    const price = parseFloat(item.dataset.productPrice);
    const quantity = parseFloat(item.querySelector('input[type="number"]').value);
    const itemTotalElement = item.querySelector('.sub-total-item');
   
    const itemTotal = price * quantity;
    item.dataset.productTotal = itemTotal;
    itemTotalElement.textContent = '$' + itemTotal.toFixed(2);
    cartSubtotal += itemTotal;

});
 let cartTotal = cartSubtotal + shippingCost;

subtotalElement.textContent = '$' + cartSubtotal.toFixed(2);
shippingElement.textContent = '$' + shippingCost.toFixed(2);
totalElement.textContent = '$' + cartTotal.toFixed(2);
}

updateTotals();

let idCart ;
const updateCartButton = document.getElementById('update-cart');

updateCartButton.addEventListener('click' ,async function(){
    
    const cartData = [];

    items.forEach(item => {
        const cartId = item.dataset.cartId;
        const itemId = item.getAttribute('data-item-id') || '0';
        const quantity = item.querySelector('input[type="number"]').value;

        
        const subtotal = item.dataset.productTotal;
        
        
        cartData.push({  
          id: itemId,
          cart_id : cartId,
          quantity: parseInt(quantity),
          total_item : parseFloat(subtotal),
          total : parseFloat(totalElement.textContent.replace('$',''))
        });

    });

    const response = await fetch('/cart/update', {
        method: 'patch',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(cartData),
    });
    
    const result = await response.json();
    if(result.success) {
        displayMessage(result.success,'/cart');
        updateCount();
    }

})


const removes  =  Array.from(document.getElementsByClassName('remove'));

removes.forEach(button => {
    button.addEventListener('click' , async function(e){
        e.preventDefault();
      const itemId =  button.dataset.itemId;
       const data = {
        id : itemId
       };
       const response = await    fetch(`/cart/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json' 
                },
                body: JSON.stringify(data),
              })
              const result = await response.json();
              if(result.success) {
                  updateCount();
                displayMessage(result.success,'/cart');
               }
             
              
    })

})
