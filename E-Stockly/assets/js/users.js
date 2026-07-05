/* E-Stockly Users JS Module */

"use strict";

import { mockDb } from "./mockDb.js";
import { showToast, initModal } from "./components/components.js";

// Ensure mockDb is initialized
mockDb.init();

// State
let state = {
    users: [],
    roles: [],
    currentPage: 1,
    pageSize: 10,
    searchQuery: "",
    userToDelete: null
};

// Modals
let userModalHandler;
let deleteModalHandler;

const elements = {
    tableBody: document.querySelector("#usersTable tbody"),
    metricActive: document.getElementById("metricActiveUsers"),
    metricRoles: document.getElementById("metricRolesCount"),
    search: document.getElementById("userSearch"),
    prevBtn: document.getElementById("prevPageBtn"),
    nextBtn: document.getElementById("nextPageBtn"),
    pageStart: document.getElementById("pageStart"),
    pageEnd: document.getElementById("pageEnd"),
    totalCount: document.getElementById("totalCount"),
    form: document.getElementById("userForm"),
    modalTitle: document.getElementById("modalTitle"),
    confirmDeleteBtn: document.getElementById("confirmDeleteBtn"),
    roleSelect: document.getElementById("uRole"),
    passwordContainer: document.getElementById("passwordFieldContainer"),
    passwordInput: document.getElementById("uPassword")
};

const updateMetrics = () => {
    const users = mockDb.getUsers();
    const roles = mockDb.getRoles();

    elements.metricActive.textContent = users.filter(u => u.status === "active").length;
    elements.metricRoles.textContent = roles.length;
};

const initSelectors = () => {
    state.roles = mockDb.getRoles();
    
    // Roles selector
    elements.roleSelect.innerHTML = '<option value="">Choisir un rôle</option>' +
        state.roles.map(r => `<option value="${r.name}">${r.name}</option>`).join("");
};

const loadUsers = () => {
    let filtered = mockDb.getUsers();

    if (state.searchQuery) {
        const query = state.searchQuery.toLowerCase();
        filtered = filtered.filter(u => 
            u.name.toLowerCase().includes(query) || 
            u.email.toLowerCase().includes(query) || 
            u.role.toLowerCase().includes(query)
        );
    }

    state.users = filtered;
    renderTable();
};

const renderTable = () => {
    const total = state.users.length;

    if (total === 0) {
        elements.tableBody.innerHTML = `
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <span class="empty-state-icon"><i class="fa-solid fa-user-gear"></i></span>
                        <h3>Aucun utilisateur</h3>
                        <p>Créez un compte utilisateur pour autoriser l'accès au système.</p>
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
    
    const paginated = state.users.slice(start, end);

    elements.tableBody.innerHTML = paginated.map(u => {
        const statusBadge = u.status === "active" ? "is-success" : "is-neutral";
        const statusText = u.status === "active" ? "Actif" : "Inactif";

        return `
            <tr>
                <td><strong>#${u.id}</strong></td>
                <td><strong style="color: var(--text);">${u.name}</strong></td>
                <td>${u.email}</td>
                <td><span class="badge is-info">${u.role}</span></td>
                <td><span class="badge ${statusBadge}">${statusText}</span></td>
                <td>
                    <div class="table-actions-cell" style="justify-content: flex-end;">
                        <button class="action-btn edit-user-btn" data-id="${u.id}" type="button" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
                        <button class="action-btn is-danger delete-user-btn" data-id="${u.id}" type="button" aria-label="Supprimer"><i class="fa-solid fa-trash"></i></button>
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
    document.querySelectorAll(".edit-user-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = Number(btn.dataset.id);
            openEditForm(id);
        });
    });

    document.querySelectorAll(".delete-user-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            state.userToDelete = Number(btn.dataset.id);
            deleteModalHandler.open();
        });
    });
};

const openEditForm = (id) => {
    const users = mockDb.getUsers();
    const user = users.find(u => u.id === id);
    if (!user) return;

    elements.modalTitle.textContent = "Modifier l'utilisateur";
    document.getElementById("userId").value = user.id;
    document.getElementById("uName").value = user.name;
    document.getElementById("uEmail").value = user.email;
    document.getElementById("uRole").value = user.role;
    document.getElementById("uStatus").value = user.status;
    
    // Hide password for modifications
    elements.passwordContainer.style.display = "none";
    elements.passwordInput.required = false;

    userModalHandler.open();
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

    if (!data.name.trim()) setError("uName", "Nom complet requis.");
    if (!data.email.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) setError("uEmail", "Email invalide.");
    if (!data.id && !data.password) setError("uPassword", "Le mot de passe est obligatoire à la création.");
    if (!data.role) setError("uRole", "Sélectionnez un rôle.");

    return isValid;
};

const handleFormSubmit = (e) => {
    e.preventDefault();

    // TODO PHP: Envoyer les données vers api/utilisateurs.php
    // TODO SQL: INSERT INTO utilisateurs / UPDATE utilisateurs

    const formData = new FormData(elements.form);
    const userData = {
        id: formData.get("id") ? Number(formData.get("id")) : null,
        name: formData.get("name"),
        email: formData.get("email"),
        role: formData.get("role"),
        status: formData.get("status"),
        password: formData.get("password")
    };

    if (!validateForm(userData)) {
        showToast("Veuillez remplir le formulaire.", "warning");
        return;
    }

    try {
        mockDb.saveUser(userData);
        showToast(userData.id ? "Utilisateur modifié !" : "Nouvel utilisateur enregistré !", "success");
        userModalHandler.close();
        loadUsers();
        updateMetrics();
    } catch (err) {
        showToast("Erreur lors de l'enregistrement.", "danger");
    }
};

const handleConfirmDelete = () => {
    // TODO PHP: api/utilisateurs.php
    // TODO SQL: DELETE FROM utilisateurs WHERE id = :id

    if (state.userToDelete) {
        // Prevent deleting the primary admin account (ID 1)
        if (state.userToDelete === 1) {
            showToast("Impossible de supprimer le compte administrateur principal.", "danger");
            deleteModalHandler.close();
            state.userToDelete = null;
            return;
        }

        const success = mockDb.deleteUser(state.userToDelete);
        if (success) {
            showToast("L'utilisateur a été supprimé.", "success");
            deleteModalHandler.close();
            state.userToDelete = null;
            loadUsers();
            updateMetrics();
        } else {
            showToast("Erreur lors de la suppression.", "danger");
        }
    }
};

const bindEvents = () => {
    elements.search.addEventListener("input", (e) => {
        state.searchQuery = e.target.value;
        state.currentPage = 1;
        loadUsers();
    });

    elements.prevBtn.addEventListener("click", () => {
        if (state.currentPage > 1) {
            state.currentPage--;
            renderTable();
        }
    });

    elements.nextBtn.addEventListener("click", () => {
        const pageCount = Math.ceil(state.users.length / state.pageSize);
        if (state.currentPage < pageCount) {
            state.currentPage++;
            renderTable();
        }
    });

    userModalHandler = initModal("userModal", "addUserBtn");

    const addBtn = document.getElementById("addUserBtn");
    if (addBtn) {
        addBtn.addEventListener("click", () => {
            elements.modalTitle.textContent = "Ajouter un utilisateur";
            document.getElementById("userId").value = "";
            elements.passwordContainer.style.display = "flex";
            elements.passwordInput.required = true;
            elements.form.reset();
        });
    }

    deleteModalHandler = initModal("deleteModal");

    elements.form.addEventListener("submit", handleFormSubmit);
    elements.confirmDeleteBtn.addEventListener("click", handleConfirmDelete);
};

const init = () => {
    initSelectors();
    updateMetrics();
    loadUsers();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
