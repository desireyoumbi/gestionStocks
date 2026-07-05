/* E-Stockly Mock Database Layer (localStorage) */

"use strict";

const DB_KEYS = {
    PRODUCTS: "estockly_products",
    CATEGORIES: "estockly_categories",
    SUPPLIERS: "estockly_suppliers",
    CLIENTS: "estockly_clients",
    DELIVERIES: "estockly_deliveries",
    USERS: "estockly_users",
    ROLES: "estockly_roles",
    HISTORY: "estockly_history",
    NOTIFICATIONS: "estockly_notifications",
    SETTINGS: "estockly_settings",
    MOVEMENTS: "estockly_movements"
};

// Initial Data
const initialCategories = [
    { id: 1, name: "Ordinateurs", description: "Ordinateurs de bureau et portables" },
    { id: 2, name: "Imprimantes", description: "Imprimantes laser et jet d'encre" },
    { id: 3, name: "Stockage", description: "Disques durs externes, SSD et clés USB" },
    { id: 4, name: "Réseau", description: "Routeurs, switchs et câbles réseau" },
    { id: 5, name: "Accessoires", description: "Souris, claviers, adaptateurs" },
    { id: 6, name: "Énergie", description: "Onduleurs, multiprises, batteries" }
];

const initialProducts = [
    { id: 1, name: "HP EliteBook 840", categoryId: 1, stock: 4, minStock: 15, price: 500000, purchasePrice: 400000, barcode: "19485038291", supplierId: 1 },
    { id: 2, name: "Canon LBP 6030w", categoryId: 2, stock: 7, minStock: 12, price: 150000, purchasePrice: 110000, barcode: "49204859201", supplierId: 2 },
    { id: 3, name: "SSD Kingston A400 1To", categoryId: 3, stock: 8, minStock: 20, price: 100000, purchasePrice: 75000, barcode: "74028402849", supplierId: 1 },
    { id: 4, name: "Routeur TP-Link C80", categoryId: 4, stock: 3, minStock: 10, price: 80000, purchasePrice: 60000, barcode: "69348509382", supplierId: 2 },
    { id: 5, name: "Souris Logitech Pebble", categoryId: 5, stock: 11, minStock: 25, price: 15000, purchasePrice: 10000, barcode: "09785409823", supplierId: 1 },
    { id: 6, name: "Onduleur APC 950VA", categoryId: 6, stock: 2, minStock: 8, price: 95000, purchasePrice: 70000, barcode: "07849028402", supplierId: 2 },
    { id: 7, name: "Dell Latitude 5440", categoryId: 1, stock: 22, minStock: 10, price: 650000, purchasePrice: 500000, barcode: "29481029482", supplierId: 1 }
];

const initialSuppliers = [
    { id: 1, name: "Global Tech Distributors", contact: "Marc Dupont", phone: "+221 77 123 45 67", email: "contact@globaltech.com", address: "Dakar, Sénégal" },
    { id: 2, name: "Africa Computer Wholesale", contact: "Aminata Diallo", phone: "+221 78 987 65 43", email: "sales@africacomputer.com", address: "Dakar, Sénégal" }
];

const initialClients = [
    { id: 1, name: "Sonatel SA", email: "contact@sonatel.sn", phone: "+221 33 839 00 00", address: "Dakar, Sénégal" },
    { id: 2, name: "Cabinet Ndiaye & Associés", email: "ndiaye@ndiayeassocies.com", phone: "+221 77 500 11 22", address: "Thies, Sénégal" }
];

const initialDeliveries = [
    { id: 1001, supplierId: 1, dateExpected: "2026-07-15", status: "pending", items: [{ productId: 1, qty: 10 }, { productId: 3, qty: 15 }] }
];

const initialUsers = [
    { id: 1, name: "Administrateur", email: "admin@ngstock.com", role: "Admin", status: "active" },
    { id: 2, name: "Manager Stock", email: "manager@ngstock.com", role: "Manager", status: "active" },
    { id: 3, name: "Agent Ventes", email: "sales@ngstock.com", role: "Vendeur", status: "active" }
];

const initialRoles = [
    { id: 1, name: "Admin", description: "Accès total au système, gestion des utilisateurs, rôles, configurations et rapports.", permissions: ["products", "categories", "stock", "movements", "suppliers", "clients", "deliveries", "users", "roles", "history", "settings"] },
    { id: 2, name: "Manager", description: "Gestion des stocks, mouvements, fournisseurs et rapports.", permissions: ["products", "categories", "stock", "movements", "suppliers", "deliveries"] },
    { id: 3, name: "Vendeur", description: "Consultation des produits, enregistrement des ventes et clients.", permissions: ["products", "clients"] }
];

const initialMovements = [
    { id: 1, productId: 7, type: "in", qty: 22, date: "2026-07-05T09:15:00", user: "Administrateur", comment: "Stock initial Dell Latitude 5440" },
    { id: 2, productId: 1, type: "out", qty: 1, date: "2026-07-05T10:05:00", user: "Administrateur", comment: "Vente en ligne #V-1049" },
    { id: 3, productId: 5, type: "in", qty: 40, date: "2026-07-05T11:30:00", user: "Administrateur", comment: "Réception livraison" }
];

const initialNotifications = [
    { id: 1, message: "12 produits sous le seuil critique", type: "danger", read: false, time: "Il y a 5 min" },
    { id: 2, message: "Les ventes augmentent de 18%", type: "success", read: false, time: "Il y a 2 heures" },
    { id: 3, message: "Le produit le plus vendu est HP EliteBook", type: "info", read: true, time: "Aujourd'hui" }
];

const initialHistory = [
    { id: 1, time: "2026-07-05T09:15:00", user: "Administrateur", action: "Ajout produit Dell Latitude 5440", module: "Produits" },
    { id: 2, time: "2026-07-05T10:05:00", user: "Administrateur", action: "Vente validée", module: "Ventes" },
    { id: 3, time: "2026-07-05T11:30:00", user: "Administrateur", action: "Entrée de stock : 40 accessoires", module: "Stock" }
];

const initialSettings = {
    companyName: "E-Stockly NG-STOCK",
    address: "Dakar Plateau, Sénégal",
    phone: "+221 33 123 45 67",
    email: "contact@ngstock.com",
    currency: "FCFA",
    backupInterval: "weekly"
};

// LocalStorage helpers
const readFromStorage = (key) => JSON.parse(localStorage.getItem(key));
const writeToStorage = (key, data) => localStorage.setItem(key, JSON.stringify(data));

export const mockDb = {
    init() {
        if (!localStorage.getItem(DB_KEYS.SETTINGS)) {
            writeToStorage(DB_KEYS.CATEGORIES, initialCategories);
            writeToStorage(DB_KEYS.PRODUCTS, initialProducts);
            writeToStorage(DB_KEYS.SUPPLIERS, initialSuppliers);
            writeToStorage(DB_KEYS.CLIENTS, initialClients);
            writeToStorage(DB_KEYS.DELIVERIES, initialDeliveries);
            writeToStorage(DB_KEYS.USERS, initialUsers);
            writeToStorage(DB_KEYS.ROLES, initialRoles);
            writeToStorage(DB_KEYS.MOVEMENTS, initialMovements);
            writeToStorage(DB_KEYS.NOTIFICATIONS, initialNotifications);
            writeToStorage(DB_KEYS.HISTORY, initialHistory);
            writeToStorage(DB_KEYS.SETTINGS, initialSettings);
        }
        this.checkLowStocks();
    },

    // Checking stock thresholds to generate warnings/notifications
    checkLowStocks() {
        const products = this.getProducts();
        const lowStockCount = products.filter(p => p.stock <= p.minStock).length;
        
        if (lowStockCount > 0) {
            const currentNotifs = readFromStorage(DB_KEYS.NOTIFICATIONS) || [];
            const lowStockNotif = currentNotifs.find(n => n.message.includes("produits sous le seuil critique"));
            if (lowStockNotif) {
                lowStockNotif.message = `${lowStockCount} produits sous le seuil critique`;
                lowStockNotif.read = false;
                writeToStorage(DB_KEYS.NOTIFICATIONS, currentNotifs);
            } else {
                this.addNotification(`${lowStockCount} produits sous le seuil critique`, "danger");
            }
        }
    },

    // History & Logs
    getHistory() {
        return readFromStorage(DB_KEYS.HISTORY) || [];
    },
    addHistory(action, module, user = "Administrateur") {
        const history = this.getHistory();
        const entry = {
            id: history.length ? Math.max(...history.map(h => h.id)) + 1 : 1,
            time: new Date().toISOString(),
            user,
            action,
            module
        };
        history.unshift(entry);
        writeToStorage(DB_KEYS.HISTORY, history);
        return entry;
    },

    // Notifications
    getNotifications() {
        return readFromStorage(DB_KEYS.NOTIFICATIONS) || [];
    },
    addNotification(message, type = "info") {
        const notifications = this.getNotifications();
        const entry = {
            id: notifications.length ? Math.max(...notifications.map(n => n.id)) + 1 : 1,
            message,
            type,
            read: false,
            time: "À l'instant"
        };
        notifications.unshift(entry);
        writeToStorage(DB_KEYS.NOTIFICATIONS, notifications);
        return entry;
    },
    markAllNotificationsRead() {
        const notifications = this.getNotifications();
        notifications.forEach(n => n.read = true);
        writeToStorage(DB_KEYS.NOTIFICATIONS, notifications);
    },
    markNotificationRead(id) {
        const notifications = this.getNotifications();
        const notif = notifications.find(n => n.id === id);
        if (notif) {
            notif.read = true;
            writeToStorage(DB_KEYS.NOTIFICATIONS, notifications);
        }
    },

    // Products
    getProducts() {
        const products = readFromStorage(DB_KEYS.PRODUCTS) || [];
        const categories = this.getCategories();
        const suppliers = this.getSuppliers();
        return products.map(p => {
            const cat = categories.find(c => c.id === Number(p.categoryId));
            const sup = suppliers.find(s => s.id === Number(p.supplierId));
            return {
                ...p,
                categoryName: cat ? cat.name : "Non classé",
                supplierName: sup ? sup.name : "Inconnu"
            };
        });
    },
    getProductById(id) {
        return this.getProducts().find(p => p.id === id);
    },
    saveProduct(productData) {
        const products = readFromStorage(DB_KEYS.PRODUCTS) || [];
        let savedProduct;
        if (productData.id) {
            const index = products.findIndex(p => p.id === Number(productData.id));
            if (index !== -1) {
                // Keep track of stock modifications for movements log
                const oldStock = products[index].stock;
                const newStock = Number(productData.stock);
                if (oldStock !== newStock) {
                    this.addStockMovement(productData.id, newStock > oldStock ? "in" : "out", Math.abs(newStock - oldStock), "Correction manuelle");
                }
                products[index] = {
                    ...products[index],
                    name: productData.name,
                    categoryId: Number(productData.categoryId),
                    stock: newStock,
                    minStock: Number(productData.minStock),
                    price: Number(productData.price),
                    purchasePrice: Number(productData.purchasePrice),
                    barcode: productData.barcode,
                    supplierId: Number(productData.supplierId)
                };
                savedProduct = products[index];
                this.addHistory(`Modification du produit ${productData.name}`, "Produits");
            }
        } else {
            const newId = products.length ? Math.max(...products.map(p => p.id)) + 1 : 1;
            savedProduct = {
                id: newId,
                name: productData.name,
                categoryId: Number(productData.categoryId),
                stock: Number(productData.stock) || 0,
                minStock: Number(productData.minStock) || 0,
                price: Number(productData.price) || 0,
                purchasePrice: Number(productData.purchasePrice) || 0,
                barcode: productData.barcode || "",
                supplierId: Number(productData.supplierId) || 0
            };
            products.push(savedProduct);
            this.addHistory(`Ajout du produit ${productData.name}`, "Produits");
            if (savedProduct.stock > 0) {
                this.addStockMovement(newId, "in", savedProduct.stock, "Stock initial");
            }
        }
        writeToStorage(DB_KEYS.PRODUCTS, products);
        this.checkLowStocks();
        return savedProduct;
    },
    deleteProduct(id) {
        const products = readFromStorage(DB_KEYS.PRODUCTS) || [];
        const product = products.find(p => p.id === id);
        if (product) {
            const filtered = products.filter(p => p.id !== id);
            writeToStorage(DB_KEYS.PRODUCTS, filtered);
            this.addHistory(`Suppression du produit ${product.name}`, "Produits");
            this.checkLowStocks();
            return true;
        }
        return false;
    },

    // Categories
    getCategories() {
        return readFromStorage(DB_KEYS.CATEGORIES) || [];
    },
    saveCategory(catData) {
        const categories = this.getCategories();
        let savedCat;
        if (catData.id) {
            const index = categories.findIndex(c => c.id === Number(catData.id));
            if (index !== -1) {
                categories[index].name = catData.name;
                categories[index].description = catData.description;
                savedCat = categories[index];
                this.addHistory(`Modification de la catégorie ${catData.name}`, "Catégories");
            }
        } else {
            const newId = categories.length ? Math.max(...categories.map(c => c.id)) + 1 : 1;
            savedCat = {
                id: newId,
                name: catData.name,
                description: catData.description
            };
            categories.push(savedCat);
            this.addHistory(`Création de la catégorie ${catData.name}`, "Catégories");
        }
        writeToStorage(DB_KEYS.CATEGORIES, categories);
        return savedCat;
    },
    deleteCategory(id) {
        const categories = this.getCategories();
        const cat = categories.find(c => c.id === id);
        if (cat) {
            // Check if products exist in category
            const products = this.getProducts();
            const count = products.filter(p => p.categoryId === id).length;
            if (count > 0) {
                throw new Error(`Impossible de supprimer : cette catégorie contient ${count} produits.`);
            }
            const filtered = categories.filter(c => c.id !== id);
            writeToStorage(DB_KEYS.CATEGORIES, filtered);
            this.addHistory(`Suppression de la catégorie ${cat.name}`, "Catégories");
            return true;
        }
        return false;
    },

    // Suppliers
    getSuppliers() {
        return readFromStorage(DB_KEYS.SUPPLIERS) || [];
    },
    saveSupplier(supData) {
        const suppliers = this.getSuppliers();
        let savedSup;
        if (supData.id) {
            const index = suppliers.findIndex(s => s.id === Number(supData.id));
            if (index !== -1) {
                suppliers[index] = { ...suppliers[index], ...supData };
                savedSup = suppliers[index];
                this.addHistory(`Modification du fournisseur ${supData.name}`, "Fournisseurs");
            }
        } else {
            const newId = suppliers.length ? Math.max(...suppliers.map(s => s.id)) + 1 : 1;
            savedSup = { id: newId, ...supData };
            suppliers.push(savedSup);
            this.addHistory(`Ajout du fournisseur ${supData.name}`, "Fournisseurs");
        }
        writeToStorage(DB_KEYS.SUPPLIERS, suppliers);
        return savedSup;
    },
    deleteSupplier(id) {
        const suppliers = this.getSuppliers();
        const sup = suppliers.find(s => s.id === id);
        if (sup) {
            const filtered = suppliers.filter(s => s.id !== id);
            writeToStorage(DB_KEYS.SUPPLIERS, filtered);
            this.addHistory(`Suppression du fournisseur ${sup.name}`, "Fournisseurs");
            return true;
        }
        return false;
    },

    // Clients
    getClients() {
        return readFromStorage(DB_KEYS.CLIENTS) || [];
    },
    saveClient(clientData) {
        const clients = this.getClients();
        let savedClient;
        if (clientData.id) {
            const index = clients.findIndex(c => c.id === Number(clientData.id));
            if (index !== -1) {
                clients[index] = { ...clients[index], ...clientData };
                savedClient = clients[index];
                this.addHistory(`Modification du client ${clientData.name}`, "Clients");
            }
        } else {
            const newId = clients.length ? Math.max(...clients.map(c => c.id)) + 1 : 1;
            savedClient = { id: newId, ...clientData };
            clients.push(savedClient);
            this.addHistory(`Ajout du client ${clientData.name}`, "Clients");
        }
        writeToStorage(DB_KEYS.CLIENTS, clients);
        return savedClient;
    },
    deleteClient(id) {
        const clients = this.getClients();
        const client = clients.find(c => c.id === id);
        if (client) {
            const filtered = clients.filter(c => c.id !== id);
            writeToStorage(DB_KEYS.CLIENTS, filtered);
            this.addHistory(`Suppression du client ${client.name}`, "Clients");
            return true;
        }
        return false;
    },

    // Movements
    getStockMovements() {
        const movements = readFromStorage(DB_KEYS.MOVEMENTS) || [];
        const products = readFromStorage(DB_KEYS.PRODUCTS) || [];
        return movements.map(m => {
            const prod = products.find(p => p.id === Number(m.productId));
            return {
                ...m,
                productName: prod ? prod.name : "Produit supprimé"
            };
        });
    },
    addStockMovement(productId, type, qty, comment = "", user = "Administrateur") {
        const movements = readFromStorage(DB_KEYS.MOVEMENTS) || [];
        const newId = movements.length ? Math.max(...movements.map(m => m.id)) + 1 : 1;
        const entry = {
            id: newId,
            productId: Number(productId),
            type, // 'in' (entrée), 'out' (sortie), 'adj' (ajustement/correction)
            qty: Number(qty),
            date: new Date().toISOString(),
            user,
            comment
        };
        movements.unshift(entry);
        writeToStorage(DB_KEYS.MOVEMENTS, movements);
        return entry;
    },
    adjustStock(productId, type, qty, comment, user = "Administrateur") {
        const products = readFromStorage(DB_KEYS.PRODUCTS) || [];
        const index = products.findIndex(p => p.id === Number(productId));
        if (index !== -1) {
            const prod = products[index];
            let newStock = prod.stock;
            if (type === "in") {
                newStock += Number(qty);
            } else if (type === "out") {
                newStock = Math.max(0, newStock - Number(qty));
            } else if (type === "adj") {
                newStock = Number(qty);
            }
            
            const diff = newStock - prod.stock;
            if (diff !== 0) {
                prod.stock = newStock;
                writeToStorage(DB_KEYS.PRODUCTS, products);
                this.addStockMovement(productId, diff > 0 ? "in" : "out", Math.abs(diff), comment, user);
                this.addHistory(`Ajustement stock de ${prod.name} (${diff > 0 ? '+' : ''}${diff})`, "Stock", user);
                this.checkLowStocks();
            }
            return prod;
        }
        throw new Error("Produit introuvable.");
    },

    // Deliveries
    getDeliveries() {
        const deliveries = readFromStorage(DB_KEYS.DELIVERIES) || [];
        const suppliers = this.getSuppliers();
        return deliveries.map(d => {
            const sup = suppliers.find(s => s.id === Number(d.supplierId));
            return {
                ...d,
                supplierName: sup ? sup.name : "Inconnu"
            };
        });
    },
    saveDelivery(delData) {
        const deliveries = readFromStorage(DB_KEYS.DELIVERIES) || [];
        let savedDel;
        if (delData.id) {
            const index = deliveries.findIndex(d => d.id === Number(delData.id));
            if (index !== -1) {
                deliveries[index] = { ...deliveries[index], ...delData };
                savedDel = deliveries[index];
                this.addHistory(`Modification de la livraison #${delData.id}`, "Livraisons");
            }
        } else {
            const newId = deliveries.length ? Math.max(...deliveries.map(d => d.id)) + 1 : 1001;
            savedDel = { id: newId, ...delData, status: "pending" };
            deliveries.push(savedDel);
            this.addHistory(`Création de la commande livraison #${newId}`, "Livraisons");
        }
        writeToStorage(DB_KEYS.DELIVERIES, deliveries);
        return savedDel;
    },
    receiveDelivery(id) {
        const deliveries = this.getDeliveries();
        const index = deliveries.findIndex(d => d.id === Number(id));
        if (index !== -1 && deliveries[index].status === "pending") {
            const delivery = deliveries[index];
            delivery.status = "received";
            
            // Adjust stocks
            delivery.items.forEach(item => {
                this.adjustStock(item.productId, "in", item.qty, `Réception livraison #${id}`);
            });
            
            writeToStorage(DB_KEYS.DELIVERIES, deliveries);
            this.addHistory(`Réception validée pour la livraison #${id}`, "Livraisons");
            return true;
        }
        return false;
    },

    // Users
    getUsers() {
        return readFromStorage(DB_KEYS.USERS) || [];
    },
    saveUser(userData) {
        const users = this.getUsers();
        let savedUser;
        if (userData.id) {
            const index = users.findIndex(u => u.id === Number(userData.id));
            if (index !== -1) {
                users[index] = { ...users[index], ...userData };
                savedUser = users[index];
                this.addHistory(`Modification de l'utilisateur ${userData.name}`, "Utilisateurs");
            }
        } else {
            const newId = users.length ? Math.max(...users.map(u => u.id)) + 1 : 1;
            savedUser = { id: newId, ...userData, status: "active" };
            users.push(savedUser);
            this.addHistory(`Création de l'utilisateur ${userData.name}`, "Utilisateurs");
        }
        writeToStorage(DB_KEYS.USERS, users);
        return savedUser;
    },
    deleteUser(id) {
        const users = this.getUsers();
        const user = users.find(u => u.id === id);
        if (user) {
            const filtered = users.filter(u => u.id !== id);
            writeToStorage(DB_KEYS.USERS, filtered);
            this.addHistory(`Suppression de l'utilisateur ${user.name}`, "Utilisateurs");
            return true;
        }
        return false;
    },

    // Roles
    getRoles() {
        return readFromStorage(DB_KEYS.ROLES) || [];
    },
    saveRole(roleData) {
        const roles = this.getRoles();
        let savedRole;
        if (roleData.id) {
            const index = roles.findIndex(r => r.id === Number(roleData.id));
            if (index !== -1) {
                roles[index].name = roleData.name;
                roles[index].description = roleData.description;
                roles[index].permissions = roleData.permissions || [];
                savedRole = roles[index];
                this.addHistory(`Modification du rôle ${roleData.name}`, "Rôles");
            }
        } else {
            const newId = roles.length ? Math.max(...roles.map(r => r.id)) + 1 : 1;
            savedRole = { id: newId, ...roleData, permissions: roleData.permissions || [] };
            roles.push(savedRole);
            this.addHistory(`Création du rôle ${roleData.name}`, "Rôles");
        }
        writeToStorage(DB_KEYS.ROLES, roles);
        return savedRole;
    },

    // Settings
    getSettings() {
        return readFromStorage(DB_KEYS.SETTINGS) || initialSettings;
    },
    saveSettings(settingsData) {
        writeToStorage(DB_KEYS.SETTINGS, settingsData);
        this.addHistory("Mise à jour des paramètres système", "Paramètres");
        return settingsData;
    }
};
