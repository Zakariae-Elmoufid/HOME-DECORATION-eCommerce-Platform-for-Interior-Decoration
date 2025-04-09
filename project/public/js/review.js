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
  });