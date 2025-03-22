

const addSizeBtn = document.getElementById('addSizeBtn');
const sizesContainer = document.getElementById('sizesContainer');
addSizeBtn.addEventListener('click', () => {
  const sizeRow = document.querySelector('.size-row').cloneNode(true);
  
  const inputs = sizeRow.querySelectorAll('input');
  inputs.forEach((input ) => {
      input.value = '' ;
  });
 

  const removeBtn = sizeRow.querySelector('.remove-size');
  removeBtn.addEventListener('click', () => {
    sizeRow.remove();
  });
  
  sizesContainer.appendChild(sizeRow);
});

// let countSizeRow =  localStorage.getItem("countSizeRow");
// console.log(countSizeRow);
// addSizeBtn.addEventListener('click', () => {
//   const sizeRow = document.querySelector('.size-row').cloneNode(true);

//   const sizeNameInput = sizeRow.querySelector('[name="size_name[]"]');
//   sizeNameInput.value = '';
 
//   sizesContainer.appendChild(sizeRow);
  

// });
document.querySelector('.remove-size').addEventListener('click', function(e) {
  if (document.querySelectorAll('.size-row').length > 1) {
    e.target.closest('.size-row').remove();
  }
});



//clore

const addColorBtn = document.getElementById('addColorBtn');
const coloresContainer = document.getElementById('coloresContainer');


addColorBtn.addEventListener('click', () => {
  const colorRow = document.querySelector('.color-row').cloneNode(true);
  const inputs = colorRow.querySelectorAll('input');
  inputs.forEach(input => input.value = '');
  
  const removeBtn = colorRow.querySelector('.remove-color');
  removeBtn.addEventListener('click', () => {
    colorRow.remove();
  });
  
  coloresContainer.appendChild(colorRow);
});

document.querySelector('.remove-color').addEventListener('click', function(e) {
  if (document.querySelectorAll('.color-row').length > 1) {
    e.target.closest('.color-row').remove();
  }
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

const statusFilter = document.getElementById('status-filter');

statusFilter.addEventListener('change', function(){
     const status = this.value;
     const rows = productsTable.querySelectorAll('tr');
     rows.forEach(row => {
         const rowStatus = row.querySelector('.status-value').textContent.trim();
         
         if(status === 'All' || status === rowStatus) {
            row.classList.remove('hidden'); 
        } else {
            row.classList.add('hidden'); 
        }
        
    });  

})

});

const previewContainer = document.getElementById('imagePreviews');
let uploadedImages = [];
document.getElementById('imageInput').addEventListener('change', function(event) {
    const files = event.target.files;
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (file && file.type.startsWith('image/')) { 
            const reader = new FileReader(); 
            
            reader.onload = function(e) {
                const imgElement = document.createElement('img');
                imgElement.src = e.target.result;
                imgElement.style.maxWidth = '150px';
                imgElement.style.maxHeight = '150px';
                imgElement.classList.add('m-2', 'rounded');
                
                uploadedImages.push(e.target.result);
                
                const imgContainer = document.createElement('div');
                imgContainer.classList.add('relative', 'inline-block');
                
                imgContainer.appendChild(imgElement);
                
                previewContainer.appendChild(imgContainer);
                
            };
            
            reader.readAsDataURL(file); 
        }
    }
});

const form = document.getElementById('productForm');


form.addEventListener("submit" ,  async (e) => {
  e.preventDefault();
  const formData = new FormData(form);
  const data = {};
  formData.forEach((value, key) => {
      data[key] = value;
    });
    console.log(data);
    const response = await fetch("/products/store", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
  });
  const result = await response.json();

})


const deleteButtons = document.querySelectorAll('.delete-product');
  
  deleteButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      
        const productId = this.getAttribute('data-product-id');
        const data = {};
        data.id = productId;
        fetch(`/products/delete`, {
          method: 'DELETE',
          body: JSON.stringify(data),

        })
        // .then(response => {
        //   if (response.ok) {
        //     // Supprimer l'élément du DOM ou rediriger
        //     window.location.reload();
        //   } else {
        //     alert('Failed to delete the product.');
        //   }
        // })
        // .catch(error => {
        //   console.error('Error:', error);
        // });
      
    });
  });
