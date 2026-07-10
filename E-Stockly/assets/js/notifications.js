/* E-Stockly Notifications JS Module */

"use strict";

import { mockDb } from "./mockDb.js";
import { showToast, updateNotificationBell } from "./components/components.js";

// Ensure mockDb is initialized
mockDb.init();

// State
let state = {
    notifications: [],
    currentPage: 1,
    pageSize: 8
};

const elements = {
    listContainer: document.getElementById("notificationsListContainer"),
    metricTotal: document.getElementById("metricTotalNotifs"),
    metricUnread: document.getElementById("metricUnreadNotifs"),
    markAllBtn: document.getElementById("markAllReadBtn"),
    prevBtn: document.getElementById("prevPageBtn"),
    nextBtn: document.getElementById("nextPageBtn"),
    pageStart: document.getElementById("pageStart"),
    pageEnd: document.getElementById("pageEnd"),
    totalCount: document.getElementById("totalCount")
};

const updateMetrics = () => {
    const notifs = mockDb.getNotifications();
    elements.metricTotal.textContent = notifs.length;
    elements.metricUnread.textContent = notifs.filter(n => !n.read).length;
};

const loadNotifications = () => {
    state.notifications = mockDb.getNotifications();
    renderList();
};

const renderList = () => {
    const total = state.notifications.length;

    if (total === 0) {
        elements.listContainer.innerHTML = `
            <div class="empty-state">
                <span class="empty-state-icon"><i class="fa-solid fa-bell-slash"></i></span>
                <h3>Aucune notification</h3>
                <p>Votre boîte de réception est vide.</p>
            </div>
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
    
    const paginated = state.notifications.slice(start, end);

    elements.listContainer.innerHTML = paginated.map(n => {
        let iconClass = "fa-circle-info";
        let stateClass = "is-info";
        
        if (n.type === "success") {
            iconClass = "fa-circle-check";
            stateClass = "is-success";
        } else if (n.type === "danger") {
            iconClass = "fa-triangle-exclamation";
            stateClass = "is-danger";
        } else if (n.type === "warning") {
            iconClass = "fa-circle-exclamation";
            stateClass = "is-warning";
        }

        const readAction = !n.read 
            ? `<button class="action-btn mark-read-btn" data-id="${n.id}" type="button" aria-label="Marquer comme lu"><i class="fa-solid fa-check"></i></button>`
            : `<span style="font-size: 0.8rem; color: var(--muted); font-weight: 700; padding-right: 6px;"><i class="fa-solid fa-circle-check" style="color: var(--success); margin-right: 4px;"></i>Lu</span>`;

        return `
            <div class="notification-item ${!n.read ? 'is-unread' : ''}">
                <div class="notification-item__left">
                    <span class="alert-icon ${stateClass}" style="width: 36px; height: 36px; border-radius: 12px; display: grid; place-items: center;"><i class="fa-solid ${iconClass}"></i></span>
                    <div class="notification-item__text">
                        <h3>${n.message}</h3>
                        <p>${n.time}</p>
                    </div>
                </div>
                <div>
                    ${readAction}
                </div>
            </div>
        `;
    }).join("");

    elements.pageStart.textContent = start + 1;
    elements.pageEnd.textContent = end;
    elements.totalCount.textContent = total;

    elements.prevBtn.disabled = state.currentPage === 1;
    elements.nextBtn.disabled = state.currentPage === pageCount;

    bindListButtons();
};

const bindListButtons = () => {
    document.querySelectorAll(".mark-read-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = Number(btn.dataset.id);
            // TODO PHP: api/notifications.php
            // TODO SQL: UPDATE notifications SET read = 1 WHERE id = :id
            
            mockDb.markNotificationRead(id);
            showToast("Notification marquée comme lue.", "success");
            loadNotifications();
            updateMetrics();
            updateNotificationBell();
        });
    });
};

const handleMarkAllRead = () => {
    // TODO PHP: api/notifications.php
    // TODO SQL: UPDATE notifications SET read = 1

    mockDb.markAllNotificationsRead();
    showToast("Toutes les notifications sont marquées comme lues.", "success");
    loadNotifications();
    updateMetrics();
    updateNotificationBell();
};

const bindEvents = () => {
    elements.markAllBtn.addEventListener("click", handleMarkAllRead);

    elements.prevBtn.addEventListener("click", () => {
        if (state.currentPage > 1) {
            state.currentPage--;
            renderList();
        }
    });

    elements.nextBtn.addEventListener("click", () => {
        const pageCount = Math.ceil(state.notifications.length / state.pageSize);
        if (state.currentPage < pageCount) {
            state.currentPage++;
            renderList();
        }
    });
};

const init = () => {
    updateMetrics();
    loadNotifications();
    bindEvents();
};

document.addEventListener("DOMContentLoaded", init);
