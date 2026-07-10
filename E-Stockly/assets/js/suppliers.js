/* E-Stockly Suppliers JS Module */

"use strict";

import { mockDb } from "./mockDb.js";
import { showToast, initModal } from "./components/components.js";

// Ensure mockDb is initialized
mockDb.init();

// State management
let state = {
    suppliers: [],
    currentPage: 1,
    pageSize: 10,
    searchQuery: "",
    supplierToDelete: null
};

// Modal helpers
let supplierModalHandler;
let deleteModalHandler;

const elements = {
    tableBody: document.querySelector("#suppliersTable tbody"),
    metricTotal: document.getElementById("metricTotalSuppliers"),
    metricPending: document.getElementById("metricPendingOrders"),
    metricReferenced: document.getElementById("metricReferencedProducts"),
    search: document.getElementById("supplierSearch"),
    prevBtn: document.getElementById("prevPageBtn"),
    nextBtn: document.getElementById("nextPageBtn"),
    pageStart: document.getElementById("pageStart"),
    pageEnd: document.getElementById("pageEnd"),
    totalCount: document.getElementById("totalCount"),
    form: document.getElementById("supplierForm"),
    modalTitle: document.getElementById("modalTitle"),
    confirmDeleteBtn: document.getElementById("confirmDeleteBtn")
};

const updateMetrics = () => {
    const suppliers = mockDb.getSuppliers();
    const deliveries = mockDb.getDeliveries();
    const products = mockDb.getProducts();

    const pendingCount = deliveries.filter(d => d.status === "pending").length;
    const referencedProducts = products.filter(p => p.supplierId > 0).length;

    elements.metricTotal.textContent = suppliers.length;
    elements.metricPending.textContent = pendingCount;
    elements.metricReferenced.textContent = referencedProducts;
};

const loadSuppliers = () => {
    let filtered = mockDb.getSuppliers();

    if (state.searchQuery) {
        const query = state.searchQuery.toLowerCase();
        filtered = filtered.filter(s => 
            s.name.toLowerCase().includes(query) || 
            s.contact.toLowerCase().includes(query) || 
            s.email.toLowerCase().includes(query) ||
            s.phone.includes(query)
        );
    }

    state.suppliers = filtered;
    renderTable();
};

const renderTable = () => {
    const total = state.suppliers.length;

    if (total === 0) {
        elements.tableBody.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <span class="empty-state-icon"><i class="fa-solid fa-truck-field"></i></span>
                        <h3>Aucun fournisseur</h3>
                        <p>Ajoutez un partenaire d'approvisionnement pour commencer à commander.</p>
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
    
    const paginated = state.suppliers.slice(start, end);

    elements.tableBody.innerHTML = paginated.map(s => {
        return `
            <tr>
                <td><strong>#${s.id}</strong></td>
                <td><strong style="color: var(--text);">${s.name}</strong></td>
                <td>${s.contact}</td>
                <td>${s.phone}</td>
                <td><a href="mailto:${s.email}" style="color: var(--primary); text-decoration: underline;">${s.email}</a></td>
                <td style="color: var(--muted);">${s.address || "—"}</td>
                <td>
                    <div class="table-actions-cell" style="justify-content: flex-end;">
                        <button class="action-btn edit-sup-btn" data-id="${s.id}" type="button" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
                        <button class="action-btn is-danger delete-sup-btn" data-id="${s.id}" type="button" aria-label="Supprimer"><i class="fa-solid fa-trash"></i></button>
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
    document.querySelectorAll(".edit-sup-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = Number(btn.dataset.id);
            openEditForm(id);
        });
    });

    document.querySelectorAll(".delete-sup-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            state.supplierToDelete = Number(btn.dataset.id);
            deleteModalHandler.open();
        });
    });
};

const openEditForm = (id) => {
    const suppliers = mockDb.getSuppliers();
    const sup = suppliers.find(s => s.id === id);
    if (!sup) return;

    elements.modalTitle.textContent = "Modifier le fournisseur";
    document.getElementById("supplierId").value = sup.id;
    document.getElementById("sName").value = sup.name;
    document.getElementById("sContact").value = sup.contact;
    document.getElementById("sPhone").value = sup.phone;
    document.getElementById("sEmail").value = sup.email;
    document.getElementById("sAddress").value = sup.address || "";

    supplierModalHandler.open();
};

const validateForm = (data) => {
    let isValid = true;
    document.querySelectorAll(".form-field").forEach(f => f.classList.remove("is-invalid"));
    document.querySelectorAll(".field-error").forEach(e => e.textContent = "");

    const setError = (id, message) => {
        const fieldEl = document.getElementById(id).closest(".form-field");
        fieldEl.classList.add("is-invalid");
        fieldEl.querySelector(".field-error").textContent = message;
        isValid = false;
    };

    if (!data.name.trim()) setError("sName", "Le nom de l'entreprise est requis.");
    if (!data.contact.trim()) setError("sContact", "Le nom du contact est requis.");
    if (!data.phone.trim()) setError("sPhone", "Le numéro de téléphone est requis.");
    if (!data.email.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) setError("sEmail", "Email invalide.");

    return isValid;
};

const handleFormSubmit = (e) => {
    e.preventDefault();

    // TODO PHP: Envoyer vers api/fournisseurs.php
    // TODO SQL: INSERT INTO fournisseurs / UPDATE fournisseurs

    const formData = new FormData(elements.form);
    const supData = {
        id: formData.get("id") ? Number(formData.get("id")) : null,
        name: formData.get("name"),
        contact: formData.get("contact"),
        phone: formData.get("phone"),
        email: formData.get("email"),
        address: formData.get("address")
    };

    if (!validateForm(supData)) {
        showToast("Veuillez remplir correctement les champs.", "warning");
        return;
    }

    try {
        mockDb.saveSupplier(supData);
        showToast(supData.id ? "Fournisseur mis à jour !" : "Fournisseur ajouté avec succès !", "success");
        supplierModalHandler.close();
        loadSuppliers();
        updateMetrics();
    } catch (err) {
        showToast(err.message || "Erreur lors de l'enregistrement.", "danger");
    }
};

const handleConfirmDelete = () => {
    // TODO PHP: Suppression api/fournisseurs.php
    // TODO SQL: DELETE FROM fournisseurs WHERE id = :id

    if (state.supplierToDelete) {
        // Prevent if there are products attached? In mockup we allow it, but we clear associations.
        const success = mockDb.deleteSupplier(state.supplierToDelete);
        if (success) {
            showToast("Le fournisseur a été supprimé.", "success");
            deleteModalHandler.close();
            state.supplierToDelete = null;
            loadSuppliers();
            updateMetrics();
        } else {
            showToast("Impossible de supprimer.", "danger");
        }
    }
};

const bindEvents = () => {
    // Search
    elements.search.addEventListener("input", (e) => {
        state.searchQuery = e.target.value;
        state.currentPage = 1;
        loadSuppliers();
    });

    // Pagination
    elements.prevBtn.addEventListener("click", () => {
        if (state.currentPage > 1) {
            state.currentPage--;
            renderTable();
        }
    });

    elements.nextBtn.addEventListener("click", () => {
        const pageCount = Math.ceil(state.suppliers.length / state.pageSize);
        if (state.currentPage < pageCount) {
            state.currentPage++;
            renderTable();
        }
    });

    // Modals
    supplierModalHandler = initModal("supplierModal", "addSupplierBtn");

    const addBtn = document.getElementById("addSupplierBtn");
    if (addBtn) {
        addBtn.addEventListener("click", () => {
            elements.modalTitle.textContent = "Ajouter un fournisseur";
            document.getElementById("supplierId").value = "";
            elements.form.reset();
        });
    }

    deleteModalHandler = initModal("deleteModal");

    elements.form.addEventListener("submit", handleFormSubmit);
    elements.confirmDeleteBtn.addEventListener("click", handleConfirmDelete);
};

const init = () => {
    updateMetrics();
    loadSuppliers();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
