/* E-Stockly Shared Components Behavior */

"use strict";

import { mockDb } from "../mockDb.js";

// Ensure mockDb is initialized
mockDb.init();

// Toast Notifications System
export const showToast = (message, type = "info") => {
    let container = document.querySelector(".toast-container");
    if (!container) {
        container = document.createElement("div");
        container.className = "toast-container";
        document.body.appendChild(container);
    }

    const toast = document.createElement("div");
    toast.className = `toast is-${type}`;
    
    let iconClass = "fa-circle-info";
    if (type === "success") iconClass = "fa-circle-check";
    if (type === "danger") iconClass = "fa-triangle-exclamation";
    if (type === "warning") iconClass = "fa-circle-exclamation";

    toast.innerHTML = `
        <span class="toast-icon"><i class="fa-solid ${iconClass}"></i></span>
        <div class="toast-content">
            <p class="toast-message">${message}</p>
        </div>
        <button class="toast-close" type="button" aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>
    `;

    container.appendChild(toast);

    // Fade/Slide In
    window.requestAnimationFrame(() => {
        toast.classList.add("is-active");
    });

    const closeToast = () => {
        toast.classList.remove("is-active");
        toast.addEventListener("transitionend", () => {
            toast.remove();
        });
    };

    // Close button
    toast.querySelector(".toast-close").addEventListener("click", closeToast);

    // Auto-remove after 4 seconds
    setTimeout(closeToast, 4000);
};

// Modals management
export const initModal = (overlayId, triggerBtnId = null) => {
    const overlay = document.getElementById(overlayId);
    if (!overlay) return null;

    const modal = overlay.querySelector(".modal-card");
    const closeBtns = overlay.querySelectorAll("[data-close-modal], .modal-close");

    const open = () => {
        overlay.classList.add("is-active");
        if (modal) {
            modal.setAttribute("tabindex", "-1");
            modal.focus();
        }
    };

    const close = () => {
        overlay.classList.remove("is-active");
        // Clear forms inside if any
        const form = overlay.querySelector("form");
        if (form) {
            form.reset();
            overlay.querySelectorAll(".form-field").forEach(f => f.classList.remove("is-invalid"));
            overlay.querySelectorAll(".field-error").forEach(e => e.textContent = "");
        }
    };

    if (triggerBtnId) {
        const triggers = document.querySelectorAll(`#${triggerBtnId}, .${triggerBtnId}`);
        triggers.forEach(trigger => {
            trigger.addEventListener("click", open);
        });
    }

    closeBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            close();
        });
    });

    // Close on overlay background click
    overlay.addEventListener("click", (e) => {
        if (e.target === overlay) {
            close();
        }
    });

    // Keyboard navigation (Esc key)
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && overlay.classList.contains("is-active")) {
            close();
        }
    });

    return { open, close };
};

// Global layouts (Sidebar and dropdown menu)
document.addEventListener("DOMContentLoaded", () => {
    // Sidebar toggle (for responsive / mobile burger menu)
    const shell = document.getElementById("dashboardShell");
    const menuToggle = document.getElementById("menuToggle");
    const backdrop = document.getElementById("sidebarBackdrop");

    const setSidebarState = (isOpen) => {
        if (shell && menuToggle && backdrop) {
            shell.classList.toggle("is-sidebar-open", isOpen);
            menuToggle.setAttribute("aria-expanded", String(isOpen));
            menuToggle.setAttribute("aria-label", isOpen ? "Fermer le menu" : "Ouvrir le menu");
            backdrop.hidden = !isOpen;
        }
    };

    if (menuToggle) {
        menuToggle.addEventListener("click", () => {
            const isOpen = shell.classList.contains("is-sidebar-open");
            setSidebarState(!isOpen);
        });
    }

    if (backdrop) {
        backdrop.addEventListener("click", () => setSidebarState(false));
    }

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            setSidebarState(false);
        }
    });

    // User Profile Dropdown Menu in topbar
    const userMenuBtn = document.getElementById("userMenuBtn");
    const userDropdown = document.getElementById("userDropdown");

    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            const isActive = userDropdown.classList.contains("is-active");
            userDropdown.classList.toggle("is-active", !isActive);
            userMenuBtn.setAttribute("aria-expanded", String(!isActive));
        });

        document.addEventListener("click", (e) => {
            if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove("is-active");
                userMenuBtn.setAttribute("aria-expanded", "false");
            }
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                userDropdown.classList.remove("is-active");
                userMenuBtn.setAttribute("aria-expanded", "false");
            }
        });
    }

    // Auto-update notification bell counter
    updateNotificationBell();
});

// Update topbar notification bell state based on unread counts
export const updateNotificationBell = () => {
    const bellBtn = document.querySelector(".topbar-actions .icon-button.has-dot");
    if (!bellBtn) return;
    
    const unreadCount = mockDb.getNotifications().filter(n => !n.read).length;
    if (unreadCount > 0) {
        bellBtn.classList.add("has-dot");
        bellBtn.setAttribute("aria-label", `Notifications (${unreadCount} non lues)`);
    } else {
        bellBtn.classList.remove("has-dot");
        bellBtn.setAttribute("aria-label", "Notifications");
    }
};
