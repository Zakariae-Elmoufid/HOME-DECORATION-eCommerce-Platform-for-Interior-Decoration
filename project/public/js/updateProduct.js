import {displayMessage} from "./alert.js"

document.addEventListener('DOMContentLoaded', function() {
    const product = window.ProductData || {};
    const product_id = product.id;
    const form = document.getElementById('productForm');
    const addVariantBtn = document.getElementById('addVariantBtn');
    const variantsContainer = document.getElementById('variantsContainer');

    


    
    if (product && product.id) {
        document.getElementById('title').value = product.title || '';
        document.getElementById('description').value = product.description || '';
        document.getElementById('base_price').value = product.base_price || '';
        document.getElementById('stock').value = product.stock || '';
        document.getElementById('isAvailable').checked = product.isAvailable == 1;
        
        if (product.category_id) {
            const categorySelect = document.getElementById('category');
            const optionToSelect = categorySelect.querySelector(`option[value="${product.category_id}"]`);
            if (optionToSelect) {
                optionToSelect.selected = true;
            }
        }

        if (product.variants && Array.isArray(product.variants) && product.variants.length > 0) {
            variantsContainer.innerHTML = '';
            
            let variantHTML = '';
            product.variants.forEach((variant, index) => {
                variantHTML += `
                                     <div  data-size-id="{{variant.variant_id}}" class="variant-row mb-4 p-4 border border-gray-200 rounded-md bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                         <input type="hidden" name="size_id[${index}]" value="${variant.variant_id  || '' }">
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
            });
            
            variantsContainer.innerHTML = variantHTML;
        }
        
      
        
     
    }

    addVariantBtn.addEventListener('click', function() {
        const index = document.querySelectorAll('.variant-row').length;
        variantHTML += `
                    <div  data-size-id="{{variant.variant_id}}" class="variant-row mb-4 p-4 border border-gray-200 rounded-md bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                         <input type="hidden" name="size_id[${index}]" value="${variant.variant_id  || '' }">
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



    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-size') || e.target.closest('.remove-size')) {
            e.target.closest('.size-row').remove();
        }
        if (e.target.classList.contains('remove-color') || e.target.closest('.remove-color')) {
            e.target.closest('.color-row').remove();
        }
    });

    imagePreviews.addEventListener('click', function(e) {
        if (e.target.closest('.remove-image')) {
            const imageDiv = e.target.closest('div[data-image-id]');
            if (imageDiv) {
                const imageId = imageDiv.dataset.imageId;
                if (imageId) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'removed_images[]';
                    input.value = imageId;
                    form.appendChild(input);
                }
                imageDiv.remove();
            } else {
                // image ajoutée localement, juste remove()
                e.target.closest('.relative')?.remove();
            }
        }
    });
    

    // Soumission du formulaire
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();
        
        const formData = new FormData(form);
        formData.append("id", product_id);
        
        const isAvailable = document.getElementById('isAvailable').checked;
        formData.append('isAvailable', isAvailable ? 1 : 0);

        // Collecte des tailles
        const sizes = [];
        document.querySelectorAll('.size-row').forEach((row, index) => {
            const sizeName = row.querySelector('.size-name')?.value;
            const sizePrice = row.querySelector('.size-price')?.value;
            const sizeStock = row.querySelector('.size-stock')?.value;
            const sizeId = row.querySelector('input[name^="size_id"]')?.value || null;

            if (sizeName) {
                sizes.push({
                    size_id: sizeId,
                    size_name: sizeName,
                    price_adjustment: sizePrice || 0,
                    stock_quantity: sizeStock || 0
                });
            }
        });
        
        // Collecte des couleurs
        const colors = [];
        document.querySelectorAll('.color-row').forEach((row, index) => {
            const colorName = row.querySelector('.color-name')?.value;
            const colorCode = row.querySelector('.color-code')?.value;
            const colorPrice = row.querySelector('.color-price')?.value;
            const colorStock = row.querySelector('.color-stock')?.value;
            const colorId = row.querySelector('input[name^="color_id"]')?.value || null;

            if (colorName) {
                colors.push({
                    color_id: colorId,
                    color_name: colorName,
                    color_code: colorCode,
                    price_adjustment: colorPrice || 0,
                    stock_quantity: colorStock || 0
                });
            }
        });
        
        // Gestion des images existantes
        const existingImages = [];
        document.querySelectorAll('input[name="existing_images[]"]').forEach(input => {
            existingImages.push(input.value);
        });
        formData.append('existing_images', JSON.stringify(existingImages));
        
        // Gestion des images supprimées
        const removedImages = [];
        document.querySelectorAll('input[name="removed_images[]"]').forEach(input => {
            removedImages.push(input.value);
        });
        formData.append('removed_images', JSON.stringify(removedImages));
        
        // Gestion des nouvelles images
        const imageInput = document.getElementById('imageInput');
        if (imageInput.files.length > 0) {
            for (let i = 0; i < imageInput.files.length; i++) {
                formData.append('new_images[]', imageInput.files[i]);
            }
        }
        
        try {
            const response = await fetch(`/admin/products/update`, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            if (result.success) {
                displayMessage(result.success, "/admin/products");
            }
            if (result.errors) {
                displayErrors(result.errors);
            }
        } catch (error) {
            console.error("Error submitting form:", error);
            displayMessage("Une erreur s'est produite lors de la mise à jour du produit.", null, "error");
        }
    });

    // Fonction pour afficher les erreurs de validation
    function displayErrors(errors) {
        const inputs = document.querySelectorAll("input, textarea, select");
    
        inputs.forEach(input => {
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
                
                // Ajout d'une classe pour indiquer visuellement l'erreur
                input.classList.add('border-red-500');
            }
        });
    }
    
    // Fonction pour effacer les messages d'erreur
    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
        });
        
        document.querySelectorAll('input, textarea, select').forEach(el => {
            el.classList.remove('border-red-500');
        });
    }
});