import {displayMessage} from "./alert.js"

document.addEventListener('DOMContentLoaded', function() {


    const form = document.getElementById('productForm');
    const addVariantBtn = document.getElementById('addVariantBtn');
    const variantsContainer = document.getElementById('variantsContainer');
    const stockglobal = document.getElementById('stock');
    
    const imageInput = document.getElementById('imageInput');
    const imagePreviews = document.getElementById('imagePreviews');
  
    addVariantBtn.addEventListener('click', function() {
        const variantIndex = document.querySelectorAll('.variant-row').length;
        const variantHtml = `
            <div class="variant-row mb-4 p-4 border border-gray-200 rounded-md bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                        <input type="text" name="size_name[${variantIndex}]" class="size-name w-full border border-gray-300 rounded-md py-2 px-3">
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input type="text" name="color_name[${variantIndex}]" class="color-name w-full border border-gray-300 rounded-md py-2 px-3">
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color Picker</label>
                        <input type="color"  name="color_code[${variantIndex}]" class="color-code w-full h-10 cursor-pointer border border-gray-300 rounded-md">
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price Adjustment ($)</label>
                        <input type="number" step="0.01" name="price_adjustment[${variantIndex}]" class="price-adjustment w-full border border-gray-300 rounded-md py-2 px-3">
                      </div>
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                        <input type="number" name="stock_quantity[${variantIndex}]" class="stock-quantity w-full border border-gray-300 rounded-md py-2 px-3">
                      </div>
                    </div>
                    <button type="button" class="remove-variant mt-3 text-red-500 hover:text-red-700 text-sm">
                      <i class="fas fa-trash-alt mr-1"></i> Remove
                    </button>
                  </div>
        `;
        variantsContainer.insertAdjacentHTML('beforeend', variantHtml);
 
    });



    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variant')) {
            e.target.closest('.variant-row').remove();
        }
       
    });

    
  
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();
        const stockValues = Array.from(document.querySelectorAll('.stock-quantity')).map(input => input.value);
        let totalStock = 0; 
        stockValues.forEach(input => {
            const stock = parseInt(input) || 0;
            totalStock += stock;
        });
        console.log(totalStock);
    
    if (totalStock !== parseInt(stockglobal.value)) {
        const message = totalStock < parseInt(stockglobal.value)
            ? "Please update the stock: the global stock is greater than the total variant stock."
            : "Please update the stock: the global stock is less than the total variant stock.";
            if (stockglobal.nextElementSibling && stockglobal.nextElementSibling.classList.contains('error-message')) {
                stockglobal.nextElementSibling.textContent = message;
            } else {
                const errorSpan = document.createElement('small');
                errorSpan.classList.add('error-message', 'text-red-500', 'text-sm');
                errorSpan.textContent = message;
                stockglobal.parentNode.appendChild(errorSpan);
            }        return;
    }
        const formData = new FormData(form);
      
        
        const isAvailable = document.getElementById('isAvailable').checked;
        formData.append('isAvailable', isAvailable);
          
            const response = await fetch('/admin/products/store', {
                method: 'POST',
                headers : {
                    'X-Requested-With': 'XMLHttpRequest' 
                },
                body: formData
            });

            const result = await response.json();
                if(result.success) {
                    displayMessage(result.success,"/admin/products");
                }
                if (result.errors) {
                    displayErrors(result.errors );
                }
            
        
    });

    imageInput?.addEventListener('change', function(e) {
        imagePreviews.innerHTML = '';
        [...e.target.files].forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = `
                    <div class="relative">
                        <img src="${e.target.result}" class="w-32 h-32 object-cover rounded-md">
                        <button type="button" class="remove-image absolute top-1 right-1 bg-red-500 text-white rounded-full p-1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                imagePreviews.insertAdjacentHTML('beforeend', preview);
            };
            reader.readAsDataURL(file);
        });
    });
    imagePreviews.addEventListener('click', function(e) {
        if (e.target.closest('.remove-image')) {
            e.target.closest('.relative').remove();
        }
    });

    function displayErrors(errors) {
        const inputs = document.querySelectorAll("input ,textarea");
    
        console.log(errors);
        inputs.forEach((input , index) => {
            const inputName = input.getAttribute('name'); 
          
            if (inputName && errors[inputName]) { 
                const errorMessage =  errors[inputName][0];
                
                if (input.nextElementSibling && input.nextElementSibling.classList.contains('error-message')) {
                    input.nextElementSibling.textContent = errorMessage;
                }else{
                    const errorSpan = document.createElement('small');
                    errorSpan.classList.add('error-message', 'text-red-500', 'text-sm');
                    errorSpan.textContent = errorMessage;
                    input.parentNode.appendChild(errorSpan);
                }
            }
        });
    }
    
});

function clearErrors() {
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(error => {
        error.remove();
    });
}