<template>
    <div class="w-screen h-screen flex items-center justify-center overflow-hidden relative">
        <!-- Gradient Hintergrund -->
        <div class="absolute inset-0 bg-gradient-to-br from-red-900 via-orange-800 to-red-950"></div>
        <div class="absolute inset-0 bg-dark opacity-30"></div>

        <transition name="slide-fade" mode="out-in">
            <!-- Wenn Warenkorb leer: Nur Logo -->
            <div v-if="cart.length === 0 && !paymentMethod && !transactionId" key="logo" class="flex items-center justify-center w-full h-full relative z-10">
                <transition name="zoom-fade" mode="out-in">
                    <img
                        v-if="logoUrl"
                        :key="'logo-' + logoKey"
                        :src="logoUrl"
                        alt="Cinema4me"
                        class="max-w-[50%] max-h-[50%] object-contain filter invert"
                    />
                </transition>
            </div>

            <!-- Wenn Zahlungsmethode ausgewählt (ABER NOCH KEINE TRANSACTION) -->
            <div v-else-if="paymentMethod && !transactionId" key="payment" class="flex items-center justify-center w-full h-full relative z-10 px-16">
                <transition name="fade" mode="out-in">
                    <div class="text-center" :key="paymentMethod">
                        <p class="text-white text-4xl mb-10 font-semibold">Sie wählten als Bezahlungsmethode:</p>
                        <p class="text-white text-7xl font-bold mb-16 tracking-wide">{{ paymentMethod }}</p>

                        <!-- Bargeld: Gegeben und Rückgeld anzeigen -->
                        <div v-if="paymentMethod === 'Bargeld' && cashReceived !== null">
                            <div class="mt-16 space-y-10">
                                <div>
                                    <p class="text-white text-3xl mb-4 font-semibold">Gegeben:</p>
                                    <p class="text-white text-7xl font-bold">{{ formatPrice(cashReceived) }}</p>
                                </div>
                                <div class="border-t-2 border-white border-opacity-40 pt-10">
                                    <p class="text-white text-3xl mb-4 font-semibold">Rückgeld:</p>
                                    <p class="text-white text-7xl font-bold">{{ formatPrice(change) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Kreditkarte/Twint: Terminal-Nachricht -->
                        <div v-else-if="paymentMethod === 'Kreditkarte' || paymentMethod === 'Twint'">
                            <p class="text-white text-3xl mt-12 font-semibold">Ihr Terminal wird gerade von dem Mitarbeiter für Sie bereit zur Zahlung gemacht.</p>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Finale Ansicht: Vielen Dank (NUR WENN TRANSACTION ABGESCHLOSSEN) -->
            <div v-else-if="transactionId" key="thank-you" class="flex flex-col items-center justify-center w-full h-full relative z-10 px-16">
                <h1 class="text-white text-7xl font-bold mb-12 text-center tracking-wide">Vielen Dank für Ihren Einkauf!</h1>
                <p class="text-white text-4xl text-center font-medium">Wir freuen uns auf Ihren nächsten Besuch!</p>
            </div>

            <!-- Wenn Warenkorb gefüllt: Bestellung anzeigen -->
            <div v-else key="cart" class="w-full h-full flex flex-col px-12 py-8 relative z-10">
            <!-- Logo oben -->
            <div class="mb-8 text-center">
                <img
                    v-if="logoUrl"
                    :src="logoUrl"
                    alt="Cinema4me"
                    class="h-16 mx-auto filter invert opacity-90"
                />
            </div>

            <!-- Bestellliste -->
            <div class="flex-1 space-y-6 overflow-y-auto mb-8 max-w-5xl mx-auto w-full">
                <div
                    v-for="(item, index) in cart"
                    :key="index"
                    class="p-6 flex justify-between items-center border-b-2 border-white border-opacity-30"
                >
                    <div>
                        <h3 class="text-white font-bold text-4xl mb-2">{{ item.name }}</h3>
                        <p class="text-white text-2xl">{{ item.quantity }}x {{ formatPrice(item.price) }}</p>
                    </div>
                    <p class="text-white font-bold text-5xl">{{ formatPrice(item.subtotal) }}</p>
                </div>
            </div>

            <!-- Gesamt-Summe -->
            <div class="p-8 border-t-4 border-white border-opacity-50 max-w-5xl mx-auto w-full">
                <div class="flex justify-between items-center">
                    <span class="text-white text-4xl font-bold">Gesamt:</span>
                    <span class="text-white text-6xl font-bold">{{ formatPrice(total) }}</span>
                </div>
            </div>
        </div>
        </transition>
    </div>
</template>

<script>
import QrcodeVue from 'qrcode.vue';

export default {
    name: 'CustomerDisplay',
    components: {
        QrcodeVue
    },
    data() {
        return {
            cart: [],
            employee: null,
            location: null,
            paymentMethod: null,
            cashReceived: null,
            change: null,
            transactionId: null,
            logoUrl: '/cinema4me.svg',
            logoKey: 0,
            thankYouTimer: null
        };
    },
    computed: {
        total() {
            return this.cart.reduce((sum, item) => sum + parseFloat(item.subtotal), 0);
        },
        receiptUrl() {
            if (!this.transactionId) return '';
            return window.location.origin + '/receipt/' + this.transactionId;
        }
    },
    watch: {
        transactionId(newVal, oldVal) {
            // Wenn Transaction-ID gesetzt wird, starte 5-Sekunden Timer
            if (newVal && !oldVal) {
                this.startThankYouTimer();
            }
        },
        cart(newVal, oldVal) {
            // Wenn von leer zu gefüllt wechselt, trigger Logo-Zoom
            if (oldVal.length === 0 && newVal.length > 0) {
                this.logoKey++;
            }
        }
    },
    mounted() {
        this.loadCartFromStorage();
        window.addEventListener('storage', this.handleStorageChange);
        window.addEventListener('cart-updated', this.handleCartUpdate);

        setInterval(() => {
            this.loadCartFromStorage();
        }, 500);
    },
    beforeUnmount() {
        window.removeEventListener('storage', this.handleStorageChange);
        window.removeEventListener('cart-updated', this.handleCartUpdate);
        if (this.thankYouTimer) {
            clearTimeout(this.thankYouTimer);
        }
    },
    methods: {
        loadCartFromStorage() {
            const cartData = localStorage.getItem('cinema4me-cart');
            if (cartData) {
                try {
                    const data = JSON.parse(cartData);
                    this.cart = data.cart || [];
                    this.employee = data.employee;
                    this.location = data.location;
                    this.paymentMethod = data.paymentMethod;
                    this.cashReceived = data.cashReceived;
                    this.change = data.change;
                    this.transactionId = data.transactionId;
                    console.log('CustomerDisplay - Zahlungsmethode geladen:', this.paymentMethod);
                } catch (e) {
                    console.error('Fehler beim Laden des Warenkorbs:', e);
                }
            }
        },
        handleStorageChange(event) {
            if (event.key === 'cinema4me-cart') {
                this.loadCartFromStorage();
            }
        },
        handleCartUpdate(event) {
            this.cart = event.detail.cart;
            this.employee = event.detail.employee;
            this.location = event.detail.location;
            this.paymentMethod = event.detail.paymentMethod;
            this.cashReceived = event.detail.cashReceived;
            this.change = event.detail.change;
            this.transactionId = event.detail.transactionId;
        },
        startThankYouTimer() {
            // Clear existing timer
            if (this.thankYouTimer) {
                clearTimeout(this.thankYouTimer);
            }

            // Nach 5 Sekunden zurück zum Logo
            this.thankYouTimer = setTimeout(() => {
                this.resetToLogo();
            }, 5000);
        },
        resetToLogo() {
            // Alles zurücksetzen
            this.cart = [];
            this.paymentMethod = null;
            this.cashReceived = null;
            this.change = null;
            this.transactionId = null;

            // LocalStorage leeren
            const cartData = {
                cart: [],
                employee: null,
                location: null,
                paymentMethod: null,
                cashReceived: null,
                change: null,
                transactionId: null,
                timestamp: Date.now()
            };
            localStorage.setItem('cinema4me-cart', JSON.stringify(cartData));
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

<style scoped>
/* Zoom-Fade Transition für Logo */
.zoom-fade-enter-active {
    transition: all 1.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.zoom-fade-leave-active {
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.zoom-fade-enter-from {
    transform: scale(3);
    opacity: 0;
}

.zoom-fade-leave-to {
    transform: scale(0.3);
    opacity: 0;
}

/* Fade Transition für Zahlungsmethode */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.6s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Slide-Fade Transition für Vielen Dank Screen */
.slide-fade-enter-active {
    transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.slide-fade-leave-active {
    transition: all 0.6s cubic-bezier(0.55, 0.085, 0.68, 0.53);
}

.slide-fade-enter-from {
    transform: translateY(50px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateY(-50px);
    opacity: 0;
}
</style>
<style scoped>
/* Zoom-Fade Transition für Logo */
.zoom-fade-enter-active {
    transition: all 1.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.zoom-fade-leave-active {
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.zoom-fade-enter-from {
    transform: scale(3);
    opacity: 0;
}

.zoom-fade-leave-to {
    transform: scale(0.3);
    opacity: 0;
}

/* Fade Transition für Zahlungsmethode */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.6s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Slide-Fade Transition für Vielen Dank Screen */
.slide-fade-enter-active {
    transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.slide-fade-leave-active {
    transition: all 0.6s cubic-bezier(0.55, 0.085, 0.68, 0.53);
}

.slide-fade-enter-from {
    transform: translateY(50px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateY(-50px);
    opacity: 0;
}
</style>
