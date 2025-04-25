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
                            return '$' + value.toLocaleString();
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
            console.log(defaultYearIndex);
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
});