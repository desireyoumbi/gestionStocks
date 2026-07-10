"use strict";

import { mockDb } from "./mockDb.js";
import { getDashboardData } from "./dashboard.js";

const chartColors = {
    primary: "#6d5dfc",
    secondary: "#0f6fff",
    accent: "#ff9f1c",
    success: "#039855",
    danger: "#d92d20",
    muted: "#98a2b3",
    grid: "rgba(23, 32, 51, 0.08)"
};

const labels = ["Jan", "Fev", "Mar", "Avr", "Mai", "Juin", "Juil"];

const defaultOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: {
                usePointStyle: true,
                boxWidth: 8,
                color: "#67718a",
                font: {
                    family: "Inter, system-ui, sans-serif"
                }
            }
        },
        tooltip: {
            backgroundColor: "#172033",
            padding: 12,
            cornerRadius: 12
        }
    },
    scales: {
        x: {
            grid: {
                display: false
            },
            ticks: {
                color: "#67718a"
            }
        },
        y: {
            beginAtZero: true,
            grid: {
                color: chartColors.grid
            },
            ticks: {
                color: "#67718a"
            }
        }
    }
};

const createGradient = (ctx, colorStart, colorEnd) => {
    const gradient = ctx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, colorStart);
    gradient.addColorStop(1, colorEnd);
    return gradient;
};

const getCanvasContext = (id) => {
    const canvas = document.getElementById(id);
    return canvas ? canvas.getContext("2d") : null;
};

const renderMonthlySalesChart = () => {
    const ctx = getCanvasContext("monthlySalesChart");

    if (!ctx) {
        return;
    }

    const data = getDashboardData();

    new window.Chart(ctx, {
        type: "bar",
        data: {
            labels,
            datasets: [{
                label: "Ventes",
                data: [120, 180, 210, 260, 240, 315, data.salesToday],
                borderRadius: 10,
                backgroundColor: createGradient(ctx, "rgba(109, 93, 252, 0.92)", "rgba(15, 111, 255, 0.48)")
            }]
        },
        options: defaultOptions
    });
};

const renderRevenueChart = () => {
    const ctx = getCanvasContext("revenueChart");

    if (!ctx) {
        return;
    }

    const data = getDashboardData();

    new window.Chart(ctx, {
        type: "line",
        data: {
            labels,
            datasets: [{
                label: "Revenu",
                data: [2800000, 3200000, 3500000, 3900000, 4200000, 4480000, data.revenue],
                borderColor: chartColors.primary,
                backgroundColor: "rgba(109, 93, 252, 0.14)",
                pointBackgroundColor: chartColors.primary,
                pointRadius: 4,
                fill: true,
                tension: 0.42
            }]
        },
        options: defaultOptions
    });
};

const renderProductsChart = () => {
    const ctx = getCanvasContext("productsChart");

    if (!ctx) {
        return;
    }

    // Group items dynamically from local DB
    const products = mockDb.getProducts();
    const categories = mockDb.getCategories();

    const categoryMap = {};
    categories.forEach(c => {
        categoryMap[c.name] = 0;
    });

    products.forEach(p => {
        if (categoryMap[p.categoryName] !== undefined) {
            categoryMap[p.categoryName]++;
        } else {
            categoryMap[p.categoryName] = 1;
        }
    });

    const activeLabels = Object.keys(categoryMap).filter(k => categoryMap[k] > 0);
    const chartData = activeLabels.map(k => categoryMap[k]);

    new window.Chart(ctx, {
        type: "doughnut",
        data: {
            labels: activeLabels.length ? activeLabels : ["Aucun"],
            datasets: [{
                data: chartData.length ? chartData : [1],
                backgroundColor: [
                    chartColors.primary, 
                    chartColors.secondary, 
                    chartColors.accent, 
                    chartColors.success, 
                    chartColors.danger, 
                    chartColors.muted
                ],
                borderColor: "#ffffff",
                borderWidth: 4,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "68%",
            plugins: defaultOptions.plugins
        }
    });
};

const initCharts = () => {
    if (!window.Chart) {
        return;
    }

    // TODO PHP: Requêter les ventes mensuelles et revenus depuis la BDD SQL
    // TODO SQL: SELECT MONTH(date), SUM(total) FROM ventes GROUP BY MONTH(date)

    renderMonthlySalesChart();
    renderRevenueChart();
    renderProductsChart();
};

document.addEventListener("DOMContentLoaded", initCharts);
