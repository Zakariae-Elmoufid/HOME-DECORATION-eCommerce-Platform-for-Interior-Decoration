import { displayMessage } from "./alert.js";

document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadImageForm');
    const imageInput = document.getElementById('imageInput');
    const uploadPreview = document.getElementById('uploadPreview');
    const imageGallery = document.getElementById('imageGallery');
    
    const productId = imageGallery.dataset.productId;
    console.log(productId);
    imageInput.addEventListener('change', function(e) {
        uploadPreview.innerHTML = '';
        Array.from(this.files).forEach(file => {
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
                uploadPreview.insertAdjacentHTML('beforeend', preview);
            };
            reader.readAsDataURL(file);
        });
    });
    uploadPreview.addEventListener('click', function(e) {
        if (e.target.closest('.remove-image')) {
            e.target.closest('.relative').remove();
        }
    });



    uploadForm.addEventListener('submit', async function(e) {
        e.preventDefault();

      
        if (!imageInput.files || imageInput.files.length === 0) {
            displayMessage("Please select at least one image to upload", null, "error");
            return;
        }
         
        const formData = new FormData();
        formData.append('product_id', productId);
        
        for (let i = 0; i < imageInput.files.length; i++) {
            formData.append('images[]', imageInput.files[i]);
        }
        
        
        
        try {
            const response = await fetch('/admin/products/upload-images', {
                method: 'POST',
                headers : {
                    'X-Requested-With': 'XMLHttpRequest' 
                },
                body: formData,
            });
            
            const result = await response.json();
            
            if (result.success) {
                displayMessage(result.success, null);
                setTimeout(() => window.location.reload(), 1500);
            } else if (result.errors) {
                displayMessage(result.errors.join('<br>'), null, "error");
            }
        } catch (error) {
            console.error("Error uploading images:", error);
            displayMessage("An error occurred while uploading images", null, "error");
        }
    });
    
    imageGallery.addEventListener('click', async function(e) {
        const setPrimaryBtn = e.target.closest('.set-primary-btn');
        if (setPrimaryBtn) {
            const imageContainer = setPrimaryBtn.closest('[data-image-id]');
            const imageId = imageContainer.dataset.imageId;
            
            try {
                const response = await fetch(`/admin/products/set-primary-image?id=${imageId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    displayMessage(result.success, null);
                    setTimeout(() => window.location.reload(), 1500);
                } else if (result.error) {
                    displayMessage(result.error, null, "error");
                }
            } catch (error) {
                displayMessage("An error occurred while setting the primary image", null, "error");
            }
        }
    });
    
    imageGallery.addEventListener('click', async function(e) {
        const deleteBtn = e.target.closest('.delete-image-btn');
       
            
            const imageContainer = deleteBtn.closest('[data-image-id]');
            const imageId = imageContainer.dataset.imageId;
            
            try {
                const response = await fetch(`/admin/products/delete-image?id=${imageId}`, {
                    method: 'GET',
    
                    
                });
                
                const result = await response.json();
                
                if (result.success) {
                    imageContainer.remove();
                    displayMessage(result.success, null);
                } else if (result.error) {
                    displayMessage(result.error, null, "error");
                }
            } catch (error) {
                console.error("Error deleting image:", error);
                displayMessage("An error occurred while deleting the image", null, "error");
            }
        })
    });
