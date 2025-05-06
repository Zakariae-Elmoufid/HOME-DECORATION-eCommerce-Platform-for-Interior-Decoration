document.addEventListener('DOMContentLoaded', function() {
    
    const showNotification = (parent, message, isSuccess = true) => {
        const notification = document.createElement('div');
        notification.className = isSuccess 
            ? 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 mt-4'
            : 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 mt-4';
        notification.textContent = message;
        
        parent.insertBefore(notification, parent.firstChild);
        
        setTimeout(() => notification.remove(), 3000);
        return notification;
    };
    
    const toggleVisibility = (hideElement, showElement) => {
        hideElement.classList.add('hidden');
        showElement.classList.remove('hidden');
    };


        const showValidationErrors = (form, errors) => {
            const existingErrors = form.querySelectorAll('.validation-error');
            existingErrors.forEach(error => error.remove());
            
            for (const field in errors) {
                const input = form.querySelector(`[name="${field}"]`);
                if (input) {

                    input.classList.add('border-red-500');
                    
                    const errorElement = document.createElement('p');
                    errorElement.className = 'validation-error text-red-500 text-xs mt-1';
                    errorElement.textContent = errors[field][0]; 
                    
                    const parent = input.parentElement;
                    parent.appendChild(errorElement);
                    
                    input.addEventListener('input', function() {
                        this.classList.remove('border-red-500');
                        const errorMsg = parent.querySelector('.validation-error');
                        if (errorMsg) errorMsg.remove();
                    }, { once: true });
                }
            }
        };

    // ===== Personal Information Section =====
    const personalElements = {
        editButton: document.getElementById('edit-personal-information'),
        infoDisplay: document.getElementById('personal-info-display'),
        editForm: document.getElementById('personal-information-edit'),
        form: document.getElementById('personal-information-form'),
        cancelButtons: document.querySelectorAll('#personal-information-edit .cancel-edit-btn')
    };
    
    // Set up personal info edit button
    if (personalElements.editButton) {
        personalElements.editButton.addEventListener('click', () => 
            toggleVisibility(personalElements.infoDisplay, personalElements.editForm));
    }
    
    // Set up personal info cancel buttons
    personalElements.cancelButtons.forEach(button => {
        button.addEventListener('click', () => 
            toggleVisibility(personalElements.editForm, personalElements.infoDisplay));
    });
    
    // Handle personal info form submission
    if (personalElements.form) {
        personalElements.form.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const data = {
                user_id: this.querySelector('input[name="user_id"]').value,
                username: document.getElementById('username').value,
                email: document.getElementById('email').value
            };
            
            try {
                const response = await fetch('/account/update', {
                    method: 'PATCH',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Update display with new values
                    const usernameDisplay = document.querySelector('#personal-info-display p:nth-child(2)');
                    const emailDisplay = document.querySelector('#personal-info-display div:nth-child(2) p:nth-child(2)');
                    
                    if (usernameDisplay) usernameDisplay.textContent = data.username;
                    if (emailDisplay) emailDisplay.textContent = data.email;
                    
                    showNotification(personalElements.editForm.parentNode, 'Profile updated successfully!');
                    toggleVisibility(personalElements.editForm, personalElements.infoDisplay);
                }else if(result.errorValidate){
                    showValidationErrors(this, result.errorValidate);
                }
                 else {
                    showNotification(
                        personalElements.editForm.parentNode, 
                        result.message || 'Error updating profile. Please try again.', 
                        false
                    );
                }
            } catch (error) {
                showNotification(
                    personalElements.editForm.parentNode, 
                    'Network error. Please try again later.', 
                    false
                );
            }
        });
    }
    
    // ===== Shipping Address Section =====
    const addressElements = {
        editButton: document.getElementById('edit-address-btn'),
        addButton: document.getElementById('add-address-btn'),
        addButtonAlt: document.getElementById('add-address-btn-alt'),
        display: document.getElementById('address-display'),
        editForm: document.getElementById('shipping-address-edit'),
        addForm: document.getElementById('shipping-address-add'),
        form: document.getElementById('shipping-address-form'),
        formAdd: document.getElementById('shipping-address-form-add'),
        cancelEditButtons: document.querySelectorAll('#shipping-address-edit .cancel-edit-btn'),
        cancelAddButtons: document.querySelectorAll('#shipping-address-add .cancel-add-btn')
    };
    
    // Set up address edit button
    if (addressElements.editButton) {
        addressElements.editButton.addEventListener('click', function() {
            toggleVisibility(addressElements.display, addressElements.editForm);
            
            const formTitle = addressElements.editForm.querySelector('h3');
            if (formTitle) formTitle.textContent = 'Edit Your Shipping Address';
        });
    }
    
    // Set up both add address buttons (primary and alternative in "No address" section)
    const setupAddButtons = (button) => {
        if (button) {
            button.addEventListener('click', () => 
                toggleVisibility(addressElements.display, addressElements.addForm));
        }
    };
    
    setupAddButtons(addressElements.addButton);
    setupAddButtons(addressElements.addButtonAlt);
    
    // Set up cancel buttons for edit form
    addressElements.cancelEditButtons.forEach(button => {
        button.addEventListener('click', () => 
            toggleVisibility(addressElements.editForm, addressElements.display));
    });
    
    // Set up cancel buttons for add form
    addressElements.cancelAddButtons.forEach(button => {
        button.addEventListener('click', () => 
            toggleVisibility(addressElements.addForm, addressElements.display));
    });
    
    // Handle address edit form submission
    if (addressElements.form) {
        addressElements.form.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const formData = {
                id: this.querySelector('input[name="id"]').value,
               
                address: document.getElementById('edit-address').value,
                city: document.getElementById('edit-city').value,
                postal_code: document.getElementById('edit-postalCode').value,
                country: document.getElementById('edit-country').value,
                phone: document.getElementById('edit-phone').value,
            };
            
            try {
                const response = await fetch('/account/update-address', {
                    method: 'PATCH',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                if (result.success) {
                    // Update display with new values
                    addressElements.display.innerHTML = `
                        <div class="bg-gray-50 rounded p-4">
                            <p class="font-medium">${formData.first_name} ${formData.last_name}</p>
                            <p>${formData.address}</p>
                            <p>${formData.city}, ${formData.postal_code}</p>
                            <p>${formData.country}</p>
                            <p>${formData.phone}</p>
                            <p>${formData.email}</p>
                        </div>
                    `;
                    
                    showNotification(addressElements.editForm.parentNode, 'Shipping address updated successfully!');
                    toggleVisibility(addressElements.editForm, addressElements.display);
                    
                    if (addressElements.editButton?.classList.contains('hidden')) {
                        addressElements.editButton.classList.remove('hidden');
                    }
                }else if(result.errorValidate){
                    showValidationErrors(this, result.errorValidate);
                }
                else {
                    showNotification(
                        addressElements.editForm.parentNode, 
                        result.message || 'Error updating shipping address. Please try again.', 
                        false
                    );
                }
            } catch (error) {
                showNotification(
                    addressElements.editForm.parentNode, 
                    'Network error. Please try again later.', 
                    false
                );
            }
        });
    }

    // Handle address add form submission
    if (addressElements.formAdd) {
        addressElements.formAdd.addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = {
                user_id: this.querySelector('input[name="user_id"]').value,
                address: document.getElementById('add-address').value,
                city: document.getElementById('add-city').value,
                postal_code: document.getElementById('add-postalCode').value,
                country: document.getElementById('add-country').value,
                phone: document.getElementById('add-phone').value,
            };
            
            try {
                const response = await fetch('/account/add-address', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                if (result.success) {
                    showNotification(addressElements.addForm.parentNode, 'Shipping address added successfully!');
                    
                    addressElements.display.innerHTML = `
                        <div class="bg-gray-50 rounded p-4">
                            <p>${formData.address}</p>
                            <p>${formData.city}, ${formData.postal_code}</p>
                            <p>${formData.country}</p>
                            <p>${formData.phone}</p>
                        </div>
                    `;
                    
                    toggleVisibility(addressElements.addForm, addressElements.display);
                    
                    // Show edit button instead of add button after adding an address
                    if (addressElements.addButton) {
                        addressElements.addButton.classList.add('hidden');
                    }
                    
                    if (addressElements.addButtonAlt) {
                        addressElements.addButtonAlt.classList.add('hidden');
                    }
                    
                    // Create and show edit button if it doesn't exist
                    if (!addressElements.editButton) {
                        const addressHeader = document.querySelector('.border-b .flex.justify-between.items-center');
                        if (addressHeader) {
                            const editBtn = document.createElement('button');
                            editBtn.id = 'edit-address-btn';
                            editBtn.className = 'text-gold hover:text-gold-dark transition';
                            editBtn.innerHTML = '<i class="fas fa-edit mr-1"></i> Edit Address';
                            
                            addressHeader.appendChild(editBtn);
                            
                            // Add event listener to the new button
                            editBtn.addEventListener('click', function() {
                                toggleVisibility(addressElements.display, addressElements.editForm);
                            });
                        }
                    } else if (addressElements.editButton.classList.contains('hidden')) {
                        addressElements.editButton.classList.remove('hidden');
                    }
                }else if(result.errorValidate){
                    showValidationErrors(this, result.errorValidate);
                }
                else {
                    showNotification(
                        addressElements.addForm.parentNode, 
                        result.message || 'Error adding shipping address. Please try again.', 
                        false
                    );
                }
            } catch (error) {
                showNotification(
                    addressElements.addForm.parentNode, 
                    'Network error. Please try again later.', 
                    false
                );
            }
        });
    }
});