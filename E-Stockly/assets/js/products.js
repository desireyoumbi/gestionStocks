/* E-Stockly Products JS Module */

"use strict";

import { mockDb } from "./mockDb.js";
import { showToast, initModal } from "./components/components.js";

// Ensure mockDb is initialized
mockDb.init();

// State management
let state = {
    products: [],
    categories: [],
    suppliers: [],
    currentPage: 1,
    pageSize: 10,
    searchQuery: "",
    selectedCategory: "",
    selectedStatus: "",
    productToDelete: null
};

// Modal helpers
let productModalHandler;
let deleteModalHandler;

const elements = {
    tableBody: document.querySelector("#productsTable tbody"),
    metricTotal: document.getElementById("metricTotalProducts"),
    metricValue: document.getElementById("metricStockValue"),
    metricLow: document.getElementById("metricLowStock"),
    metricOutOf: document.getElementById("metricOutOfStock"),
    search: document.getElementById("productSearch"),
    filterCat: document.getElementById("filterCategory"),
    filterStat: document.getElementById("filterStatus"),
    prevBtn: document.getElementById("prevPageBtn"),
    nextBtn: document.getElementById("nextPageBtn"),
    pageStart: document.getElementById("pageStart"),
    pageEnd: document.getElementById("pageEnd"),
    totalCount: document.getElementById("totalCount"),
    form: document.getElementById("productForm"),
    modalTitle: document.getElementById("modalTitle"),
    confirmDeleteBtn: document.getElementById("confirmDeleteBtn")
};

const currencyFormatter = new Intl.NumberFormat("fr-FR", {
    style: "currency",
    currency: "XOF",
    maximumFractionDigits: 0
});

// Format currency string (replaces symbol with FCFA)
const formatPrice = (value) => {
    return currencyFormatter.format(value).replace("XOF", "FCFA").replace("F CFA", "FCFA");
};

// Populate filters and form select inputs
const initSelectors = () => {
    state.categories = mockDb.getCategories();
    state.suppliers = mockDb.getSuppliers();

    // Populate category filters
    const categoryOptions = state.categories.map(c => `<option value="${c.id}">${c.name}</option>`).join("");
    elements.filterCat.innerHTML = '<option value="">Toutes les catégories</option>' + categoryOptions;

    // Populate form category select
    const formCatSelect = document.getElementById("pCategory");
    if (formCatSelect) {
        formCatSelect.innerHTML = '<option value="">Choisir une catégorie</option>' + categoryOptions;
    }

    // Populate form supplier select
    const formSupSelect = document.getElementById("pSupplier");
    if (formSupSelect) {
        formSupSelect.innerHTML = '<option value="">Choisir un fournisseur</option>' + 
            state.suppliers.map(s => `<option value="${s.id}">${s.name}</option>`).join("");
    }
};

// Calculate and render metric cards
const updateMetrics = () => {
    const products = mockDb.getProducts();
    const totalCount = products.length;
    
    const stockValue = products.reduce((sum, p) => sum + (p.purchasePrice * p.stock), 0);
    const lowStock = products.filter(p => p.stock > 0 && p.stock <= p.minStock).length;
    const outOfStock = products.filter(p => p.stock === 0).length;

    elements.metricTotal.textContent = totalCount;
    elements.metricValue.textContent = formatPrice(stockValue);
    elements.metricLow.textContent = lowStock;
    elements.metricOutOf.textContent = outOfStock;
};

// Filter, sort and slice data for display
const loadProducts = () => {
    let filtered = mockDb.getProducts();

    // Text search
    if (state.searchQuery) {
        const query = state.searchQuery.toLowerCase();
        filtered = filtered.filter(p => 
            p.name.toLowerCase().includes(query) || 
            (p.barcode && p.barcode.toLowerCase().includes(query))
        );
    }

    // Category filter
    if (state.selectedCategory) {
        filtered = filtered.filter(p => p.categoryId === Number(state.selectedCategory));
    }

    // Status filter
    if (state.selectedStatus) {
        filtered = filtered.filter(p => {
            if (state.selectedStatus === "empty") return p.stock === 0;
            if (state.selectedStatus === "critical") return p.stock > 0 && p.stock <= p.minStock / 2;
            if (state.selectedStatus === "low") return p.stock > 0 && p.stock <= p.minStock;
            if (state.selectedStatus === "normal") return p.stock > p.minStock;
            return true;
        });
    }

    state.products = filtered;
    renderTable();
};

// Render rows in the data table
const renderTable = () => {
    const total = state.products.length;
    
    if (total === 0) {
        elements.tableBody.innerHTML = `
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <span class="empty-state-icon"><i class="fa-solid fa-box-open"></i></span>
                        <h3>Aucun produit trouvé</h3>
                        <p>Essayez de modifier vos filtres ou d'ajouter un nouveau produit.</p>
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

    // Pagination calculations
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
                <td><strong>${p.barcode || "—"}</strong></td>
                <td>
                    <div style="font-weight: 700; color: var(--text);">${p.name}</div>
                </td>
                <td>${p.categoryName}</td>
                <td>${formatPrice(p.purchasePrice)}</td>
                <td>${formatPrice(p.price)}</td>
                <td><strong style="color: var(--text);">${p.stock}</strong> <small style="color: var(--muted);">/ ${p.minStock}</small></td>
                <td><span class="badge ${badgeClass}">${badgeText}</span></td>
                <td>
                    <div class="table-actions-cell" style="justify-content: flex-end;">
                        <button class="action-btn edit-product-btn" data-id="${p.id}" type="button" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
                        <button class="action-btn is-danger delete-product-btn" data-id="${p.id}" type="button" aria-label="Supprimer"><i class="fa-solid fa-trash"></i></button>
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

// Bind Pen and Trash actions on rows
const bindRowButtons = () => {
    // Edit buttons
    document.querySelectorAll(".edit-product-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = Number(btn.dataset.id);
            openEditForm(id);
        });
    });

    // Delete buttons
    document.querySelectorAll(".delete-product-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            state.productToDelete = Number(btn.dataset.id);
            deleteModalHandler.open();
        });
    });
};

// Form Validation
const validateForm = (data) => {
    let isValid = true;
    
    // Clear errors
    document.querySelectorAll(".form-field").forEach(f => f.classList.remove("is-invalid"));
    document.querySelectorAll(".field-error").forEach(e => e.textContent = "");

    const setError = (field, message) => {
        const fieldEl = document.getElementById(field).closest(".form-field");
        fieldEl.classList.add("is-invalid");
        const errorEl = fieldEl.querySelector(".field-error");
        if (errorEl) errorEl.textContent = message;
        isValid = false;
    };

    if (!data.name.trim()) setError("pName", "Le nom du produit est requis.");
    if (!data.categoryId) setError("pCategory", "Veuillez sélectionner une catégorie.");
    if (isNaN(data.purchasePrice) || data.purchasePrice < 0) setError("pPurchasePrice", "Prix d'achat invalide.");
    if (isNaN(data.price) || data.price < 0) setError("pPrice", "Prix de vente invalide.");
    if (Number(data.price) < Number(data.purchasePrice)) setError("pPrice", "Le prix de vente doit être supérieur ou égal au prix d'achat.");
    if (isNaN(data.stock) || data.stock < 0) setError("pStock", "Stock initial invalide.");
    if (isNaN(data.minStock) || data.minStock <= 0) setError("pMinStock", "Seuil de stock invalide.");
    if (!data.supplierId) setError("pSupplier", "Veuillez sélectionner un fournisseur.");

    return isValid;
};

// Open form in edit mode
const openEditForm = (id) => {
    const product = mockDb.getProductById(id);
    if (!product) return;

    elements.modalTitle.textContent = "Modifier le produit";
    document.getElementById("productId").value = product.id;
    document.getElementById("pName").value = product.name;
    document.getElementById("pCategory").value = product.categoryId;
    document.getElementById("pBarcode").value = product.barcode || "";
    document.getElementById("pPurchasePrice").value = product.purchasePrice;
    document.getElementById("pPrice").value = product.price;
    
    const stockField = document.getElementById("pStock");
    stockField.value = product.stock;
    
    document.getElementById("pMinStock").value = product.minStock;
    document.getElementById("pSupplier").value = product.supplierId;

    productModalHandler.open();
};

const handleFormSubmit = (e) => {
    e.preventDefault();

    // TODO PHP: Envoyer les données vers api/produits.php
    // TODO API: Valider les champs côté serveur
    // TODO SQL: INSERT INTO produits / UPDATE produits

    const formData = new FormData(elements.form);
    const productData = {
        id: formData.get("id") ? Number(formData.get("id")) : null,
        name: formData.get("name"),
        categoryId: formData.get("categoryId"),
        barcode: formData.get("barcode"),
        purchasePrice: formData.get("purchasePrice"),
        price: formData.get("price"),
        stock: formData.get("stock"),
        minStock: formData.get("minStock"),
        supplierId: formData.get("supplierId")
    };

    if (!validateForm(productData)) {
        showToast("Veuillez corriger les erreurs de saisie.", "warning");
        return;
    }

    try {
        mockDb.saveProduct(productData);
        showToast(productData.id ? "Produit mis à jour avec succès !" : "Produit ajouté avec succès !", "success");
        productModalHandler.close();
        loadProducts();
        updateMetrics();
    } catch (err) {
        showToast(err.message || "Erreur lors de l'enregistrement.", "danger");
    }
};

const handleConfirmDelete = () => {
    // TODO PHP: Requête de suppression vers api/produits.php
    // TODO SQL: DELETE FROM produits WHERE id = :id

    if (state.productToDelete) {
        const success = mockDb.deleteProduct(state.productToDelete);
        if (success) {
            showToast("Le produit a été supprimé.", "success");
            deleteModalHandler.close();
            state.productToDelete = null;
            loadProducts();
            updateMetrics();
        } else {
            showToast("Impossible de supprimer le produit.", "danger");
        }
    }
};

const bindEvents = () => {
    // Search
    elements.search.addEventListener("input", (e) => {
        state.searchQuery = e.target.value;
        state.currentPage = 1;
        loadProducts();
    });

    // Filters
    elements.filterCat.addEventListener("change", (e) => {
        state.selectedCategory = e.target.value;
        state.currentPage = 1;
        loadProducts();
    });

    elements.filterStat.addEventListener("change", (e) => {
        state.selectedStatus = e.target.value;
        state.currentPage = 1;
        loadProducts();
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

    // Form modal toggle
    productModalHandler = initModal("productModal", "addProductBtn");
    
    // Add product custom click reset
    const addBtn = document.getElementById("addProductBtn");
    if (addBtn) {
        addBtn.addEventListener("click", () => {
            elements.modalTitle.textContent = "Ajouter un produit";
            document.getElementById("productId").value = "";
            elements.form.reset();
        });
    }

    // Delete modal toggle
    deleteModalHandler = initModal("deleteModal");

    // Form submit
    elements.form.addEventListener("submit", handleFormSubmit);

    // Confirm delete
    elements.confirmDeleteBtn.addEventListener("click", handleConfirmDelete);
};

const init = () => {
    initSelectors();
    updateMetrics();
    loadProducts();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
