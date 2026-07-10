/* E-Stockly Categories JS Module */

"use strict";

import { mockDb } from "./mockDb.js";
import { showToast, initModal } from "./components/components.js";

// Ensure mockDb is initialized
mockDb.init();

// State management
let state = {
    categories: [],
    currentPage: 1,
    pageSize: 10,
    searchQuery: "",
    categoryToDelete: null
};

// Modal handlers
let categoryModalHandler;
let deleteModalHandler;

const elements = {
    tableBody: document.querySelector("#categoriesTable tbody"),
    metricTotal: document.getElementById("metricTotalCategories"),
    metricAssociated: document.getElementById("metricAssociatedProducts"),
    metricTop: document.getElementById("metricTopCategory"),
    search: document.getElementById("categorySearch"),
    prevBtn: document.getElementById("prevPageBtn"),
    nextBtn: document.getElementById("nextPageBtn"),
    pageStart: document.getElementById("pageStart"),
    pageEnd: document.getElementById("pageEnd"),
    totalCount: document.getElementById("totalCount"),
    form: document.getElementById("categoryForm"),
    modalTitle: document.getElementById("modalTitle"),
    confirmDeleteBtn: document.getElementById("confirmDeleteBtn")
};

const currencyFormatter = new Intl.NumberFormat("fr-FR", {
    style: "currency",
    currency: "XOF",
    maximumFractionDigits: 0
});

const formatPrice = (value) => {
    return currencyFormatter.format(value).replace("XOF", "FCFA").replace("F CFA", "FCFA");
};

// Compute and render KPIs
const updateMetrics = () => {
    const categories = mockDb.getCategories();
    const products = mockDb.getProducts();

    // Group product counts by category
    const catStats = {};
    categories.forEach(c => {
        catStats[c.id] = { name: c.name, count: 0 };
    });

    products.forEach(p => {
        if (catStats[p.categoryId]) {
            catStats[p.categoryId].count++;
        }
    });

    // Find top category
    let topCategoryName = "Aucune";
    let maxCount = -1;
    Object.keys(catStats).forEach(id => {
        if (catStats[id].count > maxCount && catStats[id].count > 0) {
            maxCount = catStats[id].count;
            topCategoryName = catStats[id].name;
        }
    });

    elements.metricTotal.textContent = categories.length;
    elements.metricAssociated.textContent = products.length;
    elements.metricTop.textContent = topCategoryName + (maxCount > 0 ? ` (${maxCount})` : "");
};

// Filter categories list
const loadCategories = () => {
    let filtered = mockDb.getCategories();

    if (state.searchQuery) {
        const query = state.searchQuery.toLowerCase();
        filtered = filtered.filter(c => 
            c.name.toLowerCase().includes(query) || 
            (c.description && c.description.toLowerCase().includes(query))
        );
    }

    state.categories = filtered;
    renderTable();
};

// Render rows
const renderTable = () => {
    const total = state.categories.length;
    const products = mockDb.getProducts();

    if (total === 0) {
        elements.tableBody.innerHTML = `
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <span class="empty-state-icon"><i class="fa-solid fa-folder-open"></i></span>
                        <h3>Aucune catégorie</h3>
                        <p>Créez votre première catégorie pour y classer vos produits.</p>
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
    
    const paginated = state.categories.slice(start, end);

    elements.tableBody.innerHTML = paginated.map(c => {
        // Calculate items and valuation in this category
        const catProducts = products.filter(p => p.categoryId === c.id);
        const productCount = catProducts.length;
        const totalValue = catProducts.reduce((sum, p) => sum + (p.purchasePrice * p.stock), 0);

        return `
            <tr>
                <td><strong>#${c.id}</strong></td>
                <td><strong style="color: var(--text); font-size: 0.95rem;">${c.name}</strong></td>
                <td style="color: var(--muted);">${c.description || "Aucune description"}</td>
                <td><strong>${productCount}</strong> produit(s)</td>
                <td><strong>${formatPrice(totalValue)}</strong></td>
                <td>
                    <div class="table-actions-cell" style="justify-content: flex-end;">
                        <button class="action-btn edit-cat-btn" data-id="${c.id}" type="button" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
                        <button class="action-btn is-danger delete-cat-btn" data-id="${c.id}" type="button" aria-label="Supprimer"><i class="fa-solid fa-trash"></i></button>
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
    // Edit buttons
    document.querySelectorAll(".edit-cat-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = Number(btn.dataset.id);
            openEditForm(id);
        });
    });

    // Delete buttons
    document.querySelectorAll(".delete-cat-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = Number(btn.dataset.id);
            checkAndDelete(id);
        });
    });
};

const checkAndDelete = (id) => {
    // RÈGLE N°7 validation checks
    const products = mockDb.getProducts();
    const associatedCount = products.filter(p => p.categoryId === id).length;

    if (associatedCount > 0) {
        showToast(`Impossible de supprimer cette catégorie car elle contient ${associatedCount} produit(s).`, "danger");
        return;
    }

    state.categoryToDelete = id;
    deleteModalHandler.open();
};

const openEditForm = (id) => {
    const categories = mockDb.getCategories();
    const cat = categories.find(c => c.id === id);
    if (!cat) return;

    elements.modalTitle.textContent = "Modifier la catégorie";
    document.getElementById("categoryId").value = cat.id;
    document.getElementById("cName").value = cat.name;
    document.getElementById("cDescription").value = cat.description || "";

    categoryModalHandler.open();
};

const handleFormSubmit = (e) => {
    e.preventDefault();

    // TODO PHP: Envoyer les données vers api/categories.php
    // TODO SQL: INSERT INTO categories / UPDATE categories

    const formData = new FormData(elements.form);
    const catData = {
        id: formData.get("id") ? Number(formData.get("id")) : null,
        name: formData.get("name"),
        description: formData.get("description")
    };

    if (!catData.name.trim()) {
        const fieldEl = document.getElementById("cName").closest(".form-field");
        fieldEl.classList.add("is-invalid");
        fieldEl.querySelector(".field-error").textContent = "Le nom de la catégorie est obligatoire.";
        showToast("Veuillez remplir le formulaire correctement.", "warning");
        return;
    }

    try {
        mockDb.saveCategory(catData);
        showToast(catData.id ? "Catégorie mise à jour avec succès !" : "Catégorie ajoutée avec succès !", "success");
        categoryModalHandler.close();
        loadCategories();
        updateMetrics();
    } catch (err) {
        showToast(err.message || "Erreur lors de l'enregistrement.", "danger");
    }
};

const handleConfirmDelete = () => {
    // TODO PHP: Requête vers api/categories.php
    // TODO SQL: DELETE FROM categories WHERE id = :id

    if (state.categoryToDelete) {
        try {
            const success = mockDb.deleteCategory(state.categoryToDelete);
            if (success) {
                showToast("La catégorie a été supprimée.", "success");
                deleteModalHandler.close();
                state.categoryToDelete = null;
                loadCategories();
                updateMetrics();
            } else {
                showToast("Catégorie introuvable.", "danger");
            }
        } catch (err) {
            showToast(err.message || "Erreur lors de la suppression.", "danger");
        }
    }
};

const bindEvents = () => {
    // Search input
    elements.search.addEventListener("input", (e) => {
        state.searchQuery = e.target.value;
        state.currentPage = 1;
        loadCategories();
    });

    // Pagination
    elements.prevBtn.addEventListener("click", () => {
        if (state.currentPage > 1) {
            state.currentPage--;
            renderTable();
        }
    });

    elements.nextBtn.addEventListener("click", () => {
        const pageCount = Math.ceil(state.categories.length / state.pageSize);
        if (state.currentPage < pageCount) {
            state.currentPage++;
            renderTable();
        }
    });

    // Modals initialization
    categoryModalHandler = initModal("categoryModal", "addCategoryBtn");

    const addBtn = document.getElementById("addCategoryBtn");
    if (addBtn) {
        addBtn.addEventListener("click", () => {
            elements.modalTitle.textContent = "Ajouter une catégorie";
            document.getElementById("categoryId").value = "";
            elements.form.reset();
        });
    }

    deleteModalHandler = initModal("deleteModal");

    elements.form.addEventListener("submit", handleFormSubmit);
    elements.confirmDeleteBtn.addEventListener("click", handleConfirmDelete);
};

const init = () => {
    updateMetrics();
    loadCategories();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
