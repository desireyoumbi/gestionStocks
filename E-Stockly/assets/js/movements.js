/* E-Stockly Movements JS Module */

"use strict";

import { mockDb } from "./mockDb.js";

// Ensure mockDb is initialized
mockDb.init();

// State management
let state = {
    movements: [],
    currentPage: 1,
    pageSize: 10,
    searchQuery: "",
    selectedType: ""
};

const elements = {
    tableBody: document.querySelector("#movementsTable tbody"),
    metricIn: document.getElementById("metricTotalIn"),
    metricOut: document.getElementById("metricTotalOut"),
    metricRecent: document.getElementById("metricRecentCount"),
    search: document.getElementById("movementSearch"),
    filterType: document.getElementById("filterType"),
    prevBtn: document.getElementById("prevPageBtn"),
    nextBtn: document.getElementById("nextPageBtn"),
    pageStart: document.getElementById("pageStart"),
    pageEnd: document.getElementById("pageEnd"),
    totalCount: document.getElementById("totalCount")
};

const updateMetrics = () => {
    const movements = mockDb.getStockMovements();
    
    const totalIn = movements.filter(m => m.type === "in").reduce((sum, m) => sum + m.qty, 0);
    const totalOut = movements.filter(m => m.type === "out").reduce((sum, m) => sum + m.qty, 0);
    const recent = movements.length;

    elements.metricIn.textContent = totalIn;
    elements.metricOut.textContent = totalOut;
    elements.metricRecent.textContent = recent;
};

const loadMovements = () => {
    let filtered = mockDb.getStockMovements();

    // Search query
    if (state.searchQuery) {
        const query = state.searchQuery.toLowerCase();
        filtered = filtered.filter(m => 
            m.productName.toLowerCase().includes(query) || 
            (m.comment && m.comment.toLowerCase().includes(query)) ||
            (m.user && m.user.toLowerCase().includes(query))
        );
    }

    // Type filter
    if (state.selectedType) {
        filtered = filtered.filter(m => m.type === state.selectedType);
    }

    state.movements = filtered;
    renderTable();
};

const renderTable = () => {
    const total = state.movements.length;

    if (total === 0) {
        elements.tableBody.innerHTML = `
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <span class="empty-state-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                        <h3>Aucun mouvement</h3>
                        <p>Il n'y a pas d'activité de stock correspondant à votre recherche.</p>
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
    
    const paginated = state.movements.slice(start, end);

    elements.tableBody.innerHTML = paginated.map(m => {
        let badgeClass = "is-success";
        let badgeText = "Entrée";

        if (m.type === "out") {
            badgeClass = "is-danger";
            badgeText = "Sortie";
        } else if (m.type === "adj") {
            badgeClass = "is-warning";
            badgeText = "Correction";
        }

        // Format Date
        let dateStr = m.date;
        try {
            const dateObj = new Date(m.date);
            if (!isNaN(dateObj.getTime())) {
                dateStr = dateObj.toLocaleDateString("fr-FR", {
                    day: "2-digit",
                    month: "2-digit",
                    year: "numeric"
                }) + " " + dateObj.toLocaleTimeString("fr-FR", { hour: "2-digit", minute: "2-digit" });
            }
        } catch (e) {}

        return `
            <tr>
                <td>${dateStr}</td>
                <td><strong style="color: var(--text);">${m.productName}</strong></td>
                <td><span class="badge ${badgeClass}">${badgeText}</span></td>
                <td><strong>${m.type === 'in' ? '+' : '-'}${m.qty}</strong></td>
                <td><span style="font-weight: 700;">${m.user}</span></td>
                <td style="color: var(--muted);">${m.comment || "—"}</td>
            </tr>
        `;
    }).join("");

    elements.pageStart.textContent = start + 1;
    elements.pageEnd.textContent = end;
    elements.totalCount.textContent = total;

    elements.prevBtn.disabled = state.currentPage === 1;
    elements.nextBtn.disabled = state.currentPage === pageCount;
};

const bindEvents = () => {
    // Search input
    elements.search.addEventListener("input", (e) => {
        state.searchQuery = e.target.value;
        state.currentPage = 1;
        loadMovements();
    });

    // Type filter
    elements.filterType.addEventListener("change", (e) => {
        state.selectedType = e.target.value;
        state.currentPage = 1;
        loadMovements();
    });

    // Pagination
    elements.prevBtn.addEventListener("click", () => {
        if (state.currentPage > 1) {
            state.currentPage--;
            renderTable();
        }
    });

    elements.nextBtn.addEventListener("click", () => {
        const pageCount = Math.ceil(state.movements.length / state.pageSize);
        if (state.currentPage < pageCount) {
            state.currentPage++;
            renderTable();
        }
    });
};

const init = () => {
    updateMetrics();
    loadMovements();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
