/* E-Stockly Stock JS Module */

"use strict";

import { mockDb } from "./mockDb.js";
import { showToast, initModal } from "./components/components.js";

// Ensure mockDb is initialized
mockDb.init();

// State management
let state = {
    products: [],
    categories: [],
    currentPage: 1,
    pageSize: 10,
    searchQuery: "",
    selectedCategory: "",
    selectedStatus: ""
};

let adjustModalHandler;

const elements = {
    tableBody: document.querySelector("#stockTable tbody"),
    metricTotalQty: document.getElementById("metricTotalStockQty"),
    metricValue: document.getElementById("metricStockValue"),
    metricAlertCount: document.getElementById("metricAlertCount"),
    metricOutCount: document.getElementById("metricOutCount"),
    search: document.getElementById("stockSearch"),
    filterCat: document.getElementById("filterCategory"),
    filterStat: document.getElementById("filterStatus"),
    prevBtn: document.getElementById("prevPageBtn"),
    nextBtn: document.getElementById("nextPageBtn"),
    pageStart: document.getElementById("pageStart"),
    pageEnd: document.getElementById("pageEnd"),
    totalCount: document.getElementById("totalCount"),
    form: document.getElementById("adjustForm"),
    productSelect: document.getElementById("adjProduct")
};

const currencyFormatter = new Intl.NumberFormat("fr-FR", {
    style: "currency",
    currency: "XOF",
    maximumFractionDigits: 0
});

const formatPrice = (value) => {
    return currencyFormatter.format(value).replace("XOF", "FCFA").replace("F CFA", "FCFA");
};

const initSelectors = () => {
    state.categories = mockDb.getCategories();
    
    // Populate categories filter
    const categoryOptions = state.categories.map(c => `<option value="${c.id}">${c.name}</option>`).join("");
    elements.filterCat.innerHTML = '<option value="">Toutes les catégories</option>' + categoryOptions;

    // Populate modal products list
    populateProductsSelect();
};

const populateProductsSelect = () => {
    const products = mockDb.getProducts();
    elements.productSelect.innerHTML = '<option value="">Choisir un produit</option>' +
        products.map(p => `<option value="${p.id}">${p.name} (Stock actuel: ${p.stock})</option>`).join("");
};

const updateMetrics = () => {
    const products = mockDb.getProducts();
    
    const totalQty = products.reduce((sum, p) => sum + p.stock, 0);
    const totalVal = products.reduce((sum, p) => sum + (p.purchasePrice * p.stock), 0);
    const alertCount = products.filter(p => p.stock > 0 && p.stock <= p.minStock).length;
    const outCount = products.filter(p => p.stock === 0).length;

    elements.metricTotalQty.textContent = totalQty;
    elements.metricValue.textContent = formatPrice(totalVal);
    elements.metricAlertCount.textContent = alertCount;
    elements.metricOutCount.textContent = outCount;
};

const loadStock = () => {
    let filtered = mockDb.getProducts();

    // Search query
    if (state.searchQuery) {
        const query = state.searchQuery.toLowerCase();
        filtered = filtered.filter(p => 
            p.name.toLowerCase().includes(query) || 
            p.categoryName.toLowerCase().includes(query)
        );
    }

    // Category
    if (state.selectedCategory) {
        filtered = filtered.filter(p => p.categoryId === Number(state.selectedCategory));
    }

    // Alert Status
    if (state.selectedStatus) {
        filtered = filtered.filter(p => {
            if (state.selectedStatus === "danger") return p.stock === 0;
            if (state.selectedStatus === "warning") return p.stock > 0 && p.stock <= p.minStock;
            if (state.selectedStatus === "normal") return p.stock > p.minStock;
            return true;
        });
    }

    state.products = filtered;
    renderTable();
};

const renderTable = () => {
    const total = state.products.length;

    if (total === 0) {
        elements.tableBody.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <span class="empty-state-icon"><i class="fa-solid fa-boxes-stacked"></i></span>
                        <h3>Aucun article trouvé</h3>
                        <p>Essayez de modifier vos filtres ou d'ajouter un produit.</p>
                    </div>
                </td>
            </tr>
        `;
        elements.pageStart.textContent = "0";
        elements.pageEnd.textContent = "0";
        elements.totalCount.textContent = "0";
        elements.prevBtn.disabled = true;
        elements.nextBtn.disabled = true;
        return;
    }

    const pageCount = Math.ceil(total / state.pageSize);
    if (state.currentPage > pageCount) state.currentPage = pageCount || 1;

    const start = (state.currentPage - 1) * state.pageSize;
    const end = Math.min(start + state.pageSize, total);
    
    const paginated = state.products.slice(start, end);

    elements.tableBody.innerHTML = paginated.map(p => {
        let badgeClass = "is-success";
        let badgeText = "Normal";

        if (p.stock === 0) {
            badgeClass = "is-danger";
            badgeText = "Rupture";
        } else if (p.stock <= p.minStock / 2) {
            badgeClass = "is-danger";
            badgeText = "Critique";
        } else if (p.stock <= p.minStock) {
            badgeClass = "is-warning";
            badgeText = "Faible";
        }

        return `
            <tr>
                <td><strong>#${p.id}</strong></td>
                <td><strong style="color: var(--text);">${p.name}</strong></td>
                <td>${p.categoryName}</td>
                <td><strong style="color: var(--text);">${p.stock}</strong></td>
                <td>${p.minStock}</td>
                <td><span class="badge ${badgeClass}">${badgeText}</span></td>
                <td>
                    <div class="table-actions-cell" style="justify-content: flex-end;">
                        <button class="action-btn adjust-single-btn" data-id="${p.id}" type="button" aria-label="Ajuster"><i class="fa-solid fa-sliders"></i></button>
                    </div>
                </td>
            </tr>
        `;
    }).join("");

    elements.pageStart.textContent = start + 1;
    elements.pageEnd.textContent = end;
    elements.totalCount.textContent = total;

    elements.prevBtn.disabled = state.currentPage === 1;
    elements.nextBtn.disabled = state.currentPage === pageCount;
    
    bindRowButtons();
};

const bindRowButtons = () => {
    document.querySelectorAll(".adjust-single-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = Number(btn.dataset.id);
            elements.productSelect.value = id;
            adjustModalHandler.open();
        });
    });
};

const handleFormSubmit = (e) => {
    e.preventDefault();

    // TODO PHP: Envoyer l'ajustement vers api/stock.php
    // TODO SQL: INSERT INTO mouvements_stock, UPDATE produits

    const formData = new FormData(elements.form);
    const adjustData = {
        productId: formData.get("productId"),
        type: formData.get("type"),
        qty: formData.get("qty"),
        comment: formData.get("comment")
    };

    // Validation
    let isValid = true;
    document.querySelectorAll(".form-field").forEach(f => f.classList.remove("is-invalid"));
    document.querySelectorAll(".field-error").forEach(e => e.textContent = "");

    if (!adjustData.productId) {
        const f = document.getElementById("adjProduct").closest(".form-field");
        f.classList.add("is-invalid");
        f.querySelector(".field-error").textContent = "Veuillez sélectionner un produit.";
        isValid = false;
    }
    if (isNaN(adjustData.qty) || Number(adjustData.qty) <= 0) {
        const f = document.getElementById("adjQty").closest(".form-field");
        f.classList.add("is-invalid");
        f.querySelector(".field-error").textContent = "Veuillez saisir une quantité positive.";
        isValid = false;
    }
    if (!adjustData.comment.trim()) {
        const f = document.getElementById("adjComment").closest(".form-field");
        f.classList.add("is-invalid");
        f.querySelector(".field-error").textContent = "Le motif est requis.";
        isValid = false;
    }

    if (!isValid) {
        showToast("Veuillez corriger le formulaire.", "warning");
        return;
    }

    try {
        mockDb.adjustStock(
            adjustData.productId,
            adjustData.type,
            adjustData.qty,
            adjustData.comment,
            "Administrateur"
        );
        showToast("Le stock a été ajusté avec succès !", "success");
        adjustModalHandler.close();
        
        // Refresh
        populateProductsSelect();
        updateMetrics();
        loadStock();
    } catch (err) {
        showToast(err.message || "Erreur lors de l'ajustement.", "danger");
    }
};

const bindEvents = () => {
    // Search
    elements.search.addEventListener("input", (e) => {
        state.searchQuery = e.target.value;
        state.currentPage = 1;
        loadStock();
    });

    // Category filter
    elements.filterCat.addEventListener("change", (e) => {
        state.selectedCategory = e.target.value;
        state.currentPage = 1;
        loadStock();
    });

    // Status filter
    elements.filterStat.addEventListener("change", (e) => {
        state.selectedStatus = e.target.value;
        state.currentPage = 1;
        loadStock();
    });

    // Pagination
    elements.prevBtn.addEventListener("click", () => {
        if (state.currentPage > 1) {
            state.currentPage--;
            renderTable();
        }
    });

    elements.nextBtn.addEventListener("click", () => {
        const pageCount = Math.ceil(state.products.length / state.pageSize);
        if (state.currentPage < pageCount) {
            state.currentPage++;
            renderTable();
        }
    });

    // Modals
    adjustModalHandler = initModal("adjustModal", "adjustStockBtn");

    const addBtn = document.getElementById("adjustStockBtn");
    if (addBtn) {
        addBtn.addEventListener("click", () => {
            elements.form.reset();
        });
    }

    elements.form.addEventListener("submit", handleFormSubmit);
};

const init = () => {
    initSelectors();
    updateMetrics();
    loadStock();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
