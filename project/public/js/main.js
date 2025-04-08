

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
    console.log('hy bro!');
    const data = await fetch(`/cart/count`, {
      method: "GET",
    });
    const response = await data.json();
    console.log(response);
    countItem.textContent = response.count;
};

export default updateCount;


updateCount();




