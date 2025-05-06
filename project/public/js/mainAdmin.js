const sidebar = document.getElementById('sidebar');
const menu = document.getElementById('menu');

document.addEventListener('DOMContentLoaded', function() {
menu.addEventListener('click' , function(){
    sidebar.classList.toggle('hidden');
})


    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        if (this.getAttribute('href') !== '#') {
          return;
        }
        
        e.preventDefault(); 
        
        navLinks.forEach(navLink => {
          navLink.classList.remove('bg-blue', 'text-gray-300');
          navLink.classList.add('hover:bg-navy-light', 'text-white');
          
          const icon = navLink.querySelector('i');
          if (icon) { 
            icon.classList.remove('text-white');
            icon.classList.add('text-blue-light');
          }
        });
        
        this.classList.add('bg-blue');
        this.classList.remove('hover:bg-navy-light');
        
        const icon = this.querySelector('i');
        if (icon) {
          icon.classList.remove('text-blue-light');
          icon.classList.add('text-white');
        }
      });
    });
    
    const currentPath = window.location.pathname;
    console.log(currentPath);
    navLinks.forEach(link => {
      const href = link.getAttribute('href');
      if (href !== '#' && currentPath.includes(href)) {
        navLinks.forEach(navLink => {
          navLink.classList.remove('bg-blue');
          const icon = navLink.querySelector('i');
          if (icon) {
            icon.classList.remove('text-white');
            icon.classList.add('text-blue-light');
          }
        });
        
        link.classList.add('bg-blue','text-white');
        link.classList.remove('hover:bg-navy-light' ,'text-gray-300');
        
        const icon = link.querySelector('i');
        if (icon) {
          icon.classList.remove('text-blue-light');
          icon.classList.add('text-white');
        }
      }
    });
  });

  