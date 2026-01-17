document.addEventListener('DOMContentLoaded', () => {
    const apiUrl = 'api.php?action=get_dashboard_stats';

    // DOM Elements
    const totalProductsEl = document.getElementById('total-products');
    const totalQuantityEl = document.getElementById('total-quantity');
    const totalValueEl = document.getElementById('total-value');
    const recentItemsList = document.getElementById('recent-items-list');
    const quantityChartCanvas = document.getElementById('quantity-chart').getContext('2d');
    
    let quantityChart;

    /**
     * Fetches dashboard data and updates all components
     */
    function fetchDashboardData() {
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                updateStatCards(data);
                updateRecentItems(data.recent_items);
                renderQuantityChart(data.chart_data);
            })
            .catch(error => console.error('Error fetching dashboard data:', error));
    }

    /**
     * Updates the three main statistic cards
     * @param {object} data The dashboard statistics object
     */
    function updateStatCards(data) {
        totalProductsEl.textContent = data.total_products || 0;
        totalQuantityEl.textContent = data.total_quantity || 0;
        totalValueEl.textContent = `$${parseFloat(data.total_value || 0).toFixed(2)}`;
    }

    /**
     * Updates the list of recently added items
     * @param {Array} recentItems An array of recent item objects
     */
    function updateRecentItems(recentItems) {
        recentItemsList.innerHTML = '';
        if (recentItems && recentItems.length > 0) {
            recentItems.forEach(item => {
                const li = document.createElement('li');
                const itemDate = new Date(item.created_at).toLocaleDateString();
                li.innerHTML = `<span>${escapeHTML(item.name)}</span> <span class="date">${itemDate}</span>`;
                recentItemsList.appendChild(li);
            });
        } else {
            recentItemsList.innerHTML = '<li>No recent items found.</li>';
        }
    }

    /**
     * Renders or updates the product quantity bar chart
     * @param {Array} chartData An array of items with name and quantity properties
     */
    function renderQuantityChart(chartData) {
        if (!chartData || chartData.length === 0) {
            document.querySelector('.chart-container').innerHTML += '<p>No data to display in chart.</p>';
            return;
        };

        const labels = chartData.map(item => item.name);
        const data = chartData.map(item => item.quantity);

        if (quantityChart) {
            quantityChart.destroy();
        }

        quantityChart = new Chart(quantityChartCanvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Quantity in Stock',
                    data: data,
                    backgroundColor: 'rgba(74, 144, 226, 0.7)',
                    borderColor: 'rgba(74, 144, 226, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 10
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    /**
     * Simple HTML sanitizer to prevent XSS
     * @param {string} str The string to escape
     * @returns {string} The escaped string
     */
    function escapeHTML(str) {
        const p = document.createElement('p');
        p.appendChild(document.createTextNode(str));
        return p.innerHTML;
    }

    // Initial data fetch
    fetchDashboardData();
});
