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

  
