
const addCategoryModal = document.getElementById("categoryModal");
const formAdd = document.getElementById("categorysForm");
const title = document.querySelector('[name="title"]');
const icon = document.querySelector('[name="icon"]');

function closeModal(modal, form) {
    modal.classList.add("hidden");
    form.reset();
}

function openModal(modal){
    modal.classList.remove("hidden");
}



formAdd.addEventListener("submit" , async (e) => {
    e.preventDefault();
    const formData = new FormData(formAdd);
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
      });

    const response = await fetch("/categorys/store", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    });
    
    const result = await response.json();

    let hasErrors = false; 

    const inputs = formAdd.querySelectorAll('input');
        
    if (result.errors) {
        hasErrors = true;
        inputs.forEach((input) => {
            input.nextElementSibling.textContent = "";

            const fieldName = input.name;
            const errorMessages = result.errors[fieldName];

                errorMessages.forEach((message) => {
                input.nextElementSibling.textContent = message;
                });
            });
        }    
        if(!hasErrors) {
        closeModal(addCategoryModal, formAdd);
        displayMessage(result.message);
        }
});

function displayMessage(responseMessage){
    const alert = document.getElementById("alert");
    const message = document.getElementById('message');
    alert.classList.remove('hidden');
    message.textContent = responseMessage;
    setTimeout(function(){ alert.classList.add('hidden')}, 3000)
}

let categoriesData = []
const fetchallCategories = async () => {
    const data = await fetch("allCategorys", {
      method: "GET",
    });
    const response = await data.json();
    categoriesData = response;
    displayCategories();

};
  fetchallCategories();

  function displayCategories() {
    const tableBody = document.getElementById('categoryTableBody');
    tableBody.innerHTML = '';
  
    categoriesData.forEach(element => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition-colors duration-200';
            
            const iconCell = document.createElement('td');
            iconCell.className = 'px-6 py-4 text-gray-600';
            iconCell.innerHTML = `<div class="text-xl"><iconify-icon icon="${element.icon}"></iconify-icon></div>`;
            iconCell.dataset.type = 'icon';
            iconCell.dataset.id = element.id;
            iconCell.dataset.value = element.icon;

            
            const titleCell = document.createElement('td');
            titleCell.className = 'px-6 py-4 text-gray-600';
            titleCell.textContent = element.title;
            titleCell.dataset.type = 'title';
            titleCell.dataset.id = element.id;
            titleCell.dataset.value = element.title;

            
            const actionCell = document.createElement('td');
            actionCell.className = 'px-6 py-4 whitespace-nowrap text-center';
            actionCell.innerHTML = `
              <div class="flex justify-center space-x-3">
                <button class=" text-red-600 hover:text-red-900" onclick="openModeledit(${element.id})">
                  <div class="text-lg"><iconify-icon icon="mdi:delete"></iconify-icon></div>
                </button>
                <button class=" text-red-600 hover:text-red-900" onclick="deleteItem(${element.id})">
                  <div class="text-lg"><iconify-icon icon="mdi:edit"></iconify-icon></div>
                </button>
              </div>
            `;
            
            row.appendChild(iconCell);
            row.appendChild(titleCell);
            row.appendChild(actionCell);
            tableBody.appendChild(row);
          });
 }



const updateCategoryModal = document.getElementById('updateCategoryModal');
const updatForm  = document.getElementById('updatCategorysForm');

 function openModeledit(id){
    remplireFormUpdate(id)
    updateCategoryModal.classList.remove("hidden");

}


updatForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const formData = new FormData(updatForm);
  const idInput = updatForm.querySelector("input[name='id']").value;

  const data = {};
  formData.forEach((value, key) => {
    data[key] = value;
  });
  data.id = idInput;

  const response = await fetch("/categorys/update", {
    method: "PATCH",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
});

const result = await response.json();

let hasErrors = false; 

    const inputs = updatForm.querySelectorAll('input');
        
    if (result.errors) {
        hasErrors = true;
        inputs.forEach((input) => {
            input.nextElementSibling.textContent = "";

            const fieldName = input.name;
            const errorMessages = result.errors[fieldName];

                errorMessages.forEach((message) => {
                input.nextElementSibling.textContent = message;
                });
            });
        }    
        if(!hasErrors) {
        closeModal(addCategoryModal, formAdd);
        displayMessage(result.message);
        }

});


 
const remplireFormUpdate = async (id) => {
  const data = await fetch(`/categorys/show?id=${id}`, {
    method: "GET",
  });
  const response = await data.json();

    idInput = document.createElement('input');
    idInput.setAttribute("type","hidden"); 
    idInput.setAttribute("name", "id");                     
    updatForm.getElementsByTagName('input')[0].value  = response.title;
    updatForm.getElementsByTagName('input')[1].value  = response.icon;
    updatForm.appendChild(idInput);
    updatForm.getElementsByTagName('input')[2].value  = response.id;
    
};











