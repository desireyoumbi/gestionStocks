"use strict";

import { mockDb } from "./mockDb.js";

// Ensure DB is initialized
mockDb.init();

export const getDashboardData = () => {
    const products = mockDb.getProducts();
    const lowStock = products.filter(p => p.stock <= p.minStock).length;
    
    const todayStr = new Date().toISOString().split('T')[0];
    const movements = mockDb.getStockMovements();
    
    // Ventes today based on out movements or standard count
    const salesToday = movements.filter(m => m.type === "out" && m.date.startsWith(todayStr)).reduce((sum, m) => sum + m.qty, 0) + 28;
    
    // Extra revenue
    const extraRevenue = movements
        .filter(m => m.type === "out" && m.date.startsWith(todayStr))
        .reduce((sum, m) => {
            const prod = mockDb.getProductById(m.productId);
            return sum + (prod ? prod.price * m.qty : 0);
        }, 0);
    
    const revenue = 4850000 + extraRevenue;

    return {
        products: products.length,
        salesToday,
        revenue,
        lowStock
    };
};

const selectors = {
    shell: "#dashboardShell",
    sidebar: "#sidebar",
    backdrop: "#sidebarBackdrop",
    menuToggle: "#menuToggle",
    currentDate: "#currentDate",
    counters: ".counter",
    navLinks: ".sidebar-nav .nav-link"
};

const currencyFormatter = new Intl.NumberFormat("fr-FR", {
    style: "currency",
    currency: "XAF",
    maximumFractionDigits: 0
});

const numberFormatter = new Intl.NumberFormat("fr-FR");

const getElement = (selector) => document.querySelector(selector);
const getElements = (selector) => [...document.querySelectorAll(selector)];

const elements = {
    shell: getElement(selectors.shell),
    sidebar: getElement(selectors.sidebar),
    backdrop: getElement(selectors.backdrop),
    menuToggle: getElement(selectors.menuToggle),
    currentDate: getElement(selectors.currentDate)
};

const setCurrentDate = () => {
    if (!elements.currentDate) return;
    const date = new Intl.DateTimeFormat("fr-FR", {
        weekday: "long",
        day: "2-digit",
        month: "long",
        year: "numeric"
    }).format(new Date());

    elements.currentDate.textContent = date.charAt(0).toUpperCase() + date.slice(1);
};

const formatCounterValue = (value, isCurrency) => (
    isCurrency ? currencyFormatter.format(value) : numberFormatter.format(value)
);

const animateCounters = () => {
    const counters = getElements(selectors.counters);
    const shouldReduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const data = getDashboardData();

    counters.forEach((counter) => {
        // Set dynamic dataset values from mockDb
        const cardTitle = counter.parentElement.querySelector("h3")?.textContent.trim();
        if (cardTitle === "Produits") {
            counter.dataset.count = data.products;
        } else if (cardTitle === "Stock faible") {
            counter.dataset.count = data.lowStock;
        } else if (cardTitle === "Ventes") {
            counter.dataset.count = data.salesToday;
        } else if (cardTitle === "Chiffre d'affaires") {
            counter.dataset.count = data.revenue;
        }

        const target = Number(counter.dataset.count || 0);
        const isCurrency = counter.dataset.currency === "true";

        if (shouldReduceMotion) {
            counter.textContent = formatCounterValue(target, isCurrency);
            return;
        }

        const duration = 900;
        const startTime = performance.now();

        const tick = (now) => {
            const progress = Math.min((now - startTime) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const value = Math.round(target * eased);

            counter.textContent = formatCounterValue(value, isCurrency);

            if (progress < 1) {
                window.requestAnimationFrame(tick);
            }
        };

        window.requestAnimationFrame(tick);
    });
};

const renderLowStockTable = () => {
    const tableContainer = document.querySelector(".data-layout__main-column .table-panel:first-child");
    if (!tableContainer) return;
    
    const tbody = tableContainer.querySelector("tbody");
    if (!tbody) return;

    const lowStockProducts = mockDb.getProducts().filter(p => p.stock <= p.minStock);

    if (lowStockProducts.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding: 24px; color: var(--muted);">Aucun produit en stock faible.</td></tr>`;
        return;
    }

    tbody.innerHTML = lowStockProducts.map(p => {
        const badgeClass = p.stock === 0 ? "is-danger" : "is-warning";
        const badgeText = p.stock === 0 ? "Critique" : "Faible";
        return `
            <tr>
                <td>${p.name}</td>
                <td>${p.categoryName}</td>
                <td>${p.stock}</td>
                <td>${p.minStock}</td>
                <td><span class="badge ${badgeClass}">${badgeText}</span></td>
            </tr>
        `;
    }).join("");
};

const renderTimeline = () => {
    const ol = document.querySelector(".timeline");
    if (!ol) return;

    const history = mockDb.getHistory().slice(0, 5);

    if (history.length === 0) {
        ol.innerHTML = `<li style="padding: 10px 0; color: var(--muted);">Aucune activité récente.</li>`;
        return;
    }

    ol.innerHTML = history.map(h => {
        let icon = "fa-clock";
        if (h.module === "Produits") icon = "fa-box";
        if (h.module === "Stock") icon = "fa-inbox";
        if (h.module === "Ventes") icon = "fa-cart-shopping";
        if (h.module === "Utilisateurs") icon = "fa-user-gear";
        if (h.module === "Livraisons") icon = "fa-truck";

        // Extract time
        let timeStr = h.time;
        try {
            const dateObj = new Date(h.time);
            if (!isNaN(dateObj.getTime())) {
                timeStr = dateObj.toLocaleTimeString("fr-FR", { hour: "2-digit", minute: "2-digit" });
            }
        } catch (e) {}

        return `
            <li>
                <span><i class="fa-solid ${icon}"></i></span>
                <div>
                    <time>${timeStr}</time>
                    <p>${h.action}</p>
                </div>
            </li>
        `;
    }).join("");
};

const initDashboard = () => {
    // TODO PHP: Récupérer les données depuis la base de données réelle
    // TODO API: Charger les statistiques de l'entrepôt via API rest
    // TODO SQL: SELECT COUNT(*), SUM(prix * stock) FROM produits...

    setCurrentDate();
    animateCounters();
    renderLowStockTable();
    renderTimeline();
};

document.addEventListener("DOMContentLoaded", initDashboard);
