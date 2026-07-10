/* E-Stockly Roles JS Module */

"use strict";

import { mockDb } from "./mockDb.js";
import { showToast, initModal } from "./components/components.js";

// Ensure mockDb is initialized
mockDb.init();

// State
let state = {
    roles: []
};

let roleModalHandler;

const elements = {
    tableBody: document.querySelector("#rolesTable tbody"),
    metricCount: document.getElementById("metricRolesCount"),
    form: document.getElementById("roleForm"),
    modalTitle: document.getElementById("modalTitle")
};

const updateMetrics = () => {
    const roles = mockDb.getRoles();
    elements.metricCount.textContent = roles.length;
};

const loadRoles = () => {
    state.roles = mockDb.getRoles();
    renderTable();
};

const renderTable = () => {
    if (state.roles.length === 0) {
        elements.tableBody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; padding: 24px; color: var(--muted);">
                    Aucun rôle configuré.
                </td>
            </tr>
        `;
        return;
    }

    elements.tableBody.innerHTML = state.roles.map(r => {
        const permissionsBadges = r.permissions.map(p => {
            let label = p;
            if (p === "products") label = "Produits";
            if (p === "categories") label = "Catégories";
            if (p === "stock") label = "Stock";
            if (p === "movements") label = "Mouvements";
            if (p === "suppliers") label = "Fournisseurs";
            if (p === "sales") label = "Ventes";
            if (p === "deliveries") label = "Livraisons";
            if (p === "users") label = "Utilisateurs";
            if (p === "roles") label = "Rôles";
            if (p === "history") label = "Historique";
            if (p === "settings") label = "Paramètres";
            
            return `<span class="badge is-neutral" style="margin-right: 4px; margin-bottom: 4px;">${label}</span>`;
        }).join("");

        return `
            <tr>
                <td><strong>#${r.id}</strong></td>
                <td><strong style="color: var(--text);">${r.name === "Admin" ? "Administrateur" : r.name}</strong></td>
                <td style="color: var(--muted);">${r.description}</td>
                <td>
                    <div style="display: flex; flex-wrap: wrap;">
                        ${permissionsBadges || '<span class="badge is-danger">Aucune</span>'}
                    </div>
                </td>
                <td>
                    <div class="table-actions-cell" style="justify-content: flex-end;">
                        <button class="action-btn edit-role-btn" data-id="${r.id}" type="button" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
                    </div>
                </td>
            </tr>
        `;
    }).join("");

    bindRowButtons();
};

const bindRowButtons = () => {
    document.querySelectorAll(".edit-role-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = Number(btn.dataset.id);
            openEditForm(id);
        });
    });
};

const openEditForm = (id) => {
    const role = state.roles.find(r => r.id === id);
    if (!role) return;

    elements.modalTitle.textContent = "Modifier le rôle";
    document.getElementById("roleId").value = role.id;
    document.getElementById("rName").value = role.name;
    document.getElementById("rName").readOnly = true;
    document.getElementById("rDescription").value = role.description;

    // Check permissions boxes
    elements.form.querySelectorAll("input[type='checkbox']").forEach(chk => {
        chk.checked = role.permissions.includes(chk.value);
    });

    roleModalHandler.open();
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

    if (!data.name.trim()) setError("rName", "Le nom du rôle est requis.");
    if (!data.description.trim()) setError("rDescription", "La description est requise.");

    return isValid;
};

const handleFormSubmit = (e) => {
    e.preventDefault();

    // TODO PHP: api/roles.php
    // TODO SQL: UPDATE roles / INSERT INTO roles

    const id = document.getElementById("roleId").value ? Number(document.getElementById("roleId").value) : null;
    const name = document.getElementById("rName").value;
    const description = document.getElementById("rDescription").value;

    // Collect permissions
    const checkedPermissions = [];
    elements.form.querySelectorAll("input[type='checkbox']").forEach(chk => {
        if (chk.checked) {
            checkedPermissions.push(chk.value);
        }
    });

    const roleData = { id, name, description, permissions: checkedPermissions };

    if (!validateForm(roleData)) {
        showToast("Veuillez corriger le formulaire.", "warning");
        return;
    }

    try {
        mockDb.saveRole(roleData);
        showToast(id ? "Rôle modifié avec succès !" : "Rôle ajouté avec succès !", "success");
        roleModalHandler.close();
        loadRoles();
        updateMetrics();
    } catch (err) {
        showToast("Erreur lors de l'enregistrement.", "danger");
    }
};

const bindEvents = () => {
    roleModalHandler = initModal("roleModal", "addRoleBtn");

    const addBtn = document.getElementById("addRoleBtn");
    if (addBtn) {
        addBtn.addEventListener("click", () => {
            elements.modalTitle.textContent = "Ajouter un rôle";
            document.getElementById("roleId").value = "";
            elements.form.reset();
            elements.form.querySelectorAll("input[type='checkbox']").forEach(c => c.checked = false);
        });
    }

    elements.form.addEventListener("submit", handleFormSubmit);
};

const init = () => {
    updateMetrics();
    loadRoles();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
