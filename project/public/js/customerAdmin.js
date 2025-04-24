document.addEventListener('DOMContentLoaded', function () {
  let allCustomers = Array.from(document.querySelectorAll('.customer-row'));
  let currentPage = 1;
  const rowsPerPage = 10;

  const customerTableBody = document.getElementById('customerTableBody');
  const prevPageBtn = document.getElementById('prev-page');
  const nextPageBtn = document.getElementById('next-page');
  const paginationNumbers = document.getElementById('pagination-numbers');

  function displayCustomers() {
    allCustomers.forEach(c => c.classList.add('hidden'));

    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = Math.min(startIndex + rowsPerPage, allCustomers.length);

    for (let i = startIndex; i < endIndex; i++) {
      allCustomers[i].classList.remove('hidden');
    }
  }

  function updatePagination() {
    const totalPages = Math.ceil(allCustomers.length / rowsPerPage);
    let paginationHTML = '';

    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);

    if (endPage - startPage < 4) {
      startPage = Math.max(1, endPage - 4);
    }

    for (let i = startPage; i <= endPage; i++) {
      if (i === currentPage) {
        paginationHTML += `<a href="#" data-page="${i}" class="z-10 bg-blue text-white px-4 py-2 border">${i}</a>`;
      } else {
        paginationHTML += `<a href="#" data-page="${i}" class="bg-white text-gray-700 px-4 py-2 border hover:bg-gray-100">${i}</a>`;
      }
    }

    paginationNumbers.innerHTML = paginationHTML;
    prevPageBtn.classList.toggle('opacity-50', currentPage === 1);
    nextPageBtn.classList.toggle('opacity-50', currentPage === totalPages);
  }

  nextPageBtn.addEventListener('click', function (e) {
    e.preventDefault();
    const totalPages = Math.ceil(allCustomers.length / rowsPerPage);
    if (currentPage < totalPages) {
      currentPage++;
      displayCustomers();
      updatePagination();
    }
  });

  prevPageBtn.addEventListener('click', function (e) {
    e.preventDefault();
    if (currentPage > 1) {
      currentPage--;
      displayCustomers();
      updatePagination();
    }
  });

  paginationNumbers.addEventListener('click', function (e) {
    if (e.target.tagName === 'A' && e.target.getAttribute('data-page')) {
      e.preventDefault();
      currentPage = parseInt(e.target.getAttribute('data-page'));
      displayCustomers();
      updatePagination();
    }
  });

  displayCustomers();
  updatePagination();
});
