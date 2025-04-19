import displayMessage from "./alert.js"

// Code existant pour les tailles et couleurs...
// [Votre code pour addSizeBtn, sizesContainer, addColorBtn, coloresContainer...]

// Sorting et filtrage
document.addEventListener('DOMContentLoaded', function() {
  const sortSelect = document.getElementById('sort-by');
  const productsTable = document.querySelector('tbody');
  const paginationContainer = document.querySelector('.flex-1.flex.justify-center');
  const paginationInfo = document.querySelector('.text-sm.text-gray-700');
  
  // Configuration de pagination
  const itemsPerPage = 5; // Nombre d'éléments par page
  let currentPage = 1;
  let filteredRows = [];
  
  // Fonction pour afficher les lignes de la page actuelle
  function displayRows() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    
    // Masquer toutes les lignes
    filteredRows.forEach(row => row.classList.add('hidden'));
    
    // Afficher seulement les lignes de la page actuelle
    filteredRows.slice(startIndex, endIndex).forEach(row => {
      row.classList.remove('hidden');
    });
    
    // Mettre à jour le texte de pagination
    if (paginationInfo) {
      paginationInfo.innerHTML = `Showing <span class="font-medium">${startIndex + 1}</span> to <span class="font-medium">${Math.min(endIndex, filteredRows.length)}</span> of <span class="font-medium">${filteredRows.length}</span> products`;
    }
  }
  
  // Créer les boutons de pagination
  function createPagination() {
    if (!paginationContainer) return;
    
    // Vider le conteneur
    paginationContainer.innerHTML = '';
    
    // Calculer le nombre total de pages
    const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
    
    if (totalPages <= 1) return; // Pas besoin de pagination s'il n'y a qu'une page
    
    // Créer la navigation
    const nav = document.createElement('nav');
    nav.className = 'relative z-0 inline-flex rounded-md shadow-sm -space-x-px';
    nav.setAttribute('aria-label', 'Pagination');
    
    // Bouton Previous
    const prevBtn = document.createElement('a');
    prevBtn.href = '#';
    prevBtn.className = 'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50';
    prevBtn.innerHTML = '<span class="sr-only">Previous</span><i class="fas fa-chevron-left text-xs"></i>';
    prevBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (currentPage > 1) {
        currentPage--;
        displayRows();
        createPagination();
      }
    });
    nav.appendChild(prevBtn);
    
    // Boutons de page
    for (let i = 1; i <= totalPages; i++) {
      const pageBtn = document.createElement('a');
      pageBtn.href = '#';
      pageBtn.className = i === currentPage 
        ? 'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue text-sm font-medium text-white' 
        : 'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50';
      pageBtn.textContent = i;
      pageBtn.addEventListener('click', (e) => {
        e.preventDefault();
        currentPage = i;
        displayRows();
        createPagination();
      });
      nav.appendChild(pageBtn);
    }
    
    // Bouton Next
    const nextBtn = document.createElement('a');
    nextBtn.href = '#';
    nextBtn.className = 'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50';
    nextBtn.innerHTML = '<span class="sr-only">Next</span><i class="fas fa-chevron-right text-xs"></i>';
    nextBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (currentPage < totalPages) {
        currentPage++;
        displayRows();
        createPagination();
      }
    });
    nav.appendChild(nextBtn);
    
    paginationContainer.appendChild(nav);
  }
  
  // Appliquer tous les filtres et mettre à jour la pagination
  function applyFiltersAndPagination() {
    if (!productsTable) return;
    
    // Récupérer les valeurs des filtres
    const categoryFilter = document.getElementById('category-filter');
    const statusFilter = document.getElementById('status-filter');
    const category = categoryFilter ? categoryFilter.value : 'All Categories';
    const status = statusFilter ? statusFilter.value : 'All';
    
    // Obtenir toutes les lignes
    const allRows = Array.from(productsTable.querySelectorAll('tr'));
    
    // Filtrer les lignes selon les critères
    filteredRows = allRows.filter(row => {
      // Vérifier catégorie
      const rowCategory = row.querySelector('.category-value')?.textContent;
      const categoryMatch = category === 'All Categories' || category === rowCategory;
      
      // Vérifier statut
      const rowStatus = row.querySelector('.status-value')?.textContent.trim();
      const statusMatch = status === 'All' || status === rowStatus;
      
      return categoryMatch && statusMatch;
    });
    
    // Trier les lignes si nécessaire
    if (sortSelect && sortSelect.value) {
      const sortValue = sortSelect.value;
      
      const getValue = (row, sortType) => {
        switch(sortType) {
          case 'name-asc':
          case 'name-desc':
            return row.querySelector('td:nth-child(1) .text-navy')?.textContent.trim();
          case 'price-asc':
          case 'price-desc':
            return parseFloat(row.querySelector('td:nth-child(3) .text-sm')?.textContent.replace('$', ''));
          case 'newest':
          default:
            return parseInt(row.querySelector('td:nth-child(1) .text-gray-500')?.textContent.replace('ID: ', ''));
        }
      };
      
      filteredRows.sort((a, b) => {
        const valueA = getValue(a, sortValue);
        const valueB = getValue(b, sortValue);
        
        if (sortValue === 'name-desc' || sortValue === 'price-desc') {
          return valueB > valueA ? 1 : -1;
        } else {
          return valueA > valueB ? 1 : -1;
        }
      });
    }
    
    // Revenir à la première page après un filtre
    currentPage = 1;
    
    // Afficher les lignes et mettre à jour la pagination
    displayRows();
    createPagination();
  }
  
  // Initialiser la pagination
  if (productsTable) {
    // Filtrer initialement toutes les lignes
    filteredRows = Array.from(productsTable.querySelectorAll('tr'));
    
    // Afficher les lignes et créer la pagination
    displayRows();
    createPagination();
    
    // Configuration des écouteurs d'événements pour les filtres
    if (sortSelect) {
      sortSelect.addEventListener('change', applyFiltersAndPagination);
    }
    
    const categoryFilter = document.getElementById('category-filter');
    if (categoryFilter) {
      categoryFilter.addEventListener('change', applyFiltersAndPagination);
    }
    
    const statusFilter = document.getElementById('status-filter');
    if (statusFilter) {
      statusFilter.addEventListener('change', applyFiltersAndPagination);
    }
  }
  
  // Reste de votre code existant...
  const deleteButtons = document.querySelectorAll('.delete-product');
  // [Votre code pour les boutons de suppression...]
});