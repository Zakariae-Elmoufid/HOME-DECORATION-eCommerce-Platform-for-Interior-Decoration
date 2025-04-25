import {displayMessage} from "./alert.js"

document.addEventListener('DOMContentLoaded', function() {
    const product = window.ProductData || {};
    const product_id = product.id;
    const form = document.getElementById('productForm');
    const addSizeBtn = document.getElementById('addSizeBtn');
    const addColorBtn = document.getElementById('addColorBtn');
    const sizesContainer = document.getElementById('sizesContainer');
    const coloresContainer = document.getElementById('coloresContainer');
    const imageInput = document.getElementById('imageInaput');
    const imagePreviews = document.getElementById('imagePreviews');
    
    document.querySelectorAll('input[name="removed_images[]"]').forEach(el => el.remove());

    imageInput?.addEventListener('change', function(e) {
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

        if (product.sizes && Array.isArray(product.sizes) && product.sizes.length > 0) {
            sizesContainer.innerHTML = '';
            
            let sizesHTML = '';
            product.sizes.forEach((size, index) => {
                sizesHTML += `
                    <div class="size-row mb-4 p-4 border border-gray-200 rounded-md bg-gray-50" data-size-id="${size.size_id || ''}">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Size Name</label>
                                <input type="text" name="size_name[${index}]" class="size-name w-full border border-gray-300 rounded-md py-2 px-3" value="${size.size_name || ''}">
                                <input type="hidden" name="size_id[${index}]" value="${size.size_id || ''}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price Adjustment ($)</label>
                                <input type="number" step="0.01" name="size_price_adjustment[${index}]" class="size-price w-full border border-gray-300 rounded-md py-2 px-3" value="${size.price_adjustment || 0}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                                <input type="number" name="stock_quantity_size[${index}]" class="size-stock w-full border border-gray-300 rounded-md py-2 px-3" value="${size.stock_quantity || 0}">
                            </div>
                        </div>
                        <button type="button" class="remove-size mt-3 text-red-500 hover:text-red-700 text-sm">
                            <i class="fas fa-trash-alt mr-1"></i> Remove
                        </button>
                    </div>
                `;
            });
            
            sizesContainer.innerHTML = sizesHTML;
        }
        
        if (product.colors && Array.isArray(product.colors) && product.colors.length > 0) {
            coloresContainer.innerHTML = '';
            
            let colorsHTML = '';
            product.colors.forEach((color, index) => {
                colorsHTML += `
                    <div class="color-row mb-4 p-4 border border-gray-200 rounded-md bg-gray-50" data-color-id="${color.color_id || ''}">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Color Name</label>
                                <input type="text" name="color_name[${index}]" class="color-name w-full border border-gray-300 rounded-md py-2 px-3" value="${color.color_name || ''}">
                                <input type="hidden" name="color_id[${index}]" value="${color.color_id || ''}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Color Picker</label>
                                <input type="color" name="color_code[${index}]" class="color-code w-full h-10 cursor-pointer border border-gray-300 rounded-md" value="${color.color_code || '#000000'}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price Adjustment ($)</label>
                                <input type="number" step="0.01" name="color_price_adjustment[${index}]" class="color-price w-full border border-gray-300 rounded-md py-2 px-3" value="${color.price_adjustment || 0}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                                <input type="number" name="stock_quantity_color[${index}]" class="color-stock w-full border border-gray-300 rounded-md py-2 px-3" value="${color.stock_quantity || 0}">
                            </div>
                        </div>
                        <button type="button" class="remove-color mt-3 text-red-500 hover:text-red-700 text-sm">
                            <i class="fas fa-trash-alt mr-1"></i> Remove
                        </button>
                    </div>
                `;
            });
            
            coloresContainer.innerHTML = colorsHTML;
        }
        
        // Gestion des images existantes
        if (product.images && Array.isArray(product.images) && product.images.length > 0) {
            imagePreviews.innerHTML = '';
            
            product.images.forEach(image => {
                if (image.image_path) {
                    const isPrimary = image.is_primary == 1 || image.is_primary === true;
                    const preview = `
                        <div class="relative" data-image-id="${image.id || ''}">
                            <img src="/public/uploads/${image.image_path}" class="w-32 h-32 object-cover rounded-md">
                            <button type="button" class="remove-image absolute top-1 right-1 bg-red-500 text-white rounded-full p-1">
                                <i class="fas fa-times"></i>
                            </button>
                            ${isPrimary ? '<div class="absolute bottom-1 left-1 bg-blue text-white text-xs px-2 py-1 rounded-md">Primary</div>' : ''}
                            <input type="hidden" name="existing_images[]" value="${image.id || ''}">
                        </div>
                    `;
                    imagePreviews.insertAdjacentHTML('beforeend', preview);
                }
            });
        }
    }

    addSizeBtn.addEventListener('click', function() {
        const sizeIndex = document.querySelectorAll('.size-row').length;
        const sizeHtml = `
            <div class="size-row mb-4 p-4 border border-gray-200 rounded-md bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Size Name</label>
                        <input type="text" name="size_name[${sizeIndex}]" class="size-name w-full border border-gray-300 rounded-md py-2 px-3">
                        <input type="hidden" name="size_id[${sizeIndex}]" value="">
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
                        <input type="hidden" name="color_id[${colorIndex}]" value="">
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
        if (e.target.classList.contains('remove-size') || e.target.closest('.remove-size')) {
            e.target.closest('.size-row').remove();
        }
        if (e.target.classList.contains('remove-color') || e.target.closest('.remove-color')) {
            e.target.closest('.color-row').remove();
        }
    });

    // Gestion de la suppression des images
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