<template>
    <div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-dark-secondary p-8 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Kiosk Einstellungen</h2>
                <button @click="$emit('close')" class="text-gray-400 hover:text-white text-2xl">
                    ✕
                </button>
            </div>

            <!-- Password Lock Screen -->
            <div v-if="!isUnlocked" class="text-center py-12">
                <div class="mb-6">
                    <svg class="w-16 h-16 mx-auto text-red-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-xl text-white mb-4">Passwort eingeben</h3>
                <input
                    v-model="password"
                    type="password"
                    @keyup.enter="verifyPassword"
                    class="bg-dark border border-gray-700 text-white px-4 py-2 rounded-lg text-center text-2xl tracking-widest mb-4"
                    placeholder="••••••"
                    autofocus
                />
                <div v-if="passwordError" class="text-red-accent mb-4">{{ passwordError }}</div>
                <button
                    @click="verifyPassword"
                    class="bg-red-accent hover:bg-red-600 text-white px-6 py-2 rounded-lg font-bold"
                >
                    Entsperren
                </button>
            </div>

            <!-- Main Content (after unlock) -->
            <div v-else>
                <!-- Add New Product Section -->
                <div class="bg-dark p-6 rounded-lg mb-6">
                    <h3 class="text-xl font-bold text-white mb-4">Neues Produkt hinzufügen</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Barcode Input -->
                        <div>
                            <label class="block text-gray-400 text-sm mb-2">Barcode scannen</label>
                            <input
                                v-model="newProduct.barcode"
                                @keyup.enter="handleBarcodeEnter"
                                type="text"
                                class="w-full bg-dark-secondary border border-gray-700 text-white px-4 py-2 rounded-lg focus:border-red-accent focus:outline-none"
                                placeholder="Barcode hier scannen..."
                                ref="barcodeInput"
                            />
                            <p class="text-xs text-gray-500 mt-1">Hardware-Scanner unterstützt alle Barcode-Typen</p>
                        </div>

                        <!-- Product Name -->
                        <div>
                            <label class="block text-gray-400 text-sm mb-2">Produktname</label>
                            <input
                                v-model="newProduct.name"
                                type="text"
                                class="w-full bg-dark-secondary border border-gray-700 text-white px-4 py-2 rounded-lg focus:border-red-accent focus:outline-none"
                                placeholder="z.B. Coca Cola 0.5L"
                            />
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-gray-400 text-sm mb-2">Preis (CHF)</label>
                            <input
                                v-model="newProduct.price"
                                type="number"
                                step="0.01"
                                min="0"
                                class="w-full bg-dark-secondary border border-gray-700 text-white px-4 py-2 rounded-lg focus:border-red-accent focus:outline-none"
                                placeholder="0.00"
                            />
                        </div>

                        <!-- Add Button -->
                        <div class="flex items-end">
                            <button
                                @click="addProduct"
                                :disabled="!canAddProduct"
                                class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-700 disabled:cursor-not-allowed text-white font-bold py-2 rounded-lg transition-colors"
                            >
                                + Produkt hinzufügen
                            </button>
                        </div>
                    </div>

                    <div v-if="addError" class="text-red-accent text-sm">{{ addError }}</div>
                    <div v-if="addSuccess" class="text-green-500 text-sm">{{ addSuccess }}</div>
                </div>

                <!-- Product List -->
                <div class="bg-dark p-6 rounded-lg">
                    <h3 class="text-xl font-bold text-white mb-4">Kiosk Produkte ({{ location }})</h3>
                    
                    <div v-if="loading" class="text-center text-gray-400 py-8">
                        Lade Produkte...
                    </div>

                    <div v-else-if="products.length === 0" class="text-center text-gray-400 py-8">
                        Noch keine Produkte vorhanden. Füge dein erstes Produkt hinzu!
                    </div>

                    <div v-else class="space-y-2">
                        <div
                            v-for="product in products"
                            :key="product.id"
                            class="bg-dark-secondary p-4 rounded-lg flex items-center justify-between hover:bg-gray-800 transition-colors"
                        >
                            <!-- Product Info -->
                            <div class="flex-1">
                                <div v-if="editingProduct?.id === product.id">
                                    <!-- Edit Mode -->
                                    <div class="grid grid-cols-3 gap-2">
                                        <input
                                            v-model="editingProduct.name"
                                            type="text"
                                            class="bg-dark border border-gray-700 text-white px-2 py-1 rounded text-sm"
                                            placeholder="Name"
                                        />
                                        <input
                                            v-model="editingProduct.barcode"
                                            type="text"
                                            class="bg-dark border border-gray-700 text-white px-2 py-1 rounded text-sm"
                                            placeholder="Barcode"
                                        />
                                        <input
                                            v-model="editingProduct.price"
                                            type="number"
                                            step="0.01"
                                            class="bg-dark border border-gray-700 text-white px-2 py-1 rounded text-sm"
                                            placeholder="Preis"
                                        />
                                    </div>
                                </div>
                                <div v-else>
                                    <!-- View Mode -->
                                    <h4 class="text-white font-semibold">{{ product.name }}</h4>
                                    <div class="flex gap-4 text-sm text-gray-400 mt-1">
                                        <span>Barcode: {{ product.barcode }}</span>
                                        <span class="text-red-accent font-bold">{{ formatPrice(product.price) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2 ml-4">
                                <div v-if="editingProduct?.id === product.id">
                                    <button
                                        @click="saveEdit"
                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm"
                                    >
                                        Speichern
                                    </button>
                                    <button
                                        @click="cancelEdit"
                                        class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm ml-2"
                                    >
                                        Abbrechen
                                    </button>
                                </div>
                                <div v-else>
                                    <button
                                        @click="startEdit(product)"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm"
                                    >
                                        Bearbeiten
                                    </button>
                                    <button
                                        @click="deleteProduct(product.id)"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm ml-2"
                                    >
                                        Löschen
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'KioskSettings',
    props: {
        location: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            isUnlocked: false,
            password: '',
            passwordError: '',
            products: [],
            loading: false,
            newProduct: {
                barcode: '',
                name: '',
                price: ''
            },
            editingProduct: null,
            addError: '',
            addSuccess: ''
        };
    },
    computed: {
        canAddProduct() {
            return this.newProduct.barcode && 
                   this.newProduct.name && 
                   this.newProduct.price && 
                   parseFloat(this.newProduct.price) >= 0;
        }
    },
    methods: {
        async verifyPassword() {
            try {
                const response = await fetch('/api/kiosk/verify-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ password: this.password })
                });

                const data = await response.json();

                if (data.valid) {
                    this.isUnlocked = true;
                    this.passwordError = '';
                    this.loadProducts();
                    // Focus barcode input after unlock
                    this.$nextTick(() => {
                        if (this.$refs.barcodeInput) {
                            this.$refs.barcodeInput.focus();
                        }
                    });
                } else {
                    this.passwordError = 'Falsches Passwort';
                    this.password = '';
                }
            } catch (err) {
                console.error('Fehler:', err);
                this.passwordError = 'Verbindungsfehler';
            }
        },
        async loadProducts() {
            this.loading = true;
            try {
                const response = await fetch('/api/kiosk/products');
                const data = await response.json();
                this.products = data.products || [];
            } catch (err) {
                console.error('Fehler beim Laden:', err);
            } finally {
                this.loading = false;
            }
        },
        handleBarcodeEnter() {
            // When barcode is scanned (Enter pressed), focus on name field
            if (this.newProduct.barcode) {
                // Barcode scanners typically send Enter after scanning
                console.log('Barcode gescannt:', this.newProduct.barcode);
            }
        },
        async addProduct() {
            this.addError = '';
            this.addSuccess = '';

            try {
                const response = await fetch('/api/kiosk/products', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.newProduct)
                });

                if (response.ok) {
                    this.addSuccess = 'Produkt erfolgreich hinzugefügt!';
                    this.newProduct = { barcode: '', name: '', price: '' };
                    this.loadProducts();
                    
                    // Clear success message after 3 seconds
                    setTimeout(() => {
                        this.addSuccess = '';
                    }, 3000);

                    // Focus back to barcode input
                    this.$nextTick(() => {
                        if (this.$refs.barcodeInput) {
                            this.$refs.barcodeInput.focus();
                        }
                    });
                } else {
                    const data = await response.json();
                    this.addError = data.message || 'Fehler beim Hinzufügen';
                }
            } catch (err) {
                console.error('Fehler:', err);
                this.addError = 'Verbindungsfehler';
            }
        },
        startEdit(product) {
            this.editingProduct = { ...product };
        },
        cancelEdit() {
            this.editingProduct = null;
        },
        async saveEdit() {
            try {
                const response = await fetch(`/api/kiosk/products/${this.editingProduct.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: this.editingProduct.name,
                        barcode: this.editingProduct.barcode,
                        price: this.editingProduct.price
                    })
                });

                if (response.ok) {
                    this.loadProducts();
                    this.editingProduct = null;
                } else {
                    alert('Fehler beim Speichern');
                }
            } catch (err) {
                console.error('Fehler:', err);
                alert('Verbindungsfehler');
            }
        },
        async deleteProduct(id) {
            if (!confirm('Produkt wirklich löschen?')) {
                return;
            }

            try {
                const response = await fetch(`/api/kiosk/products/${id}`, {
                    method: 'DELETE'
                });

                if (response.ok) {
                    this.loadProducts();
                } else {
                    alert('Fehler beim Löschen');
                }
            } catch (err) {
                console.error('Fehler:', err);
                alert('Verbindungsfehler');
            }
        },
        formatPrice(price) {
            return new Intl.NumberFormat('de-CH', {
                style: 'currency',
                currency: 'CHF'
            }).format(price);
        }
    }
};
</script>

