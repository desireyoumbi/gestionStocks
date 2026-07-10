/* E-Stockly History JS Module */

"use strict";

import { mockDb } from "./mockDb.js";

// Ensure mockDb is initialized
mockDb.init();

// State
let state = {
    logs: [],
    currentPage: 1,
    pageSize: 10,
    searchQuery: "",
    selectedModule: ""
};

const elements = {
    tableBody: document.querySelector("#historyTable tbody"),
    metricTotal: document.getElementById("metricTotalLogs"),
    metricOperators: document.getElementById("metricActiveOperators"),
    search: document.getElementById("historySearch"),
    filterModule: document.getElementById("filterModule"),
    prevBtn: document.getElementById("prevPageBtn"),
    nextBtn: document.getElementById("nextPageBtn"),
    pageStart: document.getElementById("pageStart"),
    pageEnd: document.getElementById("pageEnd"),
    totalCount: document.getElementById("totalCount")
};

const updateMetrics = () => {
    const logs = mockDb.getHistory();
    elements.metricTotal.textContent = logs.length;
    
    // Calculate unique active operators
    const operators = new Set(logs.map(l => l.user));
    elements.metricOperators.textContent = operators.size;
};

const loadHistory = () => {
    let filtered = mockDb.getHistory();

    if (state.searchQuery) {
        const query = state.searchQuery.toLowerCase();
        filtered = filtered.filter(l => 
            l.action.toLowerCase().includes(query) || 
            l.user.toLowerCase().includes(query)
        );
    }

    if (state.selectedModule) {
        filtered = filtered.filter(l => l.module === state.selectedModule);
    }

    state.logs = filtered;
    renderTable();
};

const renderTable = () => {
    const total = state.logs.length;

    if (total === 0) {
        elements.tableBody.innerHTML = `
            <tr>
                <td colspan="4">
                    <div class="empty-state">
                        <span class="empty-state-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                        <h3>Aucune action enregistrée</h3>
                        <p>Le journal des audits est vide pour ces critères.</p>
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
    
    const paginated = state.logs.slice(start, end);

    elements.tableBody.innerHTML = paginated.map(l => {
        // Format timestamp
        let dateStr = l.time;
        try {
            const dateObj = new Date(l.time);
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
                <td style="color: var(--muted);">${dateStr}</td>
                <td><strong>${l.user}</strong></td>
                <td>${l.action}</td>
                <td><span class="badge is-info">${l.module}</span></td>
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
    elements.search.addEventListener("input", (e) => {
        state.searchQuery = e.target.value;
        state.currentPage = 1;
        loadHistory();
    });

    elements.filterModule.addEventListener("change", (e) => {
        state.selectedModule = e.target.value;
        state.currentPage = 1;
        loadHistory();
    });

    elements.prevBtn.addEventListener("click", () => {
        if (state.currentPage > 1) {
            state.currentPage--;
            renderTable();
        }
    });

    elements.nextBtn.addEventListener("click", () => {
        const pageCount = Math.ceil(state.logs.length / state.pageSize);
        if (state.currentPage < pageCount) {
            state.currentPage++;
            renderTable();
        }
    });
};

const init = () => {
    updateMetrics();
    loadHistory();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
