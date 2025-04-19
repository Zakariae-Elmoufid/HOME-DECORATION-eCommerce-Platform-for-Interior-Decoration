document.addEventListener('DOMContentLoaded', function() {
    // ==== GESTION DES INFORMATIONS PERSONNELLES ====
    const editPersonalButton = document.getElementById('edit-personal-information');
    const infoDisplay = document.getElementById('personal-info-display');
    const editPersonalForm = document.getElementById('personal-information-edit');
    const personalForm = document.getElementById('personal-information-form');
    const cancelPersonalButtons = document.querySelectorAll('#personal-information-edit .cancel-edit-btn');
    
    if (editPersonalButton) {
        editPersonalButton.addEventListener('click', function() {
            infoDisplay.classList.add('hidden');
            editPersonalForm.classList.remove('hidden');
        });
    }
    
    cancelPersonalButtons.forEach(button => {
        button.addEventListener('click', function() {
            editPersonalForm.classList.add('hidden');
            infoDisplay.classList.remove('hidden');
        });
    });
    
    if (personalForm) {
        personalForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const userId = document.querySelector('#personal-information-form input[name="user_id"]').value;
            
            const data = {
                user_id: userId,
                username: username,
                email: email
            };
            
            try {
                const response = await fetch('/account/update', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const usernameDisplay = document.querySelector('#personal-info-display p:nth-child(2)');
                    const emailDisplay = document.querySelector('#personal-info-display div:nth-child(2) p:nth-child(2)');
                    
                    if (usernameDisplay) usernameDisplay.textContent = username;
                    if (emailDisplay) emailDisplay.textContent = email;
                    
                    // Afficher notification de succès
                    const notification = document.createElement('div');
                    notification.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 mt-4';
                    notification.textContent = 'Profile updated successfully!';
                    
                    const parent = editPersonalForm.parentNode;
                    parent.insertBefore(notification, editPersonalForm);
                    
                    // Supprimer notification après 3 secondes
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                    
                    // Cacher le formulaire, afficher infos
                    editPersonalForm.classList.add('hidden');
                    infoDisplay.classList.remove('hidden');
                } else {
                    // Afficher notification d'erreur
                    const notification = document.createElement('div');
                    notification.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 mt-4';
                    notification.textContent = result.message || 'Error updating profile. Please try again.';
                    
                    const parent = editPersonalForm.parentNode;
                    parent.insertBefore(notification, editPersonalForm);
                    
                    // Supprimer notification après 3 secondes
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                }
            } catch (error) {
                console.error('Error:', error);
                // Afficher notification d'erreur réseau
                const notification = document.createElement('div');
                notification.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 mt-4';
                notification.textContent = 'Network error. Please try again later.';
                
                const parent = editPersonalForm.parentNode;
                parent.insertBefore(notification, editPersonalForm);
                
                // Supprimer notification après 3 secondes
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        });
    }
    
    const editAddressButton = document.getElementById('edit-address-btn');
    const addAddressButton = document.getElementById('add-address-btn');
    const addressDisplay = document.getElementById('address-display');
    const addressEditForm = document.getElementById('shipping-address-edit');
    const shippingAddressForm = document.getElementById('shipping-address-form');
    const cancelAddressButtons = document.querySelectorAll('#shipping-address-edit .cancel-edit-btn');
    
    if (editAddressButton) {
        editAddressButton.addEventListener('click', function() {
            addressDisplay.classList.add('hidden');
            addressEditForm.classList.remove('hidden');
            
            // Modifier le titre selon le cas (édition)
            const formTitle = addressEditForm.querySelector('h3');
            if (formTitle) {
                formTitle.textContent = 'Edit Your Shipping Address';
            }
        });
    }
    
    if (addAddressButton) {
        addAddressButton.addEventListener('click', function() {
            addressDisplay.classList.add('hidden');
            addressEditForm.classList.remove('hidden');
            
            const formTitle = addressEditForm.querySelector('h3');
            if (formTitle) {
                formTitle.textContent = 'Add Shipping Address';
            }
            
            shippingAddressForm.reset();
        });
    }
    
    cancelAddressButtons.forEach(button => {
        button.addEventListener('click', function() {
            addressEditForm.classList.add('hidden');
            addressDisplay.classList.remove('hidden');
        });
    });
    
    if (shippingAddressForm) {
        console.log(shippingAddressForm);
        shippingAddressForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const formData = {
                id: document.querySelector('#shipping-address-form input[name="id"]').value,
                first_name: document.getElementById('firstName').value,
                last_name: document.getElementById('lastName').value,
                address: document.getElementById('address').value,
                city: document.getElementById('city').value,
                postal_code: document.getElementById('postalCode').value,
                country: document.getElementById('country').value,
                phone: document.getElementById('phone').value,
                email: document.getElementById('email').value
            };
            
            try {
                const response = await fetch('/account/update-address', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData),
                });
                
                const result = await response.json();
                console.log(result);
                if (result.success) {
                    addressDisplay.innerHTML = `
                        <div class="bg-gray-50 rounded p-4">
                            <p class="font-medium">${formData.first_name} ${formData.last_name}</p>
                            <p>${formData.address}</p>
                            <p>${formData.city}, ${formData.postal_code}</p>
                            <p>${formData.country}</p>
                            <p>${formData.phone}</p>
                            <p>${formData.email}</p>
                        </div>
                    `;
                    
                    const notification = document.createElement('div');
                    notification.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 mt-4';
                    notification.textContent = 'Shipping address updated successfully!';
                    
                    const parent = addressEditForm.parentNode;
                    parent.insertBefore(notification, addressEditForm);
                    
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                    
                    addressEditForm.classList.add('hidden');
                    addressDisplay.classList.remove('hidden');
                    
                    if (editAddressButton && editAddressButton.classList.contains('hidden')) {
                        editAddressButton.classList.remove('hidden');
                    }
                } else {
                    const notification = document.createElement('div');
                    notification.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 mt-4';
                    notification.textContent = result.message || 'Error updating shipping address. Please try again.';
                    
                    const parent = addressEditForm.parentNode;
                    parent.insertBefore(notification, addressEditForm);
                    
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                }
            } catch (error) {
                console.error('Error:', error);
                const notification = document.createElement('div');
                notification.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 mt-4';
                notification.textContent = 'Network error. Please try again later.';
                
                const parent = addressEditForm.parentNode;
                parent.insertBefore(notification, addressEditForm);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        });
    }
});