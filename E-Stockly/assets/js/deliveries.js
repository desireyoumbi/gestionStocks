/* E-Stockly Deliveries JS Module */

"use strict";

import { mockDb } from "./mockDb.js";
import { showToast, initModal } from "./components/components.js";

// Ensure mockDb is initialized
mockDb.init();

// State
let state = {
    deliveries: [],
    suppliers: [],
    products: [],
    currentPage: 1,
    pageSize: 10,
    searchQuery: "",
    deliveryToReceive: null
};

// Modals
let deliveryModalHandler;
let receiptModalHandler;

const elements = {
    tableBody: document.querySelector("#deliveriesTable tbody"),
    metricTotal: document.getElementById("metricTotalDeliveries"),
    metricPending: document.getElementById("metricPendingDeliveries"),
    metricReceived: document.getElementById("metricReceivedDeliveries"),
    search: document.getElementById("deliverySearch"),
    prevBtn: document.getElementById("prevPageBtn"),
    nextBtn: document.getElementById("nextPageBtn"),
    pageStart: document.getElementById("pageStart"),
    pageEnd: document.getElementById("pageEnd"),
    totalCount: document.getElementById("totalCount"),
    form: document.getElementById("deliveryForm"),
    supplierSelect: document.getElementById("dSupplier"),
    itemsList: document.getElementById("itemsSelectionList"),
    btnConfirmReceipt: document.getElementById("btnConfirmReceipt")
};

const updateMetrics = () => {
    const deliveries = mockDb.getDeliveries();
    
    const total = deliveries.length;
    const pending = deliveries.filter(d => d.status === "pending").length;
    const received = deliveries.filter(d => d.status === "received").length;

    elements.metricTotal.textContent = total;
    elements.metricPending.textContent = pending;
    elements.metricReceived.textContent = received;
};

const initSelectors = () => {
    state.suppliers = mockDb.getSuppliers();
    state.products = mockDb.getProducts();

    // Suppliers select
    elements.supplierSelect.innerHTML = '<option value="">Choisir un fournisseur</option>' +
        state.suppliers.map(s => `<option value="${s.id}">${s.name}</option>`).join("");

    // Products selection list
    elements.itemsList.innerHTML = state.products.map(p => `
        <div class="delivery-item-row">
            <label class="delivery-item-label">
                <input type="checkbox" class="product-item-chk form-control" value="${p.id}">
                <span>${p.name} <small style="color: var(--muted);">(Stock actuel: ${p.stock})</small></span>
            </label>
            <input type="number" class="product-item-qty form-control" placeholder="Qté" min="1" disabled>
        </div>
    `).join("");

    // Toggle qty input state depending on checkbox
    elements.itemsList.querySelectorAll(".product-item-chk").forEach((chk, i) => {
        const qtyInput = elements.itemsList.querySelectorAll(".product-item-qty")[i];
        chk.addEventListener("change", () => {
            qtyInput.disabled = !chk.checked;
            if (chk.checked) {
                qtyInput.value = 10;
                qtyInput.focus();
            } else {
                qtyInput.value = "";
            }
        });
    });
};

const loadDeliveries = () => {
    let filtered = mockDb.getDeliveries();

    if (state.searchQuery) {
        const query = state.searchQuery.toLowerCase();
        filtered = filtered.filter(d => 
            d.supplierName.toLowerCase().includes(query) || 
            String(d.id).includes(query)
        );
    }

    state.deliveries = filtered;
    renderTable();
};

const renderTable = () => {
    const total = state.deliveries.length;

    if (total === 0) {
        elements.tableBody.innerHTML = `
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <span class="empty-state-icon"><i class="fa-solid fa-truck"></i></span>
                        <h3>Aucune livraison</h3>
                        <p>Créez une nouvelle commande fournisseur pour réapprovisionner vos stocks.</p>
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
    
    const paginated = state.deliveries.slice(start, end);

    elements.tableBody.innerHTML = paginated.map(d => {
        const itemQty = d.items.reduce((sum, item) => sum + item.qty, 0);
        const uniqueItems = d.items.length;
        
        let badgeClass = "is-warning";
        let badgeText = "En attente";
        let actionBtn = `<button class="btn btn-success receive-btn" data-id="${d.id}" type="button"><i class="fa-solid fa-check"></i>Réceptionner</button>`;

        if (d.status === "received") {
            badgeClass = "is-success";
            badgeText = "Reçue";
            actionBtn = `<span style="font-size: 0.8rem; color: var(--muted); font-weight: 700;"><i class="fa-solid fa-circle-check" style="color: var(--success); margin-right: 4px;"></i>Traité</span>`;
        }

        // Format Date
        let dateStr = d.dateExpected;
        try {
            const dateObj = new Date(d.dateExpected);
            if (!isNaN(dateObj.getTime())) {
                dateStr = dateObj.toLocaleDateString("fr-FR");
            }
        } catch (e) {}

        return `
            <tr>
                <td><strong>#${d.id}</strong></td>
                <td><strong style="color: var(--text);">${d.supplierName}</strong></td>
                <td>${dateStr}</td>
                <td><strong>${uniqueItems}</strong> article(s) (${itemQty} unités)</td>
                <td><span class="badge ${badgeClass}">${badgeText}</span></td>
                <td style="text-align: right;">${actionBtn}</td>
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
    document.querySelectorAll(".receive-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            state.deliveryToReceive = Number(btn.dataset.id);
            receiptModalHandler.open();
        });
    });
};

const handleFormSubmit = (e) => {
    e.preventDefault();

    // TODO PHP: Envoyer la livraison vers api/livraisons.php
    // TODO SQL: INSERT INTO livraisons

    const supplierId = elements.supplierSelect.value;
    const dateExpected = document.getElementById("dDate").value;

    // Collect selected items
    const selectedItems = [];
    elements.itemsList.querySelectorAll(".product-item-chk").forEach((chk, idx) => {
        if (chk.checked) {
            const qtyInput = elements.itemsList.querySelectorAll(".product-item-qty")[idx];
            selectedItems.push({
                productId: Number(chk.value),
                qty: Number(qtyInput.value) || 1
            });
        }
    });

    // Validation
    let isValid = true;
    document.querySelectorAll(".form-field").forEach(f => f.classList.remove("is-invalid"));
    document.querySelectorAll(".field-error").forEach(e => e.textContent = "");

    if (!supplierId) {
        const f = elements.supplierSelect.closest(".form-field");
        f.classList.add("is-invalid");
        f.querySelector(".field-error").textContent = "Veuillez sélectionner un fournisseur.";
        isValid = false;
    }
    if (!dateExpected) {
        const f = document.getElementById("dDate").closest(".form-field");
        f.classList.add("is-invalid");
        f.querySelector(".field-error").textContent = "Veuillez saisir une date attendue.";
        isValid = false;
    }
    if (selectedItems.length === 0) {
        showToast("Veuillez sélectionner au moins un article.", "warning");
        isValid = false;
    }

    if (!isValid) return;

    try {
        mockDb.saveDelivery({
            supplierId: Number(supplierId),
            dateExpected,
            items: selectedItems
        });
        showToast("Commande fournisseur enregistrée !", "success");
        deliveryModalHandler.close();
        loadDeliveries();
        updateMetrics();
    } catch (err) {
        showToast("Erreur lors de l'enregistrement.", "danger");
    }
};

const handleConfirmReceipt = () => {
    // TODO PHP: Valider la réception dans api/livraisons.php
    // TODO SQL: UPDATE livraisons SET status = 'received'

    if (state.deliveryToReceive) {
        const success = mockDb.receiveDelivery(state.deliveryToReceive);
        if (success) {
            showToast("Réception enregistrée et stocks incrémentés !", "success");
            receiptModalHandler.close();
            state.deliveryToReceive = null;
            loadDeliveries();
            updateMetrics();
        } else {
            showToast("Impossible de réceptionner.", "danger");
        }
    }
};

const bindEvents = () => {
    elements.search.addEventListener("input", (e) => {
        state.searchQuery = e.target.value;
        state.currentPage = 1;
        loadDeliveries();
    });

    elements.prevBtn.addEventListener("click", () => {
        if (state.currentPage > 1) {
            state.currentPage--;
            renderTable();
        }
    });

    elements.nextBtn.addEventListener("click", () => {
        const pageCount = Math.ceil(state.deliveries.length / state.pageSize);
        if (state.currentPage < pageCount) {
            state.currentPage++;
            renderTable();
        }
    });

    deliveryModalHandler = initModal("deliveryModal", "addDeliveryBtn");
    receiptModalHandler = initModal("confirmReceiptModal");

    elements.form.addEventListener("submit", handleFormSubmit);
    elements.btnConfirmReceipt.addEventListener("click", handleConfirmReceipt);
};

const init = () => {
    initSelectors();
    updateMetrics();
    loadDeliveries();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
