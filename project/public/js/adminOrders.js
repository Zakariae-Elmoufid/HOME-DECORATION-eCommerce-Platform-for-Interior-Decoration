document.addEventListener('DOMContentLoaded', function() {
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
    
    // async function showOrderDetails(orderId){
    //     const response = await fetch(`/admin/orders/details?id=${orderId}`,{
    //         method :"GET",
    //         headers: {
    //             "Content-Type": "application/json",
    //         },
    //     });
    
    //     const result = await response.json();
    //     console.log("Response text:", result);
    // }

         
     const viewDetailsBtns = document.querySelectorAll('button[title="View Details"]');
     const orderDetailsModal = document.getElementById('order-details-modal');
   viewDetailsBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const tr = this.closest('tr');
      const orderId = btn.dataset.orderId;
      showOrderDetails(orderId);
      orderDetailsModal.classList.remove('hidden');

    });
  });
  const printOrderBtn = document.getElementById('print-order-btn');
  const printInvoice  = document.querySelectorAll('button[title="Print Invoice"]');


  function showOrderDetails(orderId) {
    
    fetch(`/admin/orders/details?id=${orderId}`)
      .then(response => response.json())
      .then(data => {

        document.getElementById('modal-order-id').textContent = `#${data.order.id}`;
        document.getElementById('modal-customer-name').textContent = data.order.username;
        document.getElementById('modal-customer-email').textContent = data.order.email;
        document.getElementById('modal-order-date').textContent = new Date(data.order.created_at).toLocaleString();
        document.getElementById('modal-order-status').textContent = data.order.status;
        document.getElementById('modal-order-total').textContent = `$${parseFloat(data.order.totalAmount).toFixed(2)}`;
        
        const itemsContainer = document.getElementById('modal-order-items');
        itemsContainer.innerHTML = '';
        data.order.items.forEach(item => {
          const row = document.createElement('tr');
          
          const productCell = document.createElement('td');
          productCell.className = 'px-4 py-4 whitespace-nowrap';
          productCell.innerHTML = `
            <div class="flex items-center">
              <div class="flex-shrink-0 h-10 w-10">
                <img class="h-10 w-10 rounded-lg object-cover" src="/public/uploads/${item.productImage}" alt="${item.productTitle}" />
              </div>
              <div class="ml-4">
                <div class="text-sm font-medium text-gray-900">${item.productTitle}</div>
              </div>
            </div>
          `;
          
          const detailsCell = document.createElement('td');
          detailsCell.className = 'px-4 py-4 whitespace-nowrap';
          let detailsText = '';
          if (item.selectedColor) detailsText += `Color: ${item.selectedColor}`;
          if (item.selectedSize) {
            if (detailsText) detailsText += '<br>';
            detailsText += `Size: ${item.selectedSize}`;
          }
          detailsCell.innerHTML = `<div class="text-sm text-gray-500">${detailsText || 'N/A'}</div>`;
          
          const priceCell = document.createElement('td');
          priceCell.className = 'px-4 py-4 whitespace-nowrap';
          priceCell.innerHTML = `<div class="text-sm text-gray-900">$${parseFloat(item.price).toFixed(2)}</div>`;
          
          const qtyCell = document.createElement('td');
          qtyCell.className = 'px-4 py-4 whitespace-nowrap';
          qtyCell.innerHTML = `<div class="text-sm text-gray-900">${item.quantity}</div>`;
          
          const totalCell = document.createElement('td');
          totalCell.className = 'px-4 py-4 whitespace-nowrap';
          totalCell.innerHTML = `<div class="text-sm font-medium text-gray-900">$${parseFloat(item.total_item).toFixed(2)}</div>`;
          
          row.appendChild(productCell);
          row.appendChild(detailsCell);
          row.appendChild(priceCell);
          row.appendChild(qtyCell);
          row.appendChild(totalCell);
          
          itemsContainer.appendChild(row);
        });
        
        document.getElementById('modal-subtotal').textContent = `$${parseFloat(data.order.subTotal  ).toFixed(2)}`;
        document.getElementById('modal-shipping').textContent = `$${parseFloat(data.order.shipping || 0).toFixed(2)}`;
        document.getElementById('modal-grand-total').textContent = `$${parseFloat(data.order.totalAmount).toFixed(2)}`;
        
        printOrderBtn.setAttribute('data-order-id', orderId);
        
      })
    //   .catch(error => {
    //     console.error('Error fetching order details:', error);
    //     alert('Failed to load order details. Please try again.');
    //   });
  }
   
  const closeOrderDetailsModal = document.getElementById('close-order-details-modal');
  if (closeOrderDetailsModal) {
    closeOrderDetailsModal.addEventListener('click', closeModal);
  }
  const closeModalBtn = document.getElementById('close-modal-btn')
  if (closeModalBtn) {
    closeModalBtn.addEventListener('click', closeModal);
  }

  function closeModal() {
    if (orderDetailsModal) {
      orderDetailsModal.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }
  }




if (printOrderBtn) {
  printOrderBtn.addEventListener('click', function() {
    printOrder();
  });
}
if(printInvoice){
    printInvoice.forEach((btn) => {
      btn.addEventListener('click', function() {
       const  orderId =  btn.dataset.orderId;
        showOrderDetails(orderId);
        orderDetailsModal.classList.add('hidden');
        printOrder();
      });
    })
}

function printOrder() {
    const printWindow = window.open('', '_blank', 'height=600,width=800');
    
    // Get order data from the modal
    const orderNumber = document.getElementById('modal-order-id').textContent;
    const customerName = document.getElementById('modal-customer-name').textContent;
    const customerEmail = document.getElementById('modal-customer-email').textContent;
    const orderDate = document.getElementById('modal-order-date').textContent;
    const orderStatus = document.getElementById('modal-order-status').textContent;
    const orderTotal = document.getElementById('modal-grand-total').textContent;
    
    // Get items from the table
    const itemsTable = document.getElementById('modal-order-items');
    const itemRows = itemsTable.querySelectorAll('tr');
    
    // Format current date for invoice
    const today = new Date();
    const formattedDate = today.toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
    
    // Create HTML content for the print window with improved styling
    let printContent = `
      <!DOCTYPE html>
      <html>
      <head>
        <title>Invoice ${orderNumber}</title>
        <style>
          body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px;
            color: #333;
            line-height: 1.6;
          }
          .header { 
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px; 
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
          }
          .company-info { 
            text-align: left;
          }
          .company-info h1 {
            margin: 0;
            color: #2563eb;
          }
          .company-info p {
            margin: 5px 0;
            color: #666;
          }
          .invoice-details {
            text-align: right;
          }
          .invoice-details h2 {
            margin: 0;
            color: #2563eb;
          }
          .invoice-details p {
            margin: 5px 0;
          }
          .customer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
          }
          .bill-to {
            width: 50%;
          }
          .order-summary {
            width: 50%;
            text-align: right;
          }
          h3 {
            color: #2563eb;
            margin-bottom: 10px;
          }
          table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 30px; 
          }
          th, td { 
            padding: 12px 8px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
          }
          th { 
            background-color: #f8fafc;
            font-weight: 600;
          }
          .totals { 
            margin-top: 30px; 
            text-align: right; 
          }
          .totals div { 
            margin-bottom: 8px; 
          }
          .grand-total {
            font-size: 18px; 
            font-weight: bold; 
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
          }
          .footer { 
            margin-top: 50px; 
            text-align: center; 
            font-size: 14px;
            color: #666;
            padding-top: 20px;
            border-top: 1px solid #eee;
          }
          .thank-you {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2563eb;
          }
          @media print {
            .no-print { display: none; }
            body { padding: 0; }
            button { display: none; }
          }
          .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
          }
          .action-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
          }
          .print-btn {
            background-color: #2563eb;
            color: white;
          }
          .close-btn {
            background-color: #e5e7eb;
            color: #374151;
          }
        </style>
      </head>
      <body>
        <div class="header">
          <div class="company-info">
            <h1>ElegantHome</h1>
            <p>123 Elegant Street</p>
            <p>Beautiful City, BC 12345</p>
            <p>Phone: (555) 123-4567</p>
            <p>Email: support@ElegantHome.com</p>
          </div>
          <div class="invoice-details">
            <h2>INVOICE</h2>
            <p><strong>Invoice #:</strong> INV-${orderNumber.replace('#', '')}</p>
            <p><strong>Date:</strong> ${formattedDate}</p>
            <p><strong>Order Status:</strong> ${orderStatus}</p>
          </div>
        </div>
        
        <div class="customer-info">
          <div class="bill-to">
            <h3>BILL TO:</h3>
            <p><strong>${customerName}</strong></p>
            <p>${customerEmail}</p>
          </div>
          <div class="order-summary">
            <h3>ORDER SUMMARY:</h3>
            <p><strong>Order Number:</strong> ${orderNumber}</p>
            <p><strong>Order Date:</strong> ${orderDate}</p>
          </div>
        </div>
        
        <h3>ORDERED ITEMS</h3>
        <table>
          <thead>
            <tr>
              <th>Product</th>
              <th>Details</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
    `;
    
    // Add items to the print content
    itemRows.forEach(row => {
      try {
        const columns = row.querySelectorAll('td');
        const productName = columns[0].querySelector('.text-sm.font-medium').textContent;
        const details = columns[1].querySelector('.text-sm').innerHTML;
        const price = columns[2].querySelector('.text-sm').textContent;
        const quantity = columns[3].querySelector('.text-sm').textContent;
        const totalItem = columns[4].querySelector('.text-sm').textContent;
        
        printContent += `
          <tr>
            <td>${productName}</td>
            <td>${details}</td>
            <td>${price}</td>
            <td>${quantity}</td>
            <td>${totalItem}</td>
          </tr>
        `;
      } catch (error) {
        console.error('Error processing row:', error);
      }
    });
    
    // Add totals and footer
    const subtotal = document.getElementById('modal-subtotal').textContent;
    const shipping = document.getElementById('modal-shipping').textContent;
    
    printContent += `
          </tbody>
        </table>
        
        <div class="totals">
          <div><strong>Subtotal:</strong> ${subtotal}</div>
          <div><strong>Shipping:</strong> ${shipping}</div>
          <div class="grand-total">
            <strong>Total Amount:</strong> ${orderTotal}
          </div>
        </div>
        
        <div class="footer">
          <p class="thank-you">Thank you for your business!</p>
          <p>If you have any questions about this invoice, please contact</p>
          <p>our customer support at support@ElegantHome.com or call us at (555) 123-4567</p>
        </div>
        
        <div class="no-print action-buttons">
          <button class="print-btn" onclick="window.print()">Print Invoice</button>
          <button class="close-btn" onclick="window.close()">Close</button>
        </div>
      </body>
      </html>
    `;
    
    // Write the content to the new window and print
    printWindow.document.open();
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // Focus the new window
    printWindow.focus();
  }


    
  });