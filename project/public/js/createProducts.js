document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('productForm');
    const addSizeBtn = document.getElementById('addSizeBtn');
    const addColorBtn = document.getElementById('addColorBtn');
    const sizesContainer = document.getElementById('sizesContainer');
    const coloresContainer = document.getElementById('coloresContainer');
    const imageInput = document.getElementById('imageInput');
    const imagePreviews = document.getElementById('imagePreviews');

    // Gérer l'ajout de tailles
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

    // Gérer l'ajout de couleurs
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

    // Gérer la suppression
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-size')) {
            e.target.closest('.size-row').remove();
        }
        if (e.target.classList.contains('remove-color')) {
            e.target.closest('.color-row').remove();
        }
    });

    // Gérer la soumission du formulaire
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        // Créer un nouvel objet FormData
        const formData = new FormData(form);
        // Collecter les données des tailles
        const sizes = [];
        document.querySelectorAll('.size-row').forEach(row => {
            const sizeName = row.querySelector('.size-name')?.value;
            const sizePrice = row.querySelector('.size-price')?.value;
            const sizeStock = row.querySelector('.size-stock')?.value;
            
            if (sizeName) {
                sizes.push({
                    name: sizeName,
                    price_adjustment: sizePrice || 0,
                    stock_quantity: sizeStock || 0
                });
            }
        });
        const isAvailable = document.getElementById('isAvailable').checked;
        formData.append('isAvailable', isAvailable);
        const colors = [];
        document.querySelectorAll('.color-row').forEach(row => {
            const colorName = row.querySelector('.color-name')?.value;
            const colorCode = row.querySelector('.color-code')?.value;
            const colorPrice = row.querySelector('.color-price')?.value;
            const colorStock = row.querySelector('.color-stock')?.value;
            
            if (colorName) {
                colors.push({
                    name: colorName,
                    code: colorCode,
                    price_adjustment: colorPrice || 0,
                    stock_quantity: colorStock || 0
                });
            }
        });

        formData.append('sizes', JSON.stringify(sizes));
        formData.append('colors', JSON.stringify(colors));

      
        // for (let pair of formData.entries()) {
        //     console.log(pair[0], pair[1]);
        // }

        
            const response = await fetch('/products/store', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
         
                if (result.errors) {
                    displayErrors(result.errors );
                }
            
        
    });

    // Prévisualisation des images
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

    function displayErrors(errors) {
        const inputs = document.querySelectorAll("input ,textarea");
    
        inputs.forEach((input , index) => {
            const inputName = input.getAttribute('name'); 
    
            if (inputName && errors[inputName]) { 
                const errorMessage =  errors[inputName][index] ||  errors[inputName][0]; // Récupère le message d'erreur
                
                // Vérifie si l'élément suivant existe et s'il est un message d'erreur
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