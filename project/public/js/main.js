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