/* E-Stockly Sales JS Module */

"use strict";

import { mockDb } from "./mockDb.js";
import { showToast, initModal } from "./components/components.js";

mockDb.init();

let state = {
    sales: [],
    products: [],
    sellers: [],
    currentPage: 1,
    pageSize: 10,
    searchQuery: "",
    filterStatus: "",
    filterPayment: "",
    filterSeller: "",
    sortBy: "date-desc",
    saleToDelete: null
};

let saleModalHandler;
let deleteModalHandler;

const elements = {
    tableBody: document.querySelector("#salesTable tbody"),
    metricTotal: document.getElementById("metricTotalSales"),
    metricRevenue: document.getElementById("metricTotalRevenue"),
    metricPending: document.getElementById("metricPendingSales"),
    metricCompleted: document.getElementById("metricCompletedSales"),
    search: document.getElementById("saleSearch"),
    filterStatus: document.getElementById("filterStatus"),
    filterPayment: document.getElementById("filterPayment"),
    filterSeller: document.getElementById("filterSeller"),
    sortSales: document.getElementById("sortSales"),
    prevBtn: document.getElementById("prevPageBtn"),
    nextBtn: document.getElementById("nextPageBtn"),
    pageStart: document.getElementById("pageStart"),
    pageEnd: document.getElementById("pageEnd"),
    totalCount: document.getElementById("totalCount"),
    form: document.getElementById("saleForm"),
    modalTitle: document.getElementById("modalTitle"),
    confirmDeleteBtn: document.getElementById("confirmDeleteBtn"),
    productSelect: document.getElementById("sProduct"),
    qtyInput: document.getElementById("sQty"),
    unitPriceInput: document.getElementById("sUnitPrice"),
    totalInput: document.getElementById("sTotal"),
    sellerSelect: document.getElementById("sSeller"),
    exportBtn: document.getElementById("exportSalesBtn")
};

const currencyFormatter = new Intl.NumberFormat("fr-FR", {
    style: "currency",
    currency: "XOF",
    maximumFractionDigits: 0
});

const formatPrice = (value) => currencyFormatter.format(value).replace("XOF", "FCFA").replace("F CFA", "FCFA");

const formatDate = (dateStr) => {
    try {
        const dateObj = new Date(dateStr);
        if (!isNaN(dateObj.getTime())) {
            return dateObj.toLocaleString("fr-FR", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            });
        }
    } catch (e) {}
    return dateStr;
};

const getStatusBadge = (status) => {
    if (status === "completed") return { cls: "is-success", text: "Validée" };
    if (status === "pending") return { cls: "is-warning", text: "En attente" };
    if (status === "cancelled") return { cls: "is-danger", text: "Annulée" };
    return { cls: "is-neutral", text: status };
};

const updateTotalPreview = () => {
    const qty = Number(elements.qtyInput.value) || 0;
    const unitPrice = Number(elements.unitPriceInput.value) || 0;
    elements.totalInput.value = qty * unitPrice;
};

const initSelectors = () => {
    state.products = mockDb.getProducts();
    state.sellers = mockDb.getUsers().filter(u => u.role === "Vendeur" || u.role === "Admin");

    elements.productSelect.innerHTML = '<option value="">Choisir un produit</option>' +
        state.products.map(p => `<option value="${p.id}" data-price="${p.price}">${p.name} (Stock: ${p.stock})</option>`).join("");

    elements.sellerSelect.innerHTML = '<option value="">Choisir un vendeur</option>' +
        state.sellers.map(u => `<option value="${u.name}">${u.name}</option>`).join("");

    const sellerNames = [...new Set(mockDb.getSales().map(s => s.seller))];
    elements.filterSeller.innerHTML = '<option value="">Tous les vendeurs</option>' +
        sellerNames.map(name => `<option value="${name}">${name}</option>`).join("");
};

const updateMetrics = () => {
    const sales = mockDb.getSales();
    const completed = sales.filter(s => s.status === "completed");
    const revenue = completed.reduce((sum, s) => sum + (s.totalPrice || 0), 0);

    elements.metricTotal.textContent = sales.length;
    elements.metricRevenue.textContent = formatPrice(revenue);
    elements.metricPending.textContent = sales.filter(s => s.status === "pending").length;
    elements.metricCompleted.textContent = completed.length;
};

const sortSales = (list) => {
    const sorted = [...list];
    switch (state.sortBy) {
        case "date-asc":
            sorted.sort((a, b) => new Date(a.date) - new Date(b.date));
            break;
        case "total-desc":
            sorted.sort((a, b) => (b.totalPrice || 0) - (a.totalPrice || 0));
            break;
        case "total-asc":
            sorted.sort((a, b) => (a.totalPrice || 0) - (b.totalPrice || 0));
            break;
        default:
            sorted.sort((a, b) => new Date(b.date) - new Date(a.date));
    }
    return sorted;
};

const loadSales = () => {
    let filtered = mockDb.getSales();

    if (state.searchQuery) {
        const query = state.searchQuery.toLowerCase();
        filtered = filtered.filter(s =>
            String(s.id).includes(query) ||
            (s.seller && s.seller.toLowerCase().includes(query)) ||
            (s.productName && s.productName.toLowerCase().includes(query)) ||
            (s.note && s.note.toLowerCase().includes(query))
        );
    }

    if (state.filterStatus) {
        filtered = filtered.filter(s => s.status === state.filterStatus);
    }

    if (state.filterPayment) {
        filtered = filtered.filter(s => s.paymentMethod === state.filterPayment);
    }

    if (state.filterSeller) {
        filtered = filtered.filter(s => s.seller === state.filterSeller);
    }

    state.sales = sortSales(filtered);
    renderTable();
};

const renderTable = () => {
    const total = state.sales.length;

    if (total === 0) {
        elements.tableBody.innerHTML = `
            <tr>
                <td colspan="11">
                    <div class="empty-state">
                        <span class="empty-state-icon"><i class="fa-solid fa-cart-shopping"></i></span>
                        <h3>Aucune vente</h3>
                        <p>Enregistrez une nouvelle vente pour suivre les sorties de stock.</p>
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
    const paginated = state.sales.slice(start, end);

    elements.tableBody.innerHTML = paginated.map(s => {
        const badge = getStatusBadge(s.status);
        return `
            <tr>
                <td><strong>#${s.id}</strong></td>
                <td>${formatDate(s.date)}</td>
                <td>${s.seller || "—"}</td>
                <td><strong style="color: var(--text);">${s.productName || "—"}</strong></td>
                <td><strong>${s.qty}</strong></td>
                <td>${formatPrice(s.unitPrice)}</td>
                <td><strong>${formatPrice(s.totalPrice)}</strong></td>
                <td>${s.paymentMethod || "Mock"}</td>
                <td style="max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${s.note || ""}">${s.note || "—"}</td>
                <td><span class="badge ${badge.cls}">${badge.text}</span></td>
                <td>
                    <div class="table-actions-cell" style="justify-content: flex-end;">
                        <button class="action-btn edit-sale-btn" data-id="${s.id}" type="button" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
                        <button class="action-btn is-danger delete-sale-btn" data-id="${s.id}" type="button" aria-label="Supprimer"><i class="fa-solid fa-trash"></i></button>
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
    document.querySelectorAll(".edit-sale-btn").forEach(btn => {
        btn.addEventListener("click", () => openEditForm(Number(btn.dataset.id)));
    });
    document.querySelectorAll(".delete-sale-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            state.saleToDelete = Number(btn.dataset.id);
            deleteModalHandler.open();
        });
    });
};

const openEditForm = (id) => {
    const sale = mockDb.getSales().find(s => s.id === id);
    if (!sale) return;

    elements.modalTitle.textContent = "Modifier la vente";
    document.getElementById("saleId").value = sale.id;
    elements.productSelect.value = sale.productId;
    elements.qtyInput.value = sale.qty;
    elements.unitPriceInput.value = sale.unitPrice;
    elements.totalInput.value = sale.totalPrice;
    elements.sellerSelect.value = sale.seller;
    document.getElementById("sPayment").value = sale.paymentMethod;
    document.getElementById("sStatus").value = sale.status;
    document.getElementById("sNote").value = sale.note || "";

    saleModalHandler.open();
};

const validateForm = (data) => {
    let isValid = true;
    document.querySelectorAll("#saleForm .form-field").forEach(f => f.classList.remove("is-invalid"));
    document.querySelectorAll("#saleForm .field-error").forEach(e => e.textContent = "");

    const setError = (fieldId, message) => {
        const fieldEl = document.getElementById(fieldId).closest(".form-field");
        fieldEl.classList.add("is-invalid");
        const errorEl = fieldEl.querySelector(".field-error");
        if (errorEl) errorEl.textContent = message;
        isValid = false;
    };

    if (!data.productId) setError("sProduct", "Sélectionnez un produit.");
    if (!data.qty || Number(data.qty) <= 0) setError("sQty", "Quantité invalide.");
    if (isNaN(data.unitPrice) || Number(data.unitPrice) < 0) setError("sUnitPrice", "Prix unitaire invalide.");
    if (!data.seller) setError("sSeller", "Sélectionnez un vendeur.");
    if (!data.paymentMethod) setError("sPayment", "Sélectionnez un mode de paiement.");

    return isValid;
};

const handleFormSubmit = (e) => {
    e.preventDefault();

    // TODO PHP
    // TODO SQL

    const formData = new FormData(elements.form);
    const saleData = {
        id: formData.get("id") ? Number(formData.get("id")) : null,
        productId: formData.get("productId"),
        qty: formData.get("qty"),
        unitPrice: formData.get("unitPrice"),
        seller: formData.get("seller"),
        paymentMethod: formData.get("paymentMethod"),
        note: formData.get("note"),
        status: formData.get("status")
    };

    if (!validateForm(saleData)) {
        showToast("Veuillez corriger les erreurs de saisie.", "warning");
        return;
    }

    try {
        mockDb.saveSale(saleData);
        showToast(saleData.id ? "Vente mise à jour !" : "Vente enregistrée — stock mis à jour.", "success");
        saleModalHandler.close();
        initSelectors();
        loadSales();
        updateMetrics();
    } catch (err) {
        showToast(err.message || "Erreur lors de l'enregistrement.", "danger");
    }
};

const handleConfirmDelete = () => {
    // TODO PHP
    // TODO SQL

    if (state.saleToDelete) {
        const success = mockDb.deleteSale(state.saleToDelete);
        if (success) {
            showToast("Vente supprimée — stock réintégré.", "success");
            deleteModalHandler.close();
            state.saleToDelete = null;
            initSelectors();
            loadSales();
            updateMetrics();
        } else {
            showToast("Impossible de supprimer la vente.", "danger");
        }
    }
};

const bindEvents = () => {
    elements.search.addEventListener("input", (e) => {
        state.searchQuery = e.target.value;
        state.currentPage = 1;
        loadSales();
    });

    elements.filterStatus.addEventListener("change", (e) => {
        state.filterStatus = e.target.value;
        state.currentPage = 1;
        loadSales();
    });

    elements.filterPayment.addEventListener("change", (e) => {
        state.filterPayment = e.target.value;
        state.currentPage = 1;
        loadSales();
    });

    elements.filterSeller.addEventListener("change", (e) => {
        state.filterSeller = e.target.value;
        state.currentPage = 1;
        loadSales();
    });

    elements.sortSales.addEventListener("change", (e) => {
        state.sortBy = e.target.value;
        loadSales();
    });

    elements.prevBtn.addEventListener("click", () => {
        if (state.currentPage > 1) {
            state.currentPage--;
            renderTable();
        }
    });

    elements.nextBtn.addEventListener("click", () => {
        const pageCount = Math.ceil(state.sales.length / state.pageSize);
        if (state.currentPage < pageCount) {
            state.currentPage++;
            renderTable();
        }
    });

    saleModalHandler = initModal("saleModal", "addSaleBtn");

    document.getElementById("addSaleBtn").addEventListener("click", () => {
        elements.modalTitle.textContent = "Nouvelle vente";
        document.getElementById("saleId").value = "";
        elements.form.reset();
        elements.totalInput.value = "";
    });

    deleteModalHandler = initModal("deleteModal");
    elements.form.addEventListener("submit", handleFormSubmit);
    elements.confirmDeleteBtn.addEventListener("click", handleConfirmDelete);

    elements.productSelect.addEventListener("change", () => {
        const option = elements.productSelect.selectedOptions[0];
        if (option && option.dataset.price) {
            elements.unitPriceInput.value = option.dataset.price;
            updateTotalPreview();
        }
    });

    elements.qtyInput.addEventListener("input", updateTotalPreview);
    elements.unitPriceInput.addEventListener("input", updateTotalPreview);

    elements.exportBtn.addEventListener("click", () => {
        // TODO Export: Implémenter l'export CSV/PDF via api/ventes.php
        showToast("Export des ventes — fonctionnalité à venir.", "info");
    });
};

const init = () => {
    initSelectors();
    updateMetrics();
    loadSales();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
