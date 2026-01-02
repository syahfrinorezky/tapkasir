document.addEventListener("alpine:init", () => {
    Alpine.data("reports", () => {
        let dailyChartInstance = null;
        let hourlyChartInstance = null;
        let categoryChartInstance = null;

        return {
            loaded: false,
            activeTab: 'summary',
            startDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
            endDate: new Date().toISOString().split('T')[0],

            data: {
                summary: {
                    total_sales: 0,
                    total_profit: 0,
                    total_transactions: 0,
                    total_items: 0
                },
                charts: {
                    daily: [],
                    hourly: [],
                    categories: []
                },
                tables: {
                    top_products: [],
                    cashiers: []
                }
            },

            async init() {
                await this.fetchData();
                this.loaded = true;
                
                this.updateTabIndicator();

                this.$watch('activeTab', (value) => {
                    this.updateTabIndicator();
                    
                    if (value === 'summary') {
                        setTimeout(() => {
                            this.renderDailyChart();
                            this.renderHourlyChart();
                        }, 100);
                    } else if (value === 'products') {
                        setTimeout(() => {
                            this.renderCategoryChart();
                        }, 100);
                    }
                });
                
                window.addEventListener('resize', () => this.updateTabIndicator());
            },

            updateTabIndicator() {
                this.$nextTick(() => {
                    const activeBtn = this.$refs['tab_' + this.activeTab];
                    const indicator = this.$refs.tabIndicator;
                    
                    if (activeBtn && indicator) {
                        indicator.style.width = activeBtn.offsetWidth + 'px';
                        indicator.style.left = activeBtn.offsetLeft + 'px';
                        indicator.style.opacity = '1';
                    }
                });
            },

            async fetchData() {
                try {
                    const params = new URLSearchParams({
                        start_date: this.startDate,
                        end_date: this.endDate
                    });
                    const res = await fetch(`/admin/laporan/data?${params.toString()}`);
                    const json = await res.json();
                    this.data = json;

                    if (this.activeTab === 'summary') {
                        this.renderDailyChart();
                        this.renderHourlyChart();
                    } else if (this.activeTab === 'products') {
                        this.renderCategoryChart();
                    }
                } catch (err) {
                    console.error("Error fetching report data:", err);
                }
            },

            formatRupiah(v) {
                let val = parseFloat(v ?? 0);
                return "Rp " + (isNaN(val) ? 0 : val).toLocaleString("id-ID");
            },

            formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            },

            renderDailyChart() {
                if (dailyChartInstance) {
                    dailyChartInstance.destroy();
                    dailyChartInstance = null;
                }
                const ctx = document.getElementById('dailyChart');
                if (!ctx) return;

                const labels = this.data.charts.daily.map(d => this.formatDate(d.date));
                const revenue = this.data.charts.daily.map(d => d.revenue);
                const profit = this.data.charts.daily.map(d => d.profit);

                dailyChartInstance = new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Pendapatan',
                                data: revenue,
                                borderColor: '#6366f1',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Keuntungan (Profit)',
                                data: profit,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: (context) => {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (value) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumSignificantDigits: 3 }).format(value)
                                }
                            }
                        }
                    }
                });
            },

            renderHourlyChart() {
                if (hourlyChartInstance) {
                    hourlyChartInstance.destroy();
                    hourlyChartInstance = null;
                }
                const ctx = document.getElementById('hourlyChart');
                if (!ctx) return;

                const hours = Array.from({ length: 24 }, (_, i) => i);
                const salesData = new Array(24).fill(0);

                this.data.charts.hourly.forEach(item => {
                    const h = parseInt(item.hour);
                    if (h >= 0 && h < 24) salesData[h] = parseFloat(item.total);
                });

                hourlyChartInstance = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: hours.map(h => `${h}:00`),
                        datasets: [{
                            label: 'Penjualan per Jam',
                            data: salesData,
                            backgroundColor: '#f59e0b',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            },

            renderCategoryChart() {
                if (categoryChartInstance) {
                    categoryChartInstance.destroy();
                    categoryChartInstance = null;
                }
                const ctx = document.getElementById('categoryChart');
                if (!ctx) return;

                const labels = this.data.charts.categories.map(c => c.category_name || 'Lainnya');
                const data = this.data.charts.categories.map(c => c.total_sales);

                categoryChartInstance = new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: [
                                '#6366f1', '#ec4899', '#10b981', '#f59e0b', '#3b82f6',
                                '#8b5cf6', '#ef4444', '#14b8a6'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'right' }
                        }
                    }
                });
            }
        }
    });
});
