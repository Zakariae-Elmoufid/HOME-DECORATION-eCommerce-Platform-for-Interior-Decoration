const sidebar = document.getElementById('sidebar');
const menu = document.getElementById('menu');

menu.addEventListener('click' , function(){
    sidebar.classList.toggle('hidden');
})
