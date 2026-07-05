/* E-Stockly Settings JS Module */

"use strict";

import { mockDb } from "./mockDb.js";
import { showToast } from "./components/components.js";

// Ensure mockDb is initialized
mockDb.init();

const elements = {
    form: document.getElementById("settingsForm"),
    companyName: document.getElementById("companyName"),
    address: document.getElementById("companyAddress"),
    phone: document.getElementById("companyPhone"),
    email: document.getElementById("companyEmail"),
    currency: document.getElementById("companyCurrency"),
    backupInterval: document.getElementById("backupInterval"),
    downloadBtn: document.getElementById("downloadBackupBtn"),
    uploadInput: document.getElementById("uploadBackupInput")
};

const loadSettings = () => {
    const settings = mockDb.getSettings();

    elements.companyName.value = settings.companyName || "";
    elements.address.value = settings.address || "";
    elements.phone.value = settings.phone || "";
    elements.email.value = settings.email || "";
    elements.currency.value = settings.currency || "FCFA";
    elements.backupInterval.value = settings.backupInterval || "weekly";
};

const handleFormSubmit = (e) => {
    e.preventDefault();

    // TODO PHP: api/settings.php
    // TODO SQL: UPDATE parametres

    const settingsData = {
        companyName: elements.companyName.value,
        address: elements.address.value,
        phone: elements.phone.value,
        email: elements.email.value,
        currency: elements.currency.value,
        backupInterval: elements.backupInterval.value
    };

    // Simple validation
    if (!settingsData.companyName.trim() || !settingsData.address.trim() || !settingsData.phone.trim() || !settingsData.email.trim()) {
        showToast("Veuillez remplir tous les champs obligatoires.", "warning");
        return;
    }

    try {
        mockDb.saveSettings(settingsData);
        showToast("Paramètres sauvegardés avec succès !", "success");
    } catch (err) {
        showToast("Erreur lors de l'enregistrement.", "danger");
    }
};

const handleDownloadBackup = () => {
    showToast("Préparation de la sauvegarde SQL...", "info");
    
    // Simulate generation delay
    setTimeout(() => {
        const dateStr = new Date().toISOString().split('T')[0];
        const content = `-- E-Stockly SQL Backup File
-- Date: ${new Date().toLocaleString()}
-- Database Version: v1.0.4

CREATE TABLE IF NOT EXISTS \`produits\` (
  \`id\` int(11) NOT NULL AUTO_INCREMENT,
  \`name\` varchar(255) NOT NULL,
  \`stock\` int(11) NOT NULL,
  PRIMARY KEY (\`id\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO \`produits\` VALUES (1, 'Dell Latitude', 22);
`;

        const blob = new Blob([content], { type: "text/sql" });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download = `estockly_backup_${dateStr}.sql`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        
        showToast("Sauvegarde téléchargée avec succès !", "success");
    }, 1500);
};

const handleUploadBackup = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    showToast(`Lecture de ${file.name}...`, "info");

    setTimeout(() => {
        showToast("Restauration de la base de données en cours...", "warning");
        
        setTimeout(() => {
            showToast("Base de données restaurée avec succès !", "success");
            // Reset DB to trigger mock entries check
            loadSettings();
        }, 2000);
    }, 1200);
};

const bindEvents = () => {
    elements.form.addEventListener("submit", handleFormSubmit);
    elements.downloadBtn.addEventListener("click", handleDownloadBackup);
    elements.uploadInput.addEventListener("change", handleUploadBackup);
};

const init = () => {
    loadSettings();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
