const addProductBtn = document.getElementById('addProductBtn');
const productModal = document.getElementById('productModal');
const closeModalBtn = document.getElementById('closeModalBtn');
const cancelBtn = document.getElementById('cancelBtn');

addProductBtn.addEventListener('click', () => {
  productModal.classList.remove('hidden');
});

function closeModal() {
  productModal.classList.add('hidden');
}

closeModalBtn.addEventListener('click', closeModal);
cancelBtn.addEventListener('click', closeModal);

productModal.addEventListener('click', (e) => {
  if (e.target === productModal) {
    closeModal();
  }
});

const addSizeBtn = document.getElementById('addSizeBtn');
const sizesContainer = document.getElementById('sizesContainer');

addSizeBtn.addEventListener('click', () => {
  const sizeRow = document.querySelector('.size-row').cloneNode(true);
  const inputs = sizeRow.querySelectorAll('input');
  inputs.forEach(input => input.value = '');
  
  const removeBtn = sizeRow.querySelector('.remove-size');
  removeBtn.addEventListener('click', () => {
    sizeRow.remove();
  });
  
  sizesContainer.appendChild(sizeRow);
});

document.querySelector('.remove-size').addEventListener('click', function(e) {
  if (document.querySelectorAll('.size-row').length > 1) {
    e.target.closest('.size-row').remove();
  }
});

document.getElementById('productForm').addEventListener('submit', (e) => {
  e.preventDefault();
  alert('Product saved successfully!');
  closeModal();
});

//sorting
document.addEventListener('DOMContentLoaded', function() {
const sortSelect = document.getElementById('sort-by');
const productsTable = document.querySelector('tbody');

sortSelect.addEventListener('change', function() {
const sortValue = this.value;
const rows = Array.from(productsTable.querySelectorAll('tr'));
const getValue = (row, sortType) => {
  switch(sortType) {
    case 'name-asc':
    case 'name-desc':
      return row.querySelector('td:nth-child(1) .text-navy').textContent.trim();
    case 'price-asc':
    case 'price-desc':
      return parseFloat(row.querySelector('td:nth-child(3) .text-sm').textContent.replace('$', ''));
    case 'newest':
    default:
   
      return parseInt(row.querySelector('td:nth-child(1) .text-gray-500').textContent.replace('ID: ', ''));
  }
};

rows.sort((a, b) => {
  const valueA = getValue(a, sortValue);
  const valueB = getValue(b, sortValue);
  
  if (sortValue === 'name-desc' || sortValue === 'price-desc') {
    return valueB > valueA ? 1 : -1;
  } else {
    return valueA > valueB ? 1 : -1;
  }
});

rows.forEach(row => productsTable.appendChild(row));
});


// filter 

const categoryFilter =  document.getElementById('category-filter');

categoryFilter.addEventListener('change', function() {
    const category = this.value;
    console.log(category);
    const rows = productsTable.querySelectorAll('tr');
    rows.forEach(row => {
        const rowCategory = row.querySelector('.category-value').textContent;
        row.classList.add('hidden'); 

        if(category === 'All Categories' || category === rowCategory) {
            row.classList.remove('hidden'); 
        } else {
            row.classList.add('hidden'); 
        }
        
    });  
})




});