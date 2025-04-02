const cartBtns = document.querySelectorAll('.add-cart');

cartBtns.forEach(button => {
    button.addEventListener('click', async (e) => {
        e.preventDefault();

        const productId = button.getAttribute('data-product-id');
        const quantityInput = document.querySelector('#quantity');
        const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
        const data = {
            product_id: productId,
            quantity: quantity,


        };
        console.log(data);

            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

        

            const result = await response.json();
            console.log(result);
       
    });
});
