/* E-Stockly Profile JS Module */

"use strict";

import { mockDb } from "./mockDb.js";
import { showToast } from "./components/components.js";

// Ensure mockDb is initialized
mockDb.init();

const elements = {
    form: document.getElementById("profileForm"),
    nameInput: document.getElementById("uName"),
    emailInput: document.getElementById("uEmail"),
    oldPassword: document.getElementById("oldPassword"),
    newPassword: document.getElementById("newPassword"),
    confirmPassword: document.getElementById("confirmPassword"),
    metaName: document.getElementById("metaName"),
    metaEmail: document.getElementById("metaEmail")
};

const loadProfile = () => {
    // Current user is Admin (id: 1)
    const admin = mockDb.getUsers().find(u => u.id === 1);
    if (!admin) return;

    elements.nameInput.value = admin.name || "Administrateur";
    elements.emailInput.value = admin.email || "admin@ngstock.com";
    elements.metaName.textContent = admin.name || "Administrateur";
    elements.metaEmail.textContent = admin.email || "admin@ngstock.com";
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

    if (!data.name.trim()) setError("uName", "Le nom complet est obligatoire.");
    if (!data.email.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) setError("uEmail", "Veuillez saisir un email valide.");

    // Password validation logic
    if (data.newPassword || data.oldPassword || data.confirmPassword) {
        if (!data.oldPassword) {
            setError("oldPassword", "Saisissez votre mot de passe actuel.");
        } else if (data.oldPassword !== "Admin123") { // Mock check
            setError("oldPassword", "Mot de passe actuel incorrect.");
        }

        if (!data.newPassword) {
            setError("newPassword", "Veuillez saisir votre nouveau mot de passe.");
        } else if (data.newPassword.length < 6) {
            setError("newPassword", "Le mot de passe doit faire au moins 6 caractères.");
        }

        if (data.newPassword !== data.confirmPassword) {
            setError("confirmPassword", "Les mots de passe ne correspondent pas.");
        }
    }

    return isValid;
};

const handleFormSubmit = (e) => {
    e.preventDefault();

    // TODO PHP: Envoyer les données vers api/profile.php
    // TODO SQL: UPDATE utilisateurs SET name = :name, email = :email... WHERE id = 1

    const profileData = {
        name: elements.nameInput.value,
        email: elements.emailInput.value,
        oldPassword: elements.oldPassword.value,
        newPassword: elements.newPassword.value,
        confirmPassword: elements.confirmPassword.value
    };

    if (!validateForm(profileData)) {
        showToast("Veuillez corriger le formulaire.", "warning");
        return;
    }

    try {
        // Save Admin Info
        const admin = mockDb.getUsers().find(u => u.id === 1);
        if (admin) {
            admin.name = profileData.name;
            admin.email = profileData.email;
            mockDb.saveUser(admin);
            
            showToast("Profil enregistré avec succès !", "success");
            
            // Clear passwords fields
            elements.oldPassword.value = "";
            elements.newPassword.value = "";
            elements.confirmPassword.value = "";

            // Update top bar user menu text labels
            const userMenuStrong = document.querySelector("#userMenuBtn strong");
            if (userMenuStrong) userMenuStrong.textContent = profileData.name;

            loadProfile();
        }
    } catch (err) {
        showToast("Erreur lors de l'enregistrement.", "danger");
    }
};

const bindEvents = () => {
    elements.form.addEventListener("submit", handleFormSubmit);
};

const init = () => {
    loadProfile();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
