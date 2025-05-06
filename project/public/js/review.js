import {displayMessage} from "./alert.js";

document.addEventListener('DOMContentLoaded', function() {

const stars = document.querySelectorAll('.rating-star');
const starsLabels = document.querySelectorAll('.rating-star label');

stars.forEach(star => {
  star.addEventListener('click', function() {
    const value = this.dataset.value;
    const input = document.getElementById('star' + value);
    input.checked = true;
    
        starsLabels.forEach((label, index) => {
          if (index < value) {
            label.classList.add('text-yellow-500');
            label.classList.remove('text-gray-300');
          } else {
            label.classList.add('text-gray-300');
            label.classList.remove('text-yellow-500');
          }
        });
      });
    });







    
    

    const deleteButtons = document.querySelectorAll('.delete-review-btn');
    const modal = document.getElementById('deleteReviewModal');
  
    const cancelBtn = document.getElementById('cancelDeleteBtn');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    let reviewIdToDelete = null;
  
    deleteButtons.forEach(button => {
      button.addEventListener('click', function() {
        reviewIdToDelete = this.getAttribute('data-review-id');
        modal.classList.remove('hidden');
      });
    });
  
    // Hide modal when cancel button is clicked
    cancelBtn.addEventListener('click', function() {
      modal.classList.add('hidden');
      reviewIdToDelete = null;
    });
  
    confirmBtn.addEventListener('click', function() {
      if (reviewIdToDelete) {
        fetch(`/customer/review/delete?id=${reviewIdToDelete}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
              modal.classList.add('hidden');
              displayMessage(data.success,'/customer/myReview');
            } else {
              modal.classList.add('hidden');
              displayMessage(data.error,'/customer/myReview','error');
            }
        })
        
      }
    });
  
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        modal.classList.add('hidden');
        reviewIdToDelete = null;
      }
    });
  
    const editButtons = document.querySelectorAll('.edit-review-btn');
    
    editButtons.forEach(button => {
      button.addEventListener('click', function() {
        const reviewId = this.getAttribute('data-review-id');
        const reviewItem = document.getElementById(`review-${reviewId}`);
        
        reviewItem.querySelector('.review-view').classList.add('hidden');
        reviewItem.querySelector('.review-edit').classList.remove('hidden');
      });
    });
    
    document.querySelectorAll('.cancel-edit-btn').forEach(button => {
      button.addEventListener('click', function() {
        const reviewId = this.getAttribute('data-review-id');
        const reviewItem = document.getElementById(`review-${reviewId}`);
        
        // Show view, hide edit form
        reviewItem.querySelector('.review-view').classList.remove('hidden');
        reviewItem.querySelector('.review-edit').classList.add('hidden');
      });
    });
    
    // Rating star functionality
    document.querySelectorAll('.rating-stars').forEach(starsContainer => {
      const stars = starsContainer.querySelectorAll('.rating-star');
      const ratingInput = starsContainer.nextElementSibling;
      
      stars.forEach(star => {
        // Click to select rating
        star.addEventListener('click', function() {
          const value = parseInt(this.getAttribute('data-value'));
          ratingInput.value = value;
          
          // Update visual appearance
          stars.forEach(s => {
            const starValue = parseInt(s.getAttribute('data-value'));
            if (starValue <= value) {
              s.classList.add('text-gold', 'selected');
              s.classList.remove('text-gray-300');
            } else {
              s.classList.remove('text-gold', 'selected');
              s.classList.add('text-gray-300');
            }
          });
        });
        
        // Hover effects
        star.addEventListener('mouseenter', function() {
          const value = parseInt(this.getAttribute('data-value'));
          
          stars.forEach(s => {
            const starValue = parseInt(s.getAttribute('data-value'));
            if (starValue <= value) {
              s.classList.add('text-gold');
              s.classList.remove('text-gray-300');
            } else {
              s.classList.remove('text-gold');
              s.classList.add('text-gray-300');
            }
          });
        });
      });
      
      // Restore selected stars when mouse leaves
      starsContainer.addEventListener('mouseleave', function() {
        const selectedValue = parseInt(ratingInput.value) || 0;
        
        stars.forEach(s => {
          const starValue = parseInt(s.getAttribute('data-value'));
          if (starValue <= selectedValue) {
            s.classList.add('text-gold');
            s.classList.remove('text-gray-300');
          } else {
            s.classList.remove('text-gold');
            s.classList.add('text-gray-300');
          }
        });
      });
    });
    
    // Handle review form submissions
    document.querySelectorAll('.edit-review-form').forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const reviewId = this.getAttribute('data-review-id');
        const rating = this.querySelector('.rating-input').value;
        const content = this.querySelector('textarea[name="content"]').value.trim();
        
        // Form validation
        if (parseInt(rating) < 1) {
          alert('Please select a rating');
          return;
        }
        
        if (content.length < 3) {
          alert('Please enter a review with at least 3 characters');
          return;
        }
        
        fetch('/customer/review/update', {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            review_id: reviewId,
            rating: rating,
            content: content
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
              
            displayMessage(data.success,'/customer/myReview');
            
          } else {
            displayMessage(data.error,'/customer/myReview','error');
          }
        })
       
      });
    });
  });