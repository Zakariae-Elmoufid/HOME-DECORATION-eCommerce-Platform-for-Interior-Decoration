const addCategoryModal = document.getElementById("categoryModal");
const formAdd = document.getElementById("categorysForm");
const title = document.querySelector('[name="title"]');
const icon = document.querySelector('[name="icon"]');
console.log(icon);
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
    console.log(result);
    let hasErrors = false; 

    const inputs = formAdd.querySelectorAll('input');
        
        inputs.forEach((input) => {
            input.nextElementSibling.textContent = "";

            const fieldName = input.name;
            const errorMessages = result.errors[fieldName];

            if (errorMessages) {
                hasErrors = true;
                errorMessages.forEach((message) => {
                input.nextElementSibling.textContent = message;
                });
            }    
        });
    




        // closeModal(modalAddCategories, formAdd);
        if (!hasErrors) {
        console.log(result);
        }
        
});
    
 
