<!-- Product Form Modal -->
<div class="modal-overlay" id="productModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-card">
        <header class="modal-header">
            <h2 id="modalTitle">Ajouter un produit</h2>
            <button class="modal-close" aria-label="Fermer la modale"><i class="fa-solid fa-xmark"></i></button>
        </header>
        <form id="productForm">
            <input type="hidden" id="productId" name="id">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-field form-field--full">
                        <label for="pName">Nom du produit <span class="required">*</span></label>
                        <input type="text" id="pName" name="name" required placeholder="ex: Laptop Dell Latitude 5440">
                        <p class="field-error" id="error_name"></p>
                    </div>
                    <div class="form-field">
                        <label for="pCategory">Catégorie <span class="required">*</span></label>
                        <select id="pCategory" name="categoryId" required>
                            <option value="">Choisir une catégorie</option>
                            <!-- Dynamic categories -->
                        </select>
                        <p class="field-error" id="error_categoryId"></p>
                    </div>
                    <div class="form-field">
                        <label for="pBarcode">Code-barres</label>
                        <input type="text" id="pBarcode" name="barcode" placeholder="ex: 19485038291">
                        <p class="field-error" id="error_barcode"></p>
                    </div>
                    <div class="form-field">
                        <label for="pPurchasePrice">Prix d'achat (FCFA) <span class="required">*</span></label>
                        <input type="number" id="pPurchasePrice" name="purchasePrice" min="0" required placeholder="0">
                        <p class="field-error" id="error_purchasePrice"></p>
                    </div>
                    <div class="form-field">
                        <label for="pPrice">Prix de vente (FCFA) <span class="required">*</span></label>
                        <input type="number" id="pPrice" name="price" min="0" required placeholder="0">
                        <p class="field-error" id="error_price"></p>
                    </div>
                    <div class="form-field">
                        <label for="pStock">Stock initial <span class="required">*</span></label>
                        <input type="number" id="pStock" name="stock" min="0" required placeholder="0">
                        <p class="field-error" id="error_stock"></p>
                    </div>
                    <div class="form-field">
                        <label for="pMinStock">Seuil critique <span class="required">*</span></label>
                        <input type="number" id="pMinStock" name="minStock" min="1" required placeholder="5">
                        <p class="field-error" id="error_minStock"></p>
                    </div>
                    <div class="form-field form-field--full">
                        <label for="pSupplier">Fournisseur <span class="required">*</span></label>
                        <select id="pSupplier" name="supplierId" required>
                            <option value="">Choisir un fournisseur</option>
                            <!-- Dynamic suppliers -->
                        </select>
                        <p class="field-error" id="error_supplierId"></p>
                    </div>
                </div>
            </div>
            <footer class="modal-footer">
                <button class="secondary-action" type="button" data-close-modal>Annuler</button>
                <button class="primary-action" type="submit">Enregistrer</button>
            </footer>
        </form>
    </div>
</div>
