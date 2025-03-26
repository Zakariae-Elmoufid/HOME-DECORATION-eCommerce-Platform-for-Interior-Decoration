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
})


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



    const searchBar = document.getElementById("search-bar");
    const resultsContainer = document.getElementById("search-results");
    const searchInput = searchBar.querySelector("input");

searchBar.addEventListener("submit" , async (e)=>{
    e.preventDefault();
    const query = searchInput.value.trim();


        const data = {
            keyword: query
        };

       
          console.log(data);
          const response = await fetch(`/products/search`, {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
              },
            body: JSON.stringify(data),
        });
        const result = await response.json();
        console.log(result);


})
});

const addButtons = document.querySelectorAll('.add-product');

addButtons.forEach(button => {
  button.addEventListener('click', async function() {
 
  })
});
