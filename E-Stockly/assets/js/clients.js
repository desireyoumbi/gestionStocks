/* E-Stockly Clients JS Module */

"use strict";

import { mockDb } from "./mockDb.js";
import { showToast, initModal } from "./components/components.js";

// Ensure mockDb is initialized
mockDb.init();

// State
let state = {
    clients: [],
    currentPage: 1,
    pageSize: 10,
    searchQuery: "",
    clientToDelete: null
};

// Modal helpers
let clientModalHandler;
let deleteModalHandler;

const elements = {
    tableBody: document.querySelector("#clientsTable tbody"),
    metricTotal: document.getElementById("metricTotalClients"),
    search: document.getElementById("clientSearch"),
    prevBtn: document.getElementById("prevPageBtn"),
    nextBtn: document.getElementById("nextPageBtn"),
    pageStart: document.getElementById("pageStart"),
    pageEnd: document.getElementById("pageEnd"),
    totalCount: document.getElementById("totalCount"),
    form: document.getElementById("clientForm"),
    modalTitle: document.getElementById("modalTitle"),
    confirmDeleteBtn: document.getElementById("confirmDeleteBtn")
};

const updateMetrics = () => {
    const clients = mockDb.getClients();
    elements.metricTotal.textContent = clients.length;
};

const loadClients = () => {
    let filtered = mockDb.getClients();

    if (state.searchQuery) {
        const query = state.searchQuery.toLowerCase();
        filtered = filtered.filter(c => 
            c.name.toLowerCase().includes(query) || 
            c.email.toLowerCase().includes(query) || 
            c.phone.includes(query)
        );
    }

    state.clients = filtered;
    renderTable();
};

const renderTable = () => {
    const total = state.clients.length;

    if (total === 0) {
        elements.tableBody.innerHTML = `
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <span class="empty-state-icon"><i class="fa-solid fa-users"></i></span>
                        <h3>Aucun client</h3>
                        <p>Ajoutez des clients pour suivre leurs achats.</p>
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
    
    const paginated = state.clients.slice(start, end);

    elements.tableBody.innerHTML = paginated.map(c => {
        return `
            <tr>
                <td><strong>#${c.id}</strong></td>
                <td><strong style="color: var(--text);">${c.name}</strong></td>
                <td>${c.phone}</td>
                <td><a href="mailto:${c.email}" style="color: var(--primary); text-decoration: underline;">${c.email}</a></td>
                <td style="color: var(--muted);">${c.address || "—"}</td>
                <td>
                    <div class="table-actions-cell" style="justify-content: flex-end;">
                        <button class="action-btn edit-client-btn" data-id="${c.id}" type="button" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
                        <button class="action-btn is-danger delete-client-btn" data-id="${c.id}" type="button" aria-label="Supprimer"><i class="fa-solid fa-trash"></i></button>
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
    document.querySelectorAll(".edit-client-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = Number(btn.dataset.id);
            openEditForm(id);
        });
    });

    document.querySelectorAll(".delete-client-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            state.clientToDelete = Number(btn.dataset.id);
            deleteModalHandler.open();
        });
    });
};

const openEditForm = (id) => {
    const clients = mockDb.getClients();
    const client = clients.find(c => c.id === id);
    if (!client) return;

    elements.modalTitle.textContent = "Modifier le client";
    document.getElementById("clientId").value = client.id;
    document.getElementById("cName").value = client.name;
    document.getElementById("cPhone").value = client.phone;
    document.getElementById("cEmail").value = client.email;
    document.getElementById("cAddress").value = client.address || "";

    clientModalHandler.open();
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

    if (!data.name.trim()) setError("cName", "Le nom du client est requis.");
    if (!data.phone.trim()) setError("cPhone", "Le téléphone est requis.");
    if (!data.email.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) setError("cEmail", "Email invalide.");

    return isValid;
};

const handleFormSubmit = (e) => {
    e.preventDefault();

    // TODO PHP: Envoyer les données vers api/clients.php
    // TODO SQL: INSERT INTO clients / UPDATE clients

    const formData = new FormData(elements.form);
    const clientData = {
        id: formData.get("id") ? Number(formData.get("id")) : null,
        name: formData.get("name"),
        phone: formData.get("phone"),
        email: formData.get("email"),
        address: formData.get("address")
    };

    if (!validateForm(clientData)) {
        showToast("Veuillez corriger le formulaire.", "warning");
        return;
    }

    try {
        mockDb.saveClient(clientData);
        showToast(clientData.id ? "Fiche client mise à jour !" : "Client ajouté avec succès !", "success");
        clientModalHandler.close();
        loadClients();
        updateMetrics();
    } catch (err) {
        showToast(err.message || "Erreur d'enregistrement.", "danger");
    }
};

const handleConfirmDelete = () => {
    // TODO PHP: Requête vers api/clients.php
    // TODO SQL: DELETE FROM clients WHERE id = :id

    if (state.clientToDelete) {
        const success = mockDb.deleteClient(state.clientToDelete);
        if (success) {
            showToast("Le client a été supprimé.", "success");
            deleteModalHandler.close();
            state.clientToDelete = null;
            loadClients();
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
        loadClients();
    });

    // Pagination
    elements.prevBtn.addEventListener("click", () => {
        if (state.currentPage > 1) {
            state.currentPage--;
            renderTable();
        }
    });

    elements.nextBtn.addEventListener("click", () => {
        const pageCount = Math.ceil(state.clients.length / state.pageSize);
        if (state.currentPage < pageCount) {
            state.currentPage++;
            renderTable();
        }
    });

    // Modals
    clientModalHandler = initModal("clientModal", "addClientBtn");

    const addBtn = document.getElementById("addClientBtn");
    if (addBtn) {
        addBtn.addEventListener("click", () => {
            elements.modalTitle.textContent = "Ajouter un client";
            document.getElementById("clientId").value = "";
            elements.form.reset();
        });
    }

    deleteModalHandler = initModal("deleteModal");

    elements.form.addEventListener("submit", handleFormSubmit);
    elements.confirmDeleteBtn.addEventListener("click", handleConfirmDelete);
};

const init = () => {
    updateMetrics();
    loadClients();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
