document.addEventListener('DOMContentLoaded', function() {
const productsCard = Array.from(document.querySelectorAll('.product-card '));
const productContainer = document.getElementById("product-container");

const categoryFilter =  document.getElementById('category-filter');
categoryFilter.addEventListener('change', function() {
    const category = this.value;
    productsCard.forEach(product => {
        const cardCategory = product.querySelector('.category-value').textContent;
        console.log(cardCategory);
        product.classList.add('hidden'); 

        if(category === 'All Categories' || category === cardCategory) {
            product.classList.remove('hidden'); 
        } else {
            product.classList.add('hidden'); 
        }
        
    });  
    
    
    const sortSelect = document.getElementById('sort-by');
    
    sortSelect.addEventListener("change" , function() {
      const sortValue = this.value;
      const getValue = (card, sortType) => {
        switch(sortType) {
          case 'name-asc':
            case 'name-desc':
              return card.querySelector('.title').textContent.trim();
              case 'price-asc':
                case 'price-desc':
                  return parseFloat(card.querySelector('.price ').textContent.replace('$', ''));
                  
                }
              };
              
              
              productsCard.sort((a, b) => {
                const valueA = getValue(a, sortValue);
                const valueB = getValue(b, sortValue);
                
                if (sortValue === 'name-desc' || sortValue === 'price-desc') {
      return valueB > valueA ? 1 : -1;
    } else {
      return valueA > valueB ? 1 : -1;
    }
  });
  
  productsCard.forEach(card => productContainer.appendChild(card));
});




});

const addButtons = document.querySelectorAll('.add-product');

addButtons.forEach(button => {
  button.addEventListener('click', async function() {
    
  })
});


loadProducts(1)
function loadProducts(page = 1) {
  fetch(`/products/fetch?page=${page}`)
    .then(res => res.json())
    .then(data => {
      renderProducts(data.products);
      renderPagination(data.totalPages, page);
    })
    .catch(err => console.error("Erreur AJAX :", err));
}

function renderProducts(products) {
  const container = document.getElementById("product-container");
  container.innerHTML = "";

  products.forEach(product => {
    const rating = renderStars(product.average_rating || 0);
    const html = `
      <div class="product-card bg-white rounded-lg overflow-hidden shadow-md hover:-translate-y-2 hover:shadow-xl transition">
        <div class="relative group">
          <img src="/public/uploads/${product.primary_image}" class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-105" />
          <div class="absolute top-2 left-2">
            <span class="text-xs font-bold px-3 py-1 rounded-full ${
              product.stock === 0
                ? 'bg-red-500 text-white'
                : product.stock <= 5
                ? 'bg-orange-500 text-white'
                : 'bg-gold text-white'
            }">
              ${
                product.stock === 0
                  ? 'Out of Stock'
                  : product.stock <= 5
                  ? 'Limited Stock'
                  : 'Available'
              }
            </span>
          </div>
        </div>
        <div class="p-4">
          <h3 class="text-lg font-medium text-charcoal">${product.title}</h3>
          <p class="text-sm text-gray-500 mb-2">${product.category_name}</p>
          <div class="flex items-center mb-3">
            <div class="flex text-gold">
              ${rating}
              <span class="text-xs text-gray-500 ml-1">(${product.average_rating ?? 0}  reviews)</span>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-xl font-bold text-charcoal">$${product.base_price}</span>
            ${
               product.stock > 0
                ? `<a href="/product?id=${product.id}" class="bg-gold hover:bg-gold-dark text-white px-3 py-2 rounded-lg flex items-center"><i class="fas fa-eye mr-1"></i>See</a>`
                : `<button disabled class="bg-gray-300 text-gray-600 px-3 py-2 rounded-lg flex items-center cursor-not-allowed"><i class="fas fa-eye mr-1"></i>Unavailable</button>`
            }
          </div>
        </div>
      </div>
    `;
    container.insertAdjacentHTML("beforeend", html);
  });
}
function renderStars(rating) {
  let stars = "";
  for (let i = 1; i <= 5; i++) {
    if (rating >= i) {
      stars += '<i class="fas fa-star"></i>';
    } else if (rating >= i - 0.5) {
      stars += '<i class="fas fa-star-half-alt"></i>';
    } else {
      stars += '<i class="far fa-star"></i>';
    }
  }
  return stars;
}

function renderPagination(totalPages, currentPage) {
  const pagination = document.getElementById("pagination");
  
  const prevButton = pagination.querySelector('.pagination-prev');
  const nextButton = pagination.querySelector('.pagination-next');
  const pageButtons = pagination.querySelectorAll('.pagination-page');
  
  if (prevButton) {
    prevButton.dataset.page = currentPage - 1;
    if (currentPage === 1) {
      prevButton.classList.add('text-gray-400', 'bg-gray-100', 'border-gray-200', 'cursor-not-allowed');
      prevButton.classList.remove('text-gold', 'bg-white', 'border-gold', 'hover:bg-gold', 'hover:text-white');
    } else {
      prevButton.classList.remove('text-gray-400', 'bg-gray-100', 'border-gray-200', 'cursor-not-allowed');
      prevButton.classList.add('text-gold', 'bg-white', 'border-gold', 'hover:bg-gold', 'hover:text-white');
    }
  }
  
  if (nextButton) {
    nextButton.dataset.page = currentPage + 1;
    
    if (currentPage === totalPages) {
      nextButton.classList.add('text-gray-400', 'bg-gray-100', 'border-gray-200', 'cursor-not-allowed');
      nextButton.classList.remove('text-gold', 'bg-white', 'border-gold', 'hover:bg-gold', 'hover:text-white');
    } else {
      nextButton.classList.remove('text-gray-400', 'bg-gray-100', 'border-gray-200', 'cursor-not-allowed');
      nextButton.classList.add('text-gold', 'bg-white', 'border-gold', 'hover:bg-gold', 'hover:text-white');
    }
  }
  
  // Update page number buttons
  // This assumes you already have the correct number of page buttons in your HTML
  pageButtons.forEach((button, index) => {
    const pageNum = index + 1;
    button.dataset.page = pageNum;
    
    if (pageNum === currentPage) {
      button.classList.add('bg-gold', 'text-white', 'border-gold');
      button.classList.remove('text-charcoal', 'bg-white', 'hover:bg-cream', 'hover:text-gold');
    } else {
      button.classList.remove('bg-gold', 'text-white', 'border-gold');
      button.classList.add('text-charcoal', 'bg-white', 'hover:bg-cream', 'hover:text-gold');
    }
    
    // Update the text content to show the correct page number
    button.textContent = pageNum;
    
    // Make sure button is visible if we have the right number of pages
    // button.parentElement.style.display = pageNum <= totalPages ? 'block' : 'none';
  });
  
  // Add event listeners
  document.querySelectorAll("#pagination a").forEach(link => {
    // Remove existing event listeners (to prevent duplicates)
    const clone = link.cloneNode(true);
    link.parentNode.replaceChild(clone, link);
    
    // Add new event listener
    clone.addEventListener("click", e => {
      e.preventDefault();
      
      const target = e.currentTarget;
      const page = parseInt(target.dataset.page);
      
      const isDisabled = target.classList.contains('cursor-not-allowed');
      
      if (!isNaN(page) && !isDisabled) {
        loadProducts(page);
      }
    });
  });
}

})