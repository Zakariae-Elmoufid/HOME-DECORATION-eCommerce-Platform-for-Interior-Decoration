import updateCount from "./main.js "
import {displayMessage} from "./alert.js"


const mainImage = document.getElementById('main-product-image');
const thumbnails = document.querySelectorAll('.image-thumbnail');
thumbnails.forEach(thumbnail => {
  thumbnail.addEventListener('click', function() {
    mainImage.src = this.dataset.image;
    
    thumbnails.forEach(thumb => {
      thumb.classList.remove('border-2', 'border-gold');
      thumb.classList.add('border', 'border-gray-200');
    });
    
    this.classList.remove('border', 'border-gray-200');
    this.classList.add('border-2', 'border-gold');
  });
});

const decreaseBtn = document.getElementById('decrease-quantity');
  const increaseBtn = document.getElementById('increase-quantity');
  const quantityInput = document.getElementById('quantity');
  const maxStock = parseInt(quantityInput.getAttribute('max'));
  decreaseBtn.addEventListener('click', function() {
    let value = parseInt(quantityInput.value);
    if (value > 1) {
      quantityInput.value = value - 1;
      updateStockInfo();
      updatePrice(); 
    }
  });
  
  increaseBtn.addEventListener('click', function() {
    let value = parseInt(quantityInput.value);
    if (value < maxStock) {
      quantityInput.value = value + 1;
      updateStockInfo();
      updatePrice();
    }
  });
  
  quantityInput.addEventListener('change', function() {
    let value = parseInt(this.value);
    if (value < 1) this.value = 1;
    if (value > maxStock) this.value = maxStock;
    updateStockInfo();
    updatePrice();
  });
  
  function updateStockInfo() {
    const stockInfo = document.getElementById('stock-info');
    const remainingStock = maxStock - parseInt(quantityInput.value);
    stockInfo.textContent = `${remainingStock} units available`;
  }
  const variantOptions = document.querySelectorAll('input[name="variant"]');
  let selected = null;

  variantOptions.forEach(option => {
    option.addEventListener('change', function() {
        selected = this.value;
        document.querySelectorAll('.variant-option span').forEach(span => {
            span.classList.remove('border-gold', 'bg-gold-light');
            span.classList.add('border-gray-300');
          });
          
          this.parentElement.querySelector('span').classList.remove('border-gray-300');
          this.parentElement.querySelector('span').classList.add('border-gold', 'bg-gold-light');
          updatePrice();
          updateAvailableStock();    
    });  
  });


  

  function updatePrice(){
    const basePrice = document.getElementById('current-price');
    const price = parseFloat(basePrice.getAttribute('data-base-price'));
    const quantity = parseInt(quantityInput.value);

    let totalPrice = price  * quantity;

    if(selected){
        const selectedElement = document.querySelector(`input[name="variant"][value="${selected}"]`);
        if (selectedElement) {
            const priceAdjustment = parseFloat(selectedElement.dataset.priceAdjustment);
            totalPrice += priceAdjustment * quantity;
        }
    }

   


    const currentPriceElement = document.getElementById('current-price');
    currentPriceElement.textContent = `$${totalPrice.toFixed(2)}`;
    let base_perice = document.querySelector('[name="price"]');
    base_perice.value = totalPrice / quantity;

    const TotalInput = document.createElement('input');
    TotalInput.setAttribute("value",totalPrice.toFixed(2));
    TotalInput.setAttribute("name","totalPrice");
    TotalInput.setAttribute("type",'hidden');
    formCart.appendChild(TotalInput);
  }



    function updateAvailableStock() {
        let availableStock = maxStock;
               if (selected) {
          const selectedElement = document.querySelector(`input[name="variant"][value="${selected}"]`);
          if (selectedElement) {
            const variantStock = parseInt(selectedElement.dataset.stock);
            console.log(variantStock);
            if (variantStock < availableStock) {
              availableStock = variantStock;
            }
          }
        }
        
      
    quantityInput.setAttribute('max', availableStock);
    if (parseInt(quantityInput.value) > availableStock) {
      quantityInput.value = availableStock;
    }
    
    const stockInfo = document.getElementById('stock-info');
    stockInfo.textContent = `${availableStock} units available`;
    
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    if (availableStock <= 0) {
      addToCartBtn.disabled = true;
      addToCartBtn.classList.remove('bg-gold', 'hover:bg-gold-dark');
      addToCartBtn.classList.add('bg-gray-300');
      addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i> Out of Stock';
    } else {
      addToCartBtn.disabled = false;
      addToCartBtn.classList.remove('bg-gray-300');
      addToCartBtn.classList.add('bg-gold', 'hover:bg-gold-dark');
      addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i> Add to Cart';
    }
  }

  const formCart = document.getElementById('product-options-form');
  formCart.addEventListener('submit' ,  async (e) => {
    e.preventDefault();
    const formData = new FormData(formCart);
    const data = {};
    formData.forEach((value, key) => {
      data[key] = value;
    });
    
       const response = await fetch('/cart/add', {
                          method: 'POST',
                          headers: {
                              'Content-Type': 'application/json',
                          },
                          body: JSON.stringify(data),
                      });
       const result = await response.json();
                      if(result.success) {
                        updateCount();
                        displayMessage(result.success,'/cart');
                      }
  });

   
  const tabButtons = document.querySelectorAll('.product-tab-btn');
  const tabContents = document.querySelectorAll('.product-tab');
  tabButtons.forEach(button => {
    button.addEventListener('click', () => {
      const target = button.getAttribute('data-tab') + '-tab';

      tabButtons.forEach(btn => {
        btn.classList.remove('border-gold', 'text-gold');
        btn.classList.add('text-gray-500', 'border-transparent');
      });
      tabContents.forEach(content => content.classList.add('hidden'));

      button.classList.add('border-gold', 'text-gold');
      button.classList.remove('text-gray-500', 'border-transparent');
      document.getElementById(target).classList.remove('hidden');
    });
  });
 