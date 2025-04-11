import displayMessage from "./alert.js"

const wishlistBtn = document.getElementById('add-to-wishlist-btn');

const productId  = wishlistBtn.dataset.productId;

wishlistBtn.addEventListener('click',async function () {

    const Response = await fetch('/wishlist/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({product_id : productId}),
    });
    const result = await Response.json();
    if(result.errore){
        displayMessage(result.errore,`/product?id=${productId}` , 'errore');
    }
} )