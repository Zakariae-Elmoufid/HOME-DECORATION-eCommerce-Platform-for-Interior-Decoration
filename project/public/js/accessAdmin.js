document.addEventListener('DOMContentLoaded', function() {
    const addAdminBtn = document.getElementById('addAdminBtn');
    const addAdminModal = document.getElementById('addAdminModal');
    const closeModal = document.getElementById('closeModal');
    const cancelAddAdmin = document.getElementById('cancelAddAdmin');
  

    // Add Admin Modal
    addAdminBtn.addEventListener('click', () => {
      addAdminModal.classList.remove('hidden');
    });
  
    [closeModal, cancelAddAdmin].forEach(btn => {
      btn.addEventListener('click', () => {
        addAdminModal.classList.add('hidden');
      });
    });
  
  
    // Form submissions
    // document.getElementById('adminForm').addEventListener('submit', function(e) {
    //   e.preventDefault();
    //   // Handle form submission to backend
    //   alert('New admin added!');
    //   addAdminModal.classList.add('hidden');
    //   // Reset form
    //   this.reset();
    // });
  
   
  });
  