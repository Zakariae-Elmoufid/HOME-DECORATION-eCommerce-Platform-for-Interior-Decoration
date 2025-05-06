import {displayMessage} from "./alert.js"
const form = document.getElementById('productForm');
const addVariantBtn = document.getElementById('addVariantBtn');
const variantsContainer = document.getElementById('variantsContainer');

const stock = document.getElementById('stock');



addVariantBtn.addEventListener('click', function() {
    const index = document.querySelectorAll('.variant-row').length;
     const variantHTML = `
                <div  data-size-id="{{variant.variant_id}}" class="variant-row mb-4 p-4 border border-gray-200 rounded-md bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                     <input type="hidden" name="size_id[${index}]" value="">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Size Name</label>
                    <input type="text" name="size_name[${index}]"   class="size-name w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-blue focus:border-blue">
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="text" name="color_name[${index}]"  class="color-name w-full border border-gray-300 rounded-md py-2 px-3">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color Picker</label>
                    <input type="color"  name="color_code[${index}]"  class="color-code w-full h-10 cursor-pointer border border-gray-300 rounded-md">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price Adjustment ($)</label>
                    <input type="number" step="0.01" name="price_adjustment[${index}]"  class="price-adjustment w-full border border-gray-300 rounded-md py-2 px-3">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                    <input type="number" name="stock_quantity[${index}]"  class="stock-quantity w-full border border-gray-300 rounded-md py-2 px-3">
                  </div>
                </div>
                <button type="button" class="remove-variant mt-3 text-red-500 hover:text-red-700 text-sm">
                  <i class="fas fa-trash-alt mr-1"></i> Remove
                </button>
              </div>
            `;
    variantsContainer.insertAdjacentHTML('beforeend', variantHTML);
});

const stockValues = Array.from(document.querySelectorAll('.stock-quantity')).map(input => input.value);
console.log(stockValues);
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-variant')) {
        if(e.target.dataset.variantId){
           fetch(`/admin/product/variant?id=${e.target.dataset.variantId}`)
           .then(response => {
            if(!response.ok){
              throw new Error('Error Network');
            }
            return  response.json(); 
          })
          .then( data => {
            console.log(data);           
          })
          .catch(error => {
            console.error('Erreur de fetch:', error);
          }); 

        }
        e.target.closest('.variant-row').remove();
    }
   
});





form.addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors();

    const stockValues = Array.from(document.querySelectorAll('.stock-quantity')).map(input => input.value);
    console.log(stockValues);
    let totalStock = 0; 
    stockValues.forEach(input => {
        const stock = parseInt(input) || 0;
        totalStock += stock;
    });

    if (totalStock !== parseInt(stock.value)) {
      const message = totalStock < parseInt(stock.value)
          ? "Please update the stock: the global stock is greater than the total variant stock."
          : "Please update the stock: the global stock is less than the total variant stock.";
          if (stock.nextElementSibling && stock.nextElementSibling.classList.contains('error-message')) {
            stock.nextElementSibling.textContent = message;
          } else {
              const errorSpan = document.createElement('small');
              errorSpan.classList.add('error-message', 'text-red-500', 'text-sm');
              errorSpan.textContent = message;
              stock.parentNode.appendChild(errorSpan);
          }        
          return;
  }

    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
      });
    const isAvailable = document.getElementById('isAvailable').checked;
    data.isAvailable =  isAvailable;
  
      
        const response = await fetch(`/admin/products/update`, {
            method: 'POST',
            headers : {
                'X-Requested-With': 'XMLHttpRequest' ,
                'Content-Type': 'application/json',  

            },
              body:  JSON.stringify(data),
        });
      
        const result = await response.json();
       if (result.success) {
            displayMessage(result.success, "/admin/products");
        }
        if (result.errors) {
            displayErrors(result.errors);
        }
   
        

    function displayErrors(errors) {
        const inputs = document.querySelectorAll("input ,textarea");

        inputs.forEach((input , index) => {
            const inputName = input.getAttribute('name'); 
            if (inputName && errors[inputName]) { 
                const errorMessage = Array.isArray(errors[inputName]) ? errors[inputName][0] : errors[inputName];
                
                if (input.nextElementSibling && input.nextElementSibling.classList.contains('error-message')) {
                    input.nextElementSibling.textContent = errorMessage;
                } else {
                    const errorSpan = document.createElement('small');
                    errorSpan.classList.add('error-message', 'text-red-500', 'text-sm');
                    errorSpan.textContent = errorMessage;
                    input.parentNode.appendChild(errorSpan);
                }
                
            }
        });
    }

    function clearErrors() {
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(error => {
            error.remove();
        });
    }
});