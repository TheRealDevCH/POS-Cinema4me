<template>
    <div class="min-h-screen bg-dark">
        <!-- Header -->
        <div class="bg-dark-secondary border-b border-gray-800 p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Cinema4me POS</h1>
                    <p class="text-gray-400 text-sm">{{ employee.display_name }} - {{ location }}</p>
                </div>
                <div class="flex gap-2">
                    <button
                        @click="showReportPasswordModal = true"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    >
                        Abrechnung
                    </button>
                    <button
                        @click="$emit('logout')"
                        class="px-4 py-2 bg-gray-700 hover:bg-red-accent text-white rounded-lg transition-colors"
                    >
                        Abmelden
                    </button>
                </div>
            </div>
        </div>

        <div class="flex h-[calc(100vh-80px)]">
            <!-- Linke Seite: Produkte -->
            <div class="flex-1 p-6 overflow-y-auto bg-dark">
                <div v-for="category in visibleCategories" :key="category.id" class="mb-6">
                    <div class="mb-3 pb-2 border-b border-red-accent">
                        <h2 class="text-lg font-bold text-white">{{ category.name }}</h2>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="product in category.products"
                            :key="product.id"
                            @click="addToCart(product)"
                            class="w-24 h-24 bg-dark-secondary hover:bg-red-accent rounded transition-all duration-200 border border-gray-800 hover:border-red-accent active:scale-95"
                        >
                            <div class="flex flex-col items-center justify-center h-full px-2">
                                <h3 class="text-white font-semibold text-sm text-center leading-tight mb-1">
                                    {{ product.name }}
                                </h3>
                                <p class="text-red-accent hover:text-white font-bold text-sm">
                                    {{ formatPrice(product.price) }}
                                </p>
                            </div>
                        </button>
                    </div>
                </div>

                <div v-if="visibleCategories.length === 0" class="text-center text-gray-400 mt-20">
                    <p class="text-lg">Keine Produkte verfügbar</p>
                    <p class="text-sm mt-2">Bitte Produkte in der Datenbank anlegen</p>
                </div>
            </div>

            <!-- Rechte Seite: Warenkorb -->
            <div class="w-96 bg-dark-secondary border-l border-gray-800 flex flex-col">
                <div class="p-6 border-b border-gray-800">
                    <h2 class="text-xl font-bold text-white">Warenkorb</h2>
                </div>

                <div class="flex-1 overflow-y-auto p-6">
                    <div v-if="cart.length === 0" class="text-center text-gray-400 mt-10">
                        <p>Warenkorb ist leer</p>
                    </div>

                    <div v-for="(item, index) in cart" :key="index" class="mb-4 p-4 bg-dark rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-white font-bold">{{ item.name }}</h3>
                            <button
                                @click="removeFromCart(index)"
                                class="text-red-accent hover:text-red-hover"
                            >
                                ✕
                            </button>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-2">
                                <button
                                    @click="decreaseQuantity(index)"
                                    class="w-8 h-8 bg-gray-700 hover:bg-red-accent text-white rounded"
                                >
                                    -
                                </button>
                                <span class="text-white font-bold w-8 text-center">{{ item.quantity }}</span>
                                <button
                                    @click="increaseQuantity(index)"
                                    class="w-8 h-8 bg-gray-700 hover:bg-red-accent text-white rounded"
                                >
                                    +
                                </button>
                            </div>
                            <p class="text-red-accent font-bold">{{ formatPrice(item.subtotal) }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-800">
                    <div class="mb-4">
                        <div class="flex justify-between text-white text-xl font-bold">
                            <span>Gesamt:</span>
                            <span class="text-red-accent">{{ formatPrice(total) }}</span>
                        </div>
                    </div>

                    <button
                        @click="proceedToPayment"
                        :disabled="cart.length === 0"
                        class="w-full bg-red-accent hover:bg-red-hover text-white font-bold py-4 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Zur Zahlung
                    </button>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-dark-secondary p-8 rounded-lg shadow-xl max-w-md w-full">
                <h2 class="text-2xl font-bold text-white mb-6">Zahlungsmethode wählen</h2>

                <div class="space-y-4">
                    <button
                        @click="selectPaymentMethod('Kreditkarte')"
                        class="w-full bg-dark hover:bg-red-accent p-6 rounded-lg transition-all text-white font-bold text-lg"
                    >
                        Kreditkarte
                    </button>
                    <button
                        @click="selectPaymentMethod('Bargeld')"
                        class="w-full bg-dark hover:bg-red-accent p-6 rounded-lg transition-all text-white font-bold text-lg"
                    >
                        Bargeld
                    </button>
                    <button
                        @click="selectPaymentMethod('Twint')"
                        class="w-full bg-dark hover:bg-red-accent p-6 rounded-lg transition-all text-white font-bold text-lg"
                    >
                        Twint
                    </button>
                </div>

                <button
                    @click="showPaymentModal = false"
                    class="w-full mt-6 text-gray-400 hover:text-red-accent transition-colors"
                >
                    Abbrechen
                </button>
            </div>
        </div>

        <!-- Cash Calculator Modal -->
        <div v-if="showCashCalculator" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-dark-secondary p-8 rounded-lg shadow-xl max-w-md w-full">
                <h2 class="text-2xl font-bold text-white mb-6">Bargeld-Rechner</h2>

                <div class="mb-6">
                    <div class="text-center mb-4">
                        <p class="text-gray-400 text-sm">Zu zahlender Betrag</p>
                        <p class="text-white text-3xl font-bold">{{ formatPrice(total) }}</p>
                    </div>

                    <label class="block text-white text-sm font-medium mb-2">
                        Erhaltener Betrag (CHF)
                    </label>
                    <input
                        v-model.number="cashReceived"
                        type="number"
                        step="0.05"
                        class="w-full px-4 py-3 bg-dark border-2 border-gray-700 rounded-lg text-white text-2xl text-center focus:border-red-accent focus:outline-none"
                        placeholder="0.00"
                        autofocus
                    />
                </div>

                <div v-if="cashReceived >= total" class="mb-6 p-4 bg-green-900 bg-opacity-30 border border-green-500 rounded-lg">
                    <p class="text-green-400 text-sm text-center">Rückgeld</p>
                    <p class="text-green-400 text-3xl font-bold text-center">{{ formatPrice(change) }}</p>
                </div>

                <div v-else-if="cashReceived > 0" class="mb-6 p-4 bg-red-900 bg-opacity-30 border border-red-accent rounded-lg">
                    <p class="text-red-accent text-sm text-center">Betrag zu niedrig</p>
                </div>

                <button
                    @click="completeCashPayment"
                    :disabled="cashReceived < total"
                    class="w-full bg-red-accent hover:bg-red-hover text-white font-bold py-4 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed mb-4"
                >
                    Zahlung abschliessen
                </button>

                <button
                    @click="showCashCalculator = false"
                    class="w-full text-gray-400 hover:text-red-accent transition-colors"
                >
                    Abbrechen
                </button>
            </div>
        </div>

        <!-- Card/Twint Payment Confirmation Modal -->
        <div v-if="showCardConfirmation" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-dark-secondary p-8 rounded-lg shadow-xl max-w-md w-full">
                <h2 class="text-2xl font-bold text-white mb-6">{{ selectedPaymentMethod }} Zahlung</h2>

                <div class="text-center mb-8">
                    <p class="text-gray-400 text-sm mb-2">Zu zahlender Betrag</p>
                    <p class="text-white text-4xl font-bold mb-6">{{ formatPrice(total) }}</p>

                    <div class="bg-yellow-900 bg-opacity-30 border border-yellow-500 rounded-lg p-4 mb-6">
                        <p class="text-yellow-400 text-lg font-semibold">Terminal wird vorbereitet...</p>
                        <p class="text-yellow-300 text-sm mt-2">Bitte warten Sie auf die Bestätigung</p>
                    </div>
                </div>

                <button
                    @click="completeCardPayment"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-lg transition-colors mb-4"
                >
                    ✓ Zahlung erfolgreich bestätigen
                </button>

                <button
                    @click="cancelCardPayment"
                    class="w-full text-gray-400 hover:text-red-accent transition-colors"
                >
                    Abbrechen
                </button>
            </div>
        </div>

        <!-- Passwort-Modal für Abrechnung -->
        <div v-if="showReportPasswordModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-dark-secondary p-8 rounded-lg shadow-xl max-w-md w-full">
                <h3 class="text-xl font-bold text-white mb-4">Mitarbeiter-Code eingeben</h3>
                <p class="text-gray-400 mb-6">Bitte geben Sie Ihren Mitarbeiter-Code ein, um die Abrechnung zu öffnen.</p>

                <input
                    v-model="reportPasswordInput"
                    type="password"
                    placeholder="Mitarbeiter-Code"
                    class="w-full px-4 py-3 bg-dark border border-gray-700 rounded-lg text-white mb-4 focus:outline-none focus:border-red-accent"
                    @keydown.enter="verifyReportPassword"
                />

                <div class="flex gap-4">
                    <button
                        @click="verifyReportPassword"
                        class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors"
                    >
                        Öffnen
                    </button>
                    <button
                        @click="closeReportPasswordModal"
                        class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white font-bold rounded-lg transition-colors"
                    >
                        Abbrechen
                    </button>
                </div>
            </div>
        </div>

        <!-- Report View -->
        <report-view
            v-if="showReportView"
            :employee-code="employee.employee_code"
            :employee-name="employee.display_name"
            :location="location"
            @close="showReportView = false"
        />
    </div>
</template>

<script>
import ReportView from './ReportView.vue';

export default {
    name: 'POSMain',
    components: {
        ReportView
    },
    props: {
        employee: Object,
        location: String
    },
    data() {
        return {
            categories: [],
            cart: [],
            showPaymentModal: false,
            showCashCalculator: false,
            showCardConfirmation: false,
            cashReceived: 0,
            selectedPaymentMethod: null,
            showReportPasswordModal: false,
            reportPasswordInput: '',
            showReportView: false
        };
    },
    computed: {
        total() {
            return this.cart.reduce((sum, item) => sum + parseFloat(item.subtotal), 0);
        },
        change() {
            return this.cashReceived - this.total;
        },
        visibleCategories() {
            // Alle Kategorien anzeigen (inkl. Kiosk mit Buttons)
            return this.categories;
        }
    },
    mounted() {
        this.loadProducts();
        this.loadCartFromStorage();
    },
    watch: {
        cart: {
            deep: true,
            handler() {
                this.$emit('cart-update', this.cart);
                this.saveCartToStorage();
            }
        }
    },
    methods: {
        saveCartToStorage() {
            localStorage.setItem('pos_cart', JSON.stringify(this.cart));
        },

        loadCartFromStorage() {
            const savedCart = localStorage.getItem('pos_cart');
            if (savedCart) {
                try {
                    this.cart = JSON.parse(savedCart);
                } catch (err) {
                    console.error('Fehler beim Laden des Warenkorbs:', err);
                }
            }
        },

        async loadProducts() {
            try {
                const response = await fetch(`/api/products?location=${encodeURIComponent(this.location)}`);
                const data = await response.json();
                this.categories = data;
            } catch (err) {
                console.error('Fehler beim Laden der Produkte:', err);
            }
        },

        addToCart(product) {
            const existingItem = this.cart.find(item => item.id === product.id);
            if (existingItem) {
                existingItem.quantity++;
                existingItem.subtotal = existingItem.quantity * existingItem.price;
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    quantity: 1,
                    subtotal: parseFloat(product.price)
                });
            }
        },
        removeFromCart(index) {
            this.cart.splice(index, 1);
        },
        increaseQuantity(index) {
            this.cart[index].quantity++;
            this.cart[index].subtotal = this.cart[index].quantity * this.cart[index].price;
        },
        decreaseQuantity(index) {
            if (this.cart[index].quantity > 1) {
                this.cart[index].quantity--;
                this.cart[index].subtotal = this.cart[index].quantity * this.cart[index].price;
            } else {
                this.removeFromCart(index);
            }
        },
        formatPrice(price) {
            return new Intl.NumberFormat('de-CH', {
                style: 'currency',
                currency: 'CHF'
            }).format(price);
        },
        proceedToPayment() {
            this.showPaymentModal = true;
        },
        selectPaymentMethod(method) {
            this.selectedPaymentMethod = method;

            // Zahlungsmethode SOFORT an Kunden-Display senden (bevor Modal schließt)
            console.log('POSMain - Zahlungsmethode ausgewählt:', method);
            this.$emit('payment-method-selected', method);

            // Kurze Verzögerung, damit Kunde die Methode sieht
            setTimeout(() => {
                this.showPaymentModal = false;

                if (method === 'Bargeld') {
                    this.showCashCalculator = true;
                    this.cashReceived = 0;
                } else if (method === 'Kreditkarte' || method === 'Twint') {
                    // Zeige Bestätigungs-Modal für Kreditkarte/Twint
                    this.showCardConfirmation = true;
                }
            }, 100);
        },
        completeCashPayment() {
            // Bargeld-Details an Kunden-Display senden
            this.$emit('cash-payment-details', {
                cashReceived: this.cashReceived,
                change: this.change
            });

            this.completeTransaction('Bargeld', this.cashReceived, this.change);
            this.showCashCalculator = false;
        },
        completeCardPayment() {
            // Zahlung wurde vom Kassierer bestätigt
            this.showCardConfirmation = false;
            this.completeTransaction(this.selectedPaymentMethod, null, null);
        },
        cancelCardPayment() {
            // Zahlung abbrechen
            this.showCardConfirmation = false;
            this.selectedPaymentMethod = null;
            // Kunde-Display zurücksetzen
            this.$emit('payment-cancelled');
        },
        async completeTransaction(paymentMethod, cashReceived, changeGiven) {
            try {
                const response = await fetch('/api/transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        employee_id: this.employee.id,
                        location: this.location,
                        payment_method: paymentMethod,
                        total_amount: this.total,
                        cash_received: cashReceived,
                        change_given: changeGiven,
                        items: this.cart
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    const transactionId = result.transaction_id || Date.now();

                    // Transaktion für Abrechnung speichern (nur Eintritte)
                    try {
                        const reportItems = this.cart.map(item => ({
                            product_name: item.name,
                            product_price: item.price,
                            quantity: item.quantity,
                            subtotal: item.subtotal,
                            category: this.getCategoryForProduct(item.id)
                        }));

                        const reportResponse = await fetch('/api/report/transactions', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                employee_code: this.employee.employee_code || this.employee.id,
                                location: this.location,
                                payment_method: paymentMethod,
                                total: this.total,
                                items: reportItems
                            })
                        });

                        if (!reportResponse.ok) {
                            console.error('Fehler beim Speichern der Abrechnung:', await reportResponse.text());
                        }
                    } catch (reportErr) {
                        console.error('Fehler beim Speichern der Abrechnung:', reportErr);
                        // Transaktion trotzdem fortsetzen
                    }

                    // Transaktion für Quittung speichern
                    const transactionData = {
                        transactionId: transactionId,
                        employee: this.employee.name,
                        location: this.location,
                        cart: this.cart,
                        total: this.total,
                        paymentMethod: paymentMethod,
                        cashReceived: cashReceived,
                        change: changeGiven,
                        timestamp: Date.now()
                    };
                    localStorage.setItem('cinema4me-transaction-' + transactionId, JSON.stringify(transactionData));

                    // Warte 5 Sekunden, dann zeige "Vielen Dank" Screen
                    setTimeout(() => {
                        this.$emit('transaction-completed', transactionId);
                    }, 5000);

                    // Nach weiteren 5 Sekunden (insgesamt 10 Sekunden) Warenkorb leeren und zurücksetzen
                    setTimeout(() => {
                        this.cart = [];
                        localStorage.removeItem('pos_cart'); // Warenkorb aus localStorage löschen
                        this.cashReceived = 0;
                        this.selectedPaymentMethod = null;
                        this.$emit('cart-update', this.cart);
                        this.$emit('payment-method-selected', null);
                        this.$emit('transaction-completed', null);
                    }, 10000);
                } else {
                    alert('Fehler bei der Transaktion');
                }
            } catch (err) {
                console.error('Fehler:', err);
                alert('Verbindungsfehler');
            }
        },
        getCategoryForProduct(productId) {
            for (const category of this.categories) {
                const product = category.products.find(p => p.id === productId);
                if (product) {
                    return category.name;
                }
            }
            return 'Unbekannt';
        },
        async verifyReportPassword() {
            if (!this.reportPasswordInput.trim()) {
                alert('Bitte Code eingeben');
                return;
            }

            try {
                const response = await fetch('/api/report/verify-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        code: this.reportPasswordInput
                    })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.valid) {
                        // Code ist korrekt
                        this.showReportPasswordModal = false;
                        this.reportPasswordInput = '';
                        this.showReportView = true;
                    } else {
                        alert('Falscher Code');
                        this.reportPasswordInput = '';
                    }
                } else {
                    alert('Falscher Code');
                    this.reportPasswordInput = '';
                }
            } catch (err) {
                console.error('Fehler:', err);
                alert('Verbindungsfehler');
            }
        },
        closeReportPasswordModal() {
            this.showReportPasswordModal = false;
            this.reportPasswordInput = '';
        }
    }
};
</script>

