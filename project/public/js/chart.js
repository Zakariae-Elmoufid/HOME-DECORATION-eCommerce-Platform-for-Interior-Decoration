document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    const yearSelector = document.getElementById('yearSelector');
    
    let salesChart = null;
    
    async function loadSalesData(year = new Date().getFullYear()) {
        try {
            const url = `/admin/dashboard/sales-data?year=${year}` ;
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error('Failed to load sales data');
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error loading sales data:', error);
            return { data: Array(12).fill(0), years: [] };
        }   
    }
    
    // Function to populate the year selector dropdown
    function populateYearSelector(years, selectedYear) {
        yearSelector.innerHTML = '';
        
        years.forEach(year => {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            option.selected = year == selectedYear;
            yearSelector.appendChild(option);
        });
    }
    
    // Function to render or update the chart
    function renderChart(data) {

        const months = [
            'January', 'February', 'March', 'April', 'May', 'June', 
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        
        const chartData = {
            labels: months,
            datasets: [{
                label: 'Monthly Sales ($)',
                data: Object.values(data),
                backgroundColor: Array(12).fill('rgba(59, 130, 246, 0.7)'),
                borderColor: Array(12).fill('rgb(59, 130, 246)'),
                borderWidth: 1
            }]
        };
        
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        };
        
        // If chart already exists, update it
        if (salesChart) {
            salesChart.data = chartData;
            salesChart.options = chartOptions;
            salesChart.update();
        } else {
            // Create new chart
            salesChart = new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: chartOptions
            });
        }
    }
    
// Initial load
async function initialize() {
    const currentYear = new Date().getFullYear();
    const result = await loadSalesData(currentYear);
 
    if (result.years && result.years.length > 0) {
        
        const defaultYearIndex = result.years.indexOf(currentYear) !== -1 
        ? result.years.indexOf(currentYear) 
        : 0;

        populateYearSelector(result.years, result.years[defaultYearIndex]);
        renderChart(result.data);
        
        yearSelector.addEventListener('change', async function() {
            const selectedYear = this.value;
            const newData = await loadSalesData(selectedYear);
            renderChart(newData.data);
        });
    } else {
        renderChart(Array(12).fill(0));
    }
}
    
    initialize();

    

        const periodSelector = document.getElementById('popularProductsPeriod');
        const productsList = document.getElementById('popularProductsList');
        
        if (!periodSelector || !productsList) return;
        
        periodSelector.addEventListener('change', async function() {
            const period = this.value;
            
            try {
                productsList.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Loading...</div>';
                
                const response = await fetch(`/admin/dashboard/popular-products?period=${period}`);
                
                if (!response.ok) {
                    throw new Error('Failed to load products data');
                }
                
                const data = await response.json();
                console.log(data.products);   
                // Build HTML for products
                let html = '';
                
                if (data.products && data.products.length > 0) {
                    data.products.forEach(product => {
                        html += `
                            <div class="flex items-center">
                                <img src="/public/uploads/${product.image_path || 'placeholder.jpg'}" 
                                     alt="${product.name}" 
                                     class="w-12 h-12 object-cover rounded-md">
                                <div class="ml-4 flex-1">
                                    <div class="flex justify-between">
                                        <h4 class="text-sm font-medium text-navy">${product.title}</h4>
                                        <span class="text-sm font-semibold text-navy">$${product.base_price}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                        <div class="bg-blue h-2 rounded-full" style="width: ${product.percentage}%"></div>
                                    </div>
                                    <div class="flex justify-between mt-1">
                                        <span class="text-xs text-gray-500">${product.units_sold} sold</span>
                                        <span class="text-xs text-gray-500">${product.percentage}%</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html = '<div class="text-center py-4 text-gray-500">No product sales data available for this period</div>';
                }
                
                productsList.innerHTML = html;
            } catch (error) {
                console.error('Error loading popular products:', error);
                productsList.innerHTML = '<div class="text-center py-4 text-red-500">Failed to load products data</div>';
            }
        });








});