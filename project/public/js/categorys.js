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
    let icon = result["icon"];
    let title = result["title"];



    fermModal(modalAddCategories, formAdd);
    alert(icon, title);
    fetchallCategories();  
});
m
function alert(iconparam, titleparam) {
    const Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
      },
    });
    Toast.fire({
      icon: iconparam,
      title: titleparam,
    });
  }