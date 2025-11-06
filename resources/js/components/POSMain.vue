<template>
    <div class="min-h-screen bg-dark">
        <!-- Header -->
        <div class="bg-dark-secondary border-b border-gray-800 p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white">Cinema4me POS</h1>
                    <p class="text-gray-400 text-sm">{{ employee.display_name }} - {{ location }}</p>
                </div>
                <button
                    @click="$emit('logout')"
                    class="px-4 py-2 bg-gray-700 hover:bg-red-accent text-white rounded-lg transition-colors"
                >
                    Abmelden
                </button>
            </div>
        </div>

        <div class="flex h-[calc(100vh-80px)]">
            <!-- Linke Seite: Produkte -->
            <div class="flex-1 p-6 overflow-y-auto bg-dark">
                <div v-for="category in categories" :key="category.id" class="mb-6">
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

                <div v-if="categories.length === 0" class="text-center text-gray-400 mt-20">
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
    </div>
</template>

<script>
export default {
    name: 'POSMain',
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
            cashReceived: 0,
            selectedPaymentMethod: null
        };
    },
    computed: {
        total() {
            return this.cart.reduce((sum, item) => sum + parseFloat(item.subtotal), 0);
        },
        change() {
            return this.cashReceived - this.total;
        }
    },
    mounted() {
        this.loadProducts();
    },
    watch: {
        cart: {
            deep: true,
            handler() {
                this.$emit('cart-update', this.cart);
            }
        }
    },
    methods: {
        async loadProducts() {
            try {
                const response = await fetch('/api/products');
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
            this.showPaymentModal = false;

            if (method === 'Bargeld') {
                this.showCashCalculator = true;
                this.cashReceived = 0;
            } else {
                this.completeTransaction(method, null, null);
            }
        },
        completeCashPayment() {
            this.completeTransaction('Bargeld', this.cashReceived, this.change);
            this.showCashCalculator = false;
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
                    alert('Transaktion erfolgreich abgeschlossen!');
                    this.cart = [];
                    this.cashReceived = 0;
                } else {
                    alert('Fehler bei der Transaktion');
                }
            } catch (err) {
                console.error('Fehler:', err);
                alert('Verbindungsfehler');
            }
        }
    }
};
</script>

