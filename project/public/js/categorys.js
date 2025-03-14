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
            console.log(result.message);
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

const fetchallCategories = async () => {
    const data = await fetch("allCategorys", {
      method: "GET",
    });
    const response = await data.json();
 
    displayCategories(response);
  };
  fetchallCategories();

  function displayCategories(data) {
    console.log("Données reçues :", data);
console.log("Type de data :", typeof data);
    const tbody = document.getElementById("categoryTableBody");
    let tableRows = "";
  
    data.data.forEach((element) => {
      tableRows += `<tr>
      <td class="px-6 py-4 text-gray-600">
        <div class="text-xl"><iconify-icon icon="${element.icon}"></iconify-icon></div> 
       </td>
          <td class="px-6 py-4 text-gray-600">${element.tile} </td>
          <td class="px-6 py-4">
              <div class="flex items-center justify-end gap-2">
                  <a id="${element["id"]}" onclick="ouverModal(modalUpdateCategories)" class="text-gray-400 hover:text-blue-500 transition-colors editLink">
                      <svg  class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                      </svg>
                  </a>
                  <button id="${element["id"]}" class="text-gray-400 hover:text-red-500 transition-colors deleteLink ">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                  </button>
              </div>
          </td>
      </tr>`;
    });
  
    tbody.innerHTML = tableRows;
  }
  