document.addEventListener("DOMContentLoaded", function () {

const bars = document.getElementById('bars');
bars.addEventListener("click", function () {
    
  const mobileMenu =  document.getElementById('mobile-menu');
  mobileMenu.classList.toggle('hidden');

});

const furniture = document.getElementById('mobile-furniture-dropdown');
furniture.addEventListener('click', function(){
  document.getElementById('furniture-submenu').classList.toggle('hidden');
})
});

const  countItem =  document.getElementById('count-cart-item');

 const  updateCount = async () => {
    const data = await fetch(`/cart/count`, {
      method: "GET",
    });
    const response = await data.json();
    countItem.textContent = response.count;
};

export default updateCount;
updateCount();

const productContainer = document.getElementById("product-container");
const searchForms = document.querySelectorAll('#search-bar');
let resultsContainer = document.getElementById("search-results");

searchForms.forEach(form => {

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const searchInput = form.querySelector("input");
    const query = searchInput.value.trim();

    const data = { keyword: query };
    const existingMark = form.querySelector('.clear-button');
    if (existingMark) existingMark.remove();

    const markButton = document.createElement('button'); 
    markButton.className = 'clear-button bg-gold hover:bg-gold-dark text-white px-4 py-2 rounded-r-lg transition duration-300';
    const icon = document.createElement('i'); 

    icon.classList.add('fa-solid', 'fa-x'); 
    markButton.appendChild(icon);
    form.appendChild(markButton);

    markButton.addEventListener('click', function (e) {
      e.preventDefault(); 
      handleClearSearch(markButton, searchInput);
    });
    const response = await fetch(`/products/search`, {
      method: 'POST',
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    const result = await response.json();
    productContainer.classList.add('hidden')
    resultsContainer.innerHTML = "";
    result.products.forEach(product => {
      const badge = product.stock === 0
        ? `<span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">Out of Stock</span>`
        : product.stock <= 5
        ? `<span class="bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full">Limited Stock</span>`
        : `<span class="bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full">Available</span>`;

      const rating = renderStars(product.average_rating || 0);
       
      const card = document.createElement("div");
      card.className = "product-card bg-white rounded-lg overflow-hidden shadow-md transition-all duration-300 hover:-translate-y-2 hover:shadow-xl";

      card.innerHTML = `
        <div class="relative group">
           <img src="/public/uploads/${ product.primary_image }" alt="${product.title}" 
             class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-105">
          <div class="absolute top-2 left-2">
            ${badge}
          </div>
        </div>
        <div class="p-4">
          <h3 class="text-lg font-medium text-gray-800">${product.title}</h3>
          <p class="text-sm text-gray-500 mb-2">${product.category_name}</p>

          <div class="flex items-center mb-3">
            <div class="flex text-yellow-500 text-sm">
              ${rating}
              <span class="text-xs text-gray-500 ml-2">(${product.average_rating ?? 0} reviews)</span>
            </div>
          </div>

          <div class="flex justify-between items-center">
            <span class="text-xl font-bold text-gray-800">$${product.base_price}</span>
            ${
              product.isAvailable && product.stock > 0
              ? `<a href="/product?id=${product.id}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg transition duration-300 flex items-center">
                    <i class="fas fa-eye mr-1"></i> See 
                 </a>`
              : `<button class="bg-gray-300 text-gray-600 px-3 py-2 rounded-lg flex items-center cursor-not-allowed" disabled>
                    <i class="fas fa-eye mr-1"></i> Unavailable
                 </button>`
            }
          </div>
        </div>
      `;

      resultsContainer.appendChild(card);
    });


  });


});



function renderStars(rating) {
  let stars = "";
  for (let i = 1; i <= 5; i++) {
    if (rating >= i) {
      stars += `<i class="fas fa-star"></i>`; 
    } else if (rating >= i - 0.5) {
      stars += `<i class="fas fa-star-half-alt"></i>`;
    } else {
      stars += `<i class="far fa-star"></i>`; 
    }
  }
  return stars;
}

  function handleClearSearch(markButton,searchInput){


    productContainer.classList.remove('hidden');
    
    searchInput.value = "";

    resultsContainer.innerHTML = "";

     markButton.remove();
  }
