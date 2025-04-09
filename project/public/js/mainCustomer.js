document.addEventListener('DOMContentLoaded', () => {
    const navLinks = document.querySelectorAll('#sidebar nav a');
    const currentPath = window.location.pathname;

    navLinks.forEach(link => {
      const href = link.getAttribute('href');

      if (href && href !== '#' && currentPath.includes(href)) {
        setActiveLink(link);
      }

      link.addEventListener('click', function (e) {
        if (!this.getAttribute('href') || this.getAttribute('href') === '#') {
          e.preventDefault();
          setActiveLink(this);
        }
      });
    });

    function setActiveLink(activeLink) {
      navLinks.forEach(link => {
        link.classList.remove('bg-cream', 'text-gold');
        link.classList.add('text-charcoal', 'hover:text-gold');

        const icon = link.querySelector('i');
        if (icon) {
          icon.classList.remove('text-gold');
        }
      });

      activeLink.classList.add('bg-cream', 'text-gold');
      activeLink.classList.remove('text-charcoal', 'hover:text-gold');

      const icon = activeLink.querySelector('i');
      if (icon) {
        icon.classList.add('text-gold');
      }
    }
  });