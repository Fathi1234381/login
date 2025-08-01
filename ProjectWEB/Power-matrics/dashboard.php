<?php
// Dashboard Power Metrics dengan Chart
// Version: 2.0
// Updated: <?php echo date('Y-m-d H:i:s'); ?>

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Power Metrics dengan Chart</title>
    
    <!-- External CSS -->
    <link rel="stylesheet" href="dashboardstyle.css">
    
    <!-- Chart.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    <div class="blur-container">
        <div class="power-metrics-title">Power Metrics</div>
        
        <div class="metrics-container">
            <!-- Voltage Card -->
            <div class="metric-card volt-card">
                <span class="volt-emoji">⚡</span>
                <div class="volt-number" id="voltValue">234</div>
                <div class="volt-label">VOLT</div>
            </div>
            
            <!-- Amperage Card -->
            <div class="metric-card amp-card">
                <span class="amp-emoji">🔌</span>
                <div class="amp-number" id="ampValue">8.5</div>
                <div class="amp-label">AMP</div>
            </div>
            
            <!-- Wattage Card -->
            <div class="metric-card watt-card">
                <span class="watt-emoji">💡</span>
                <div class="watt-number" id="wattValue">1,950</div>
                <div class="watt-label">WATT</div>
                <div class="status-badge" id="statusBadge">NORMAL</div>
            </div>
            
            <!-- kWh Card -->
            <div class="metric-card kwh-card">
                <span class="kwh-emoji">⏱️</span>
                <div class="kwh-number" id="kwhValue">872.5</div>
                <div class="kwh-label">kWh</div>
                <div class="today-info">
                    <span class="today-label">Today</span>
                    <span class="today-value" id="todayValue">4.2</span>
                </div>
            </div>
        </div>
        
        <!-- Chart History -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="chart-title">
                    <span>🕐</span>
                    History
                </div>
                <div class="chart-legend">
                    <div class="legend-item">
                        <div class="legend-dot legend-volt"></div>
                        <span>Volt</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot legend-current"></div>
                        <span>Current</span>
                    </div>
                </div>
            </div>
            
            <div class="chart-canvas-container">
                <canvas id="powerChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Dashboard JavaScript -->
    <script>
        // Global Variables
        let powerChart;
        let socket;
        let isConnected = false;
        let simulationInterval;

        // Configuration
        const CONFIG = {
            websocketUrl: 'ws://localhost:8080',
            simulationInterval: 2000,
            maxChartPoints: 20,
            baseVoltage: 230,
            baseCurrent: 8.5
        };

        /**
         * WebSocket Connection with Fallback to Simulation
         */
        function connectWebSocket() {
            try {
                socket = new WebSocket(CONFIG.websocketUrl);
                
                socket.onopen = () => {
                    console.log('✅ WebSocket connected to server');
                    isConnected = true;
                    stopSimulation();
                };

                socket.onmessage = (event) => {
                    try {
                        const data = JSON.parse(event.data);
                        updateDashboard(data);
                    } catch (error) {
                        console.error('❌ Error parsing WebSocket data:', error);
                    }
                };

                socket.onclose = () => {
                    console.log('⚠️ WebSocket connection closed');
                    isConnected = false;
                    startSimulation();
                };

                socket.onerror = (error) => {
                    console.log('❌ WebSocket server not available. Using simulation mode...');
                    isConnected = false;
                    startSimulation();
                };

            } catch (error) {
                console.log('❌ Cannot connect to WebSocket. Using simulation mode...');
                startSimulation();
            }
        }

        /**
         * Simulation Mode (Fallback when server is not available)
         */
        function startSimulation() {
            if (simulationInterval) return;

            console.log('🔄 Starting simulation mode...');
            
            simulationInterval = setInterval(() => {
                if (!isConnected) {
                    const voltage = CONFIG.baseVoltage + (Math.random() - 0.5) * 10;
                    const current = CONFIG.baseCurrent + (Math.random() - 0.5) * 2;
                    const power = voltage * current;
                    const kwh = 872.5 + (Math.random() * 0.1);
                    
                    const simulatedData = {
                        voltage: Math.round(voltage),
                        current: parseFloat(current.toFixed(1)),
                        power: Math.round(power),
                        kwh: parseFloat(kwh.toFixed(1)),
                        timestamp: new Date().toISOString()
                    };
                    
                    updateDashboard(simulatedData);
                }
            }, CONFIG.simulationInterval);
        }

        /**
         * Stop Simulation Mode
         */
        function stopSimulation() {
            if (simulationInterval) {
                clearInterval(simulationInterval);
                simulationInterval = null;
                console.log('⏹️ Simulation mode stopped');
            }
        }

        /**
         * Update Dashboard Values
         * @param {Object} data - Power metrics data
         */
        function updateDashboard(data) {
            // Update metric values
            updateElement('voltValue', data.voltage);
            updateElement('ampValue', data.current);
            updateElement('wattValue', data.power.toLocaleString());
            updateElement('kwhValue', data.kwh);
            
            // Update status badge
            updateStatusBadge(data.power);
            
            // Update chart
            updateChartRealtime(data);
        }

        /**
         * Update DOM Element safely
         * @param {string} elementId - Element ID
         * @param {string|number} value - New value
         */
        function updateElement(elementId, value) {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = value;
            }
        }

        /**
         * Update Status Badge based on power consumption
         * @param {number} power - Current power value
         */
        function updateStatusBadge(power) {
            const statusBadge = document.getElementById('statusBadge');
            if (!statusBadge) return;
            
            if (power > 2200) {
                statusBadge.textContent = 'HIGH';
                statusBadge.style.backgroundColor = 'rgba(239, 68, 68, 0.2)';
                statusBadge.style.borderColor = 'rgba(239, 68, 68, 0.4)';
                statusBadge.style.color = '#dc2626';
            } else if (power < 1500) {
                statusBadge.textContent = 'LOW';
                statusBadge.style.backgroundColor = 'rgba(245, 158, 11, 0.2)';
                statusBadge.style.borderColor = 'rgba(245, 158, 11, 0.4)';
                statusBadge.style.color = '#d97706';
            } else {
                statusBadge.textContent = 'NORMAL';
                statusBadge.style.backgroundColor = 'rgba(76, 175, 80, 0.2)';
                statusBadge.style.borderColor = 'rgba(76, 175, 80, 0.4)';
                statusBadge.style.color = '#2e7d32';
            }
        }

        /**
         * Update Chart with Real-time Data
         * @param {Object} data - Power metrics data
         */
        function updateChartRealtime(data) {
            if (!powerChart) return;
            
            const now = new Date().toLocaleTimeString();
            
            // Add new data point
            powerChart.data.labels.push(now);
            powerChart.data.datasets[0].data.push(data.voltage);
            powerChart.data.datasets[1].data.push(data.current);
            
            // Remove old data points (keep only last N points)
            if (powerChart.data.labels.length > CONFIG.maxChartPoints) {
                powerChart.data.labels.shift();
                powerChart.data.datasets[0].data.shift();
                powerChart.data.datasets[1].data.shift();
            }
            
            // Update chart without animation for smooth real-time updates
            powerChart.update('none');
        }

        /**
         * Initialize Power Chart
         */
        function initializePowerChart() {
            const ctx = document.getElementById('powerChart');
            if (!ctx) {
                console.error('❌ Canvas element not found');
                return;
            }

            const powerData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'],
                datasets: [
                    {
                        label: 'Volt',
                        data: [230, 231, 232, 229, 234, 235, 233, 234, 232, 231],
                        borderColor: '#dc2626',
                        backgroundColor: 'rgba(220, 38, 38, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'Ampere',
                        data: [8.3, 8.4, 8.5, 8.6, 8.4, 8.2, 8.5, 8.7, 8.6, 8.5],
                        borderColor: '#6b7280',
                        backgroundColor: 'rgba(107, 114, 128, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: false
                    }
                ]
            };

            powerChart = new Chart(ctx, {
                type: 'line',
                data: powerData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 0 // Disable animation for real-time updates
                    },
                    plugins: {
                        legend: {
                            display: false // We use custom legend
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(107, 114, 128, 0.2)',
                                borderColor: 'rgba(107, 114, 128, 0.3)'
                            },
                            ticks: {
                                color: '#4b5563',
                                maxTicksLimit: 10
                            }
                        },
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: 'rgba(107, 114, 128, 0.2)',
                                borderColor: 'rgba(107, 114, 128, 0.3)'
                            },
                            ticks: {
                                color: '#4b5563'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }

        /**
         * Initialize Dashboard
         */
        function initializeDashboard() {
            console.log('🚀 Initializing Power Metrics Dashboard...');
            
            // Initialize chart
            initializePowerChart();
            
            // Start WebSocket connection with fallback
            connectWebSocket();
            
            console.log('✅ Dashboard initialized successfully');
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', initializeDashboard);
        
        // Fallback for older browsers
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeDashboard);
        } else {
            initializeDashboard();
        }
    </script>
</body>
</html>