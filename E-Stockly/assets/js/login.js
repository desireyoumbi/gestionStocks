"use strict";

const mockUser = {
    email: "admin@ngstock.com",
    password: "Admin123",
    role: "admin",
    fullname: "Administrateur"
};

const loginState = {
    isSubmitting: false
};

const selectors = {
    form: "#loginForm",
    email: "#email",
    password: "#password",
    togglePassword: "#togglePassword",
    submitButton: "#submitButton",
    formAlert: "#formAlert",
    emailError: "#emailError",
    passwordError: "#passwordError"
};

const messages = {
    requiredEmail: "Veuillez saisir votre adresse email.",
    invalidEmail: "Veuillez saisir une adresse email valide.",
    requiredPassword: "Veuillez saisir votre mot de passe.",
    invalidCredentials: "Identifiants incorrects. Verifiez votre email et votre mot de passe.",
    success: "Connexion reussie. Redirection en cours..."
};

const getElement = (selector) => document.querySelector(selector);

const elements = {
    form: getElement(selectors.form),
    email: getElement(selectors.email),
    password: getElement(selectors.password),
    togglePassword: getElement(selectors.togglePassword),
    submitButton: getElement(selectors.submitButton),
    formAlert: getElement(selectors.formAlert),
    emailError: getElement(selectors.emailError),
    passwordError: getElement(selectors.passwordError)
};

const setFieldError = (field, message = "") => {
    const errorElement = field === "email" ? elements.emailError : elements.passwordError;
    const inputElement = field === "email" ? elements.email : elements.password;

    errorElement.textContent = message;
    inputElement.setAttribute("aria-invalid", message ? "true" : "false");
};

const showAlert = (message, type = "error") => {
    elements.formAlert.textContent = message;
    elements.formAlert.className = `form-alert is-visible is-${type}`;
};

const clearAlert = () => {
    elements.formAlert.textContent = "";
    elements.formAlert.className = "form-alert";
};

const setLoading = (isLoading) => {
    loginState.isSubmitting = isLoading;
    elements.submitButton.disabled = isLoading;
    elements.submitButton.classList.toggle("is-loading", isLoading);
    elements.submitButton.setAttribute("aria-busy", String(isLoading));
};

const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

const getCredentials = () => ({
    email: elements.email.value.trim().toLowerCase(),
    password: elements.password.value.trim()
});

const validateForm = ({ email, password }) => {
    let isValid = true;

    clearAlert();
    setFieldError("email");
    setFieldError("password");

    if (!email) {
        setFieldError("email", messages.requiredEmail);
        isValid = false;
    } else if (!isValidEmail(email)) {
        setFieldError("email", messages.invalidEmail);
        isValid = false;
    }

    if (!password) {
        setFieldError("password", messages.requiredPassword);
        isValid = false;
    }

    return isValid;
};

const authenticateMockUser = ({ email, password }) => (
    email === mockUser.email.toLowerCase() && password === mockUser.password
);

const redirectToDashboard = () => {
    window.location.href = "dashboard.php";
};

const handlePasswordToggle = () => {
    const isPasswordVisible = elements.password.type === "text";

    elements.password.type = isPasswordVisible ? "password" : "text";
    elements.togglePassword.setAttribute("aria-label", isPasswordVisible ? "Afficher le mot de passe" : "Masquer le mot de passe");
    elements.togglePassword.setAttribute("aria-pressed", String(!isPasswordVisible));
    elements.togglePassword.innerHTML = isPasswordVisible
        ? '<i class="fa-solid fa-eye" aria-hidden="true"></i>'
        : '<i class="fa-solid fa-eye-slash" aria-hidden="true"></i>';
};

const handleSubmit = (event) => {
    event.preventDefault();

    if (loginState.isSubmitting) {
        return;
    }

    const credentials = getCredentials();

    if (!validateForm(credentials)) {
        showAlert("Veuillez corriger les erreurs du formulaire.");
        return;
    }

    setLoading(true);

    // TODO:
    // Envoyer les donnees vers api/login.php

    window.setTimeout(() => {
        const isAuthenticated = authenticateMockUser(credentials);

        // TODO:
        // Gerer la reponse JSON

        if (!isAuthenticated) {
            setLoading(false);
            showAlert(messages.invalidCredentials);
            return;
        }

        showAlert(messages.success, "success");

        // TODO:
        // Sauvegarder la session

        window.setTimeout(() => {
            // TODO:
            // Redirection selon le role
            redirectToDashboard();
        }, 1200);
    }, 1500);
};

const bindEvents = () => {
    elements.togglePassword.addEventListener("click", handlePasswordToggle);
    elements.form.addEventListener("submit", handleSubmit);

    elements.email.addEventListener("input", () => setFieldError("email"));
    elements.password.addEventListener("input", () => setFieldError("password"));
};

document.addEventListener("DOMContentLoaded", bindEvents);
