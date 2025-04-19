document.addEventListener('DOMContentLoaded', function() {
    // Configuration de pagination
    const ordersTable = document.querySelector('tbody');
    const paginationContainer = document.getElementById('pagination-container');
    const paginationInfo = document.getElementById('pagination-info');
    
    const rowsPerPage = 10; 
    let currentPage = 1;
    let filteredRows = [];

    function displayRows() {
        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = Math.min(startIndex + rowsPerPage, filteredRows.length);
      
      Array.from(ordersTable.querySelectorAll('tr')).forEach(row => {
        row.classList.add('hidden');
      });
      
      filteredRows.slice(startIndex, endIndex).forEach(row => {
        row.classList.remove('hidden');
      });
      
      if (paginationInfo) {
        paginationInfo.innerHTML = `Showing <span class="font-medium">${startIndex + 1}</span> to <span class="font-medium">${endIndex}</span> of <span class="font-medium">${filteredRows.length}</span> orders`;
      }
    }
    
    function createPagination() {
      
      paginationContainer.innerHTML = '';
      
      const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
      
      if (totalPages <= 1) return; 
      
      const nav = document.createElement('nav');
      nav.className = 'relative z-0 inline-flex rounded-md shadow-sm -space-x-px';
      nav.setAttribute('aria-label', 'Pagination');
      
      const prevButton = document.createElement('a');
      prevButton.href = '#';
      prevButton.className = 'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50';
      prevButton.innerHTML = '<span class="sr-only">Previous</span><i class="fas fa-chevron-left text-xs"></i>';
      prevButton.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentPage > 1) {
          currentPage--;
          displayRows();
          createPagination();
        }
      });
      nav.appendChild(prevButton);
      
      for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement('a');
        pageButton.href = '#';
        pageButton.className = i === currentPage 
          ? 'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue text-sm font-medium text-white' 
          : 'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50';
        pageButton.textContent = i;
        pageButton.addEventListener('click', function(e) {
          e.preventDefault();
          currentPage = i;
          displayRows();
          createPagination();
        });
        nav.appendChild(pageButton);
      }
      
      const nextButton = document.createElement('a');
      nextButton.href = '#';
      nextButton.className = 'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50';
      nextButton.innerHTML = '<span class="sr-only">Next</span><i class="fas fa-chevron-right text-xs"></i>';
      nextButton.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentPage < totalPages) {
          currentPage++;
          displayRows();
          createPagination();
        }
      });
      nav.appendChild(nextButton);
      
      paginationContainer.appendChild(nav);
    }
    
    function applyFilters() {
      
      const statusFilter = document.getElementById('status-filter');
      const dateFilter = document.getElementById('date-filter');
      const sortSelect = document.getElementById('sort-by');
      
      const status = statusFilter ? statusFilter.value : 'All';
      const dateRange = dateFilter ? dateFilter.value : 'all-time';
      const sortValue = sortSelect ? sortSelect.value : 'newest';
      
      filteredRows = Array.from(ordersTable.querySelectorAll('tr'));
      
      if (status !== 'All') {
        filteredRows = filteredRows.filter(row => {
          const statusElement = row.querySelector('.status-value');
          return statusElement && statusElement.textContent.trim() === status;
        });
      }

      
      if (dateRange !== 'all-time') {
        const today = new Date();
        const startDate = new Date();
        
        switch (dateRange) {
          case 'today':
            startDate.setHours(0, 0, 0, 0);
            break;
          case 'this-week':
            startDate.setDate(today.getDate() - today.getDay()); 
            startDate.setHours(0, 0, 0, 0);
            break;
          case 'this-month':
            startDate.setDate(1); 
            startDate.setHours(0, 0, 0, 0);
            break;
          case 'last-month':
            startDate.setMonth(today.getMonth() - 1);
            startDate.setDate(1);
            startDate.setHours(0, 0, 0, 0);
            break;
        }
        
        filteredRows = filteredRows.filter(row => {
          const dateCell = row.querySelector('td:nth-child(3)');
          if (!dateCell) return false;
          
          const orderDate = new Date(dateCell.getAttribute('data-date') || dateCell.textContent);
          
          if (dateRange === 'last-month') {
            const lastMonthStart = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
            return orderDate >= lastMonthStart && orderDate <= lastMonthEnd;
          }
          
          return orderDate >= startDate;
        });
      }
      
      filteredRows.sort((a, b) => {
        const getOrderId = row => parseInt(row.querySelector('td:nth-child(1)')?.textContent.replace('#', ''));
        const getAmount = row => parseFloat(row.querySelector('td:nth-child(4)')?.textContent.replace('$', ''));
        
        switch (sortValue) {
          case 'oldest':
            return getOrderId(a) - getOrderId(b);
          case 'amount-asc':
            return getAmount(a) - getAmount(b);
          case 'amount-desc':
            return getAmount(b) - getAmount(a);
          case 'newest':
          default:
            return getOrderId(b) - getOrderId(a);
        }
      });
      
      currentPage = 1;
      displayRows();
      createPagination();
    }
    
    if (ordersTable) {

      filteredRows = Array.from(ordersTable.querySelectorAll('tr'));
      
      displayRows();
      createPagination();
      
      const statusFilter = document.getElementById('status-filter');
      const dateFilter = document.getElementById('date-filter');
      const sortSelect = document.getElementById('sort-by');
      
      if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
      }
      
      if (dateFilter) {
        dateFilter.addEventListener('change', applyFilters);
      }
      
      if (sortSelect) {
        sortSelect.addEventListener('change', applyFilters);
      }
    }
    
 
  });