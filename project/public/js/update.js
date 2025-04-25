import {displayMessage} from "./alert.js"
const form = document.getElementById('productForm');
const addSizeBtn = document.getElementById('addSizeBtn');
const addColorBtn = document.getElementById('addColorBtn');
const sizesContainer = document.getElementById('sizesContainer');
const coloresContainer = document.getElementById('coloresContainer');

addSizeBtn.addEventListener('click', function() {
    const sizeIndex = document.querySelectorAll('.size-row').length;
    const sizeHtml = `
        <div class="size-row mb-4 p-4 border border-gray-200 rounded-md bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Size Name</label>
                    <input type="text" name="size_name[${sizeIndex}]" class="size-name w-full border border-gray-300 rounded-md py-2 px-3">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price Adjustment ($)</label>
                    <input type="number" step="0.01" name="size_price_adjustment[${sizeIndex}]" class="size-price w-full border border-gray-300 rounded-md py-2 px-3">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                    <input type="number" name="stock_quantity_size[${sizeIndex}]" class="size-stock w-full border border-gray-300 rounded-md py-2 px-3">
                </div>
            </div>
            <button type="button" class="remove-size mt-3 text-red-500 hover:text-red-700 text-sm">
                <i class="fas fa-trash-alt mr-1"></i> Remove
            </button>
        </div>
    `;
    sizesContainer.insertAdjacentHTML('beforeend', sizeHtml);
});

addColorBtn.addEventListener('click', function() {
    const colorIndex = document.querySelectorAll('.color-row').length;
    const colorHtml = `
        <div class="color-row mb-4 p-4 border border-gray-200 rounded-md bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color Name</label>
                    <input type="text" name="color_name[${colorIndex}]" class="color-name w-full border border-gray-300 rounded-md py-2 px-3">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color Picker</label>
                    <input type="color" name="color_code[${colorIndex}]" class="color-code w-full h-10 cursor-pointer border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price Adjustment ($)</label>
                    <input type="number" step="0.01" name="color_price_adjustment[${colorIndex}]" class="color-price w-full border border-gray-300 rounded-md py-2 px-3">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                    <input type="number" name="stock_quantity_color[${colorIndex}]" class="color-stock w-full border border-gray-300 rounded-md py-2 px-3">
                </div>
            </div>
            <button type="button" class="remove-color mt-3 text-red-500 hover:text-red-700 text-sm">
                <i class="fas fa-trash-alt mr-1"></i> Remove
            </button>
        </div>
    `;
    coloresContainer.insertAdjacentHTML('beforeend', colorHtml);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-size') ) {
        e.target.closest('.size-row').remove();
    }
    if (e.target.classList.contains('remove-color')) {
        e.target.closest('.color-row').remove();
    }
});


form.addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors();
    const formData = new FormData(form);
 
    const isAvailable = document.getElementById('isAvailable').checked;
    formData.append('isAvailable', isAvailable);
  
     
      
        const response = await fetch(`/admin/products/update`, {
            method: 'POST',
            headers : {
                'X-Requested-With': 'XMLHttpRequest' 
            },
              body: formData,
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