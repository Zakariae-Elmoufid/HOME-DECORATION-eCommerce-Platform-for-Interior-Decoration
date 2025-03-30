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
    }
  });
  
  increaseBtn.addEventListener('click', function() {
    let value = parseInt(quantityInput.value);
    if (value < maxStock) {
      quantityInput.value = value + 1;
      updateStockInfo();
    }
  });
  
  quantityInput.addEventListener('change', function() {
    let value = parseInt(this.value);
    if (value < 1) this.value = 1;
    if (value > maxStock) this.value = maxStock;
    updateStockInfo();
  });
  
  function updateStockInfo() {
    const stockInfo = document.getElementById('stock-info');
    const remainingStock = maxStock - parseInt(quantityInput.value);
    stockInfo.textContent = `${remainingStock} units available`;
  }
  //Size options
  const sizeOptions = document.querySelectorAll('input[name="size"]');
  let selectedSize = null;

  sizeOptions.forEach(option => {
    option.addEventListener('change', function() {
        selectedSize = this.value;
        document.querySelectorAll('.size-option span').forEach(span => {
            span.classList.remove('border-gold', 'bg-gold-light');
            span.classList.add('border-gray-300');
          });
          
          this.parentElement.querySelector('span').classList.remove('border-gray-300');
          this.parentElement.querySelector('span').classList.add('border-gold', 'bg-gold-light');
          updatePrice();
          updateAvailableStock();    
    });  
  });


   // Color options
   const colorOptions = document.querySelectorAll('input[name="color"]');
   let selectedColor = null;
   
     
  colorOptions.forEach(option => {
    option.addEventListener('change', function() {
      // Update selected color
      selectedColor = this.value;
      
      // Update visual selection
      document.querySelectorAll('.color-option span').forEach(span => {
        span.classList.remove('border-gold');
        span.classList.add('border-gray-300');
        span.querySelector('.color-check')?.classList.add('hidden');
      });
      
      this.parentElement.querySelector('span').classList.remove('border-gray-300');
      this.parentElement.querySelector('span').classList.add('border-gold');
      this.parentElement.querySelector('.color-check')?.classList.remove('hidden');
      
      updatePrice();
      updateAvailableStock();
    });
  });

  function updatePrice(){
    const basePrice = document.getElementById('current-price');
    const price = parseFloat(basePrice.getAttribute('data-base-price'));
    let totalPrice = price;

    if(selectedSize){
        const selectedSizeElement = document.querySelector(`input[name="size"][value="${selectedSize}"]`);
        if (selectedSizeElement) {
            const priceAdjustment = parseFloat(selectedSizeElement.dataset.priceAdjustment);
            totalPrice += priceAdjustment;
        }
    }

    const currentPriceElement = document.getElementById('current-price');
    currentPriceElement.textContent = `$${totalPrice.toFixed(2)}`;
  }



    // Update stock info based on selected options
    function updateAvailableStock() {
        let availableStock = maxStock;
        
        // Check stock for selected size
        if (selectedSize) {
          const selectedSizeElement = document.querySelector(`input[name="size"][value="${selectedSize}"]`);
          if (selectedSizeElement) {
            const sizeStock = parseInt(selectedSizeElement.dataset.stock);
            if (sizeStock < availableStock) {
              availableStock = sizeStock;
            }
          }
        }
        
        // Check stock for selected color
        if (selectedColor) {
          const selectedColorElement = document.querySelector(`input[name="color"][value="${selectedColor}"]`);
          if (selectedColorElement) {
            const colorStock = parseInt(selectedColorElement.dataset.stock);
            if (colorStock < availableStock) {
              availableStock = colorStock;
            }
          }
        }
            // Update max quantity and stock info
    quantityInput.setAttribute('max', availableStock);
    if (parseInt(quantityInput.value) > availableStock) {
      quantityInput.value = availableStock;
    }
    
    const stockInfo = document.getElementById('stock-info');
    stockInfo.textContent = `${availableStock} units available`;
    
    // Update add to cart button state
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
