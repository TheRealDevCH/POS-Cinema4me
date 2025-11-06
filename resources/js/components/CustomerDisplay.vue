<template>
    <div class="w-screen h-screen flex items-center justify-center overflow-hidden relative">
        <!-- Gradient Hintergrund -->
        <div class="absolute inset-0 bg-gradient-to-br from-red-900 via-orange-800 to-red-950"></div>
        <div class="absolute inset-0 bg-dark opacity-70"></div>

        <!-- Wenn Warenkorb leer: Nur Logo -->
        <div v-if="cart.length === 0" class="flex items-center justify-center w-full h-full relative z-10">
            <img
                v-if="logoUrl"
                :src="logoUrl"
                alt="Cinema4me"
                class="max-w-[60%] max-h-[60%] object-contain filter invert"
            />
        </div>

        <!-- Wenn Warenkorb gefüllt: Bestellung anzeigen -->
        <div v-else class="w-full h-full flex flex-col p-8 relative z-10">
            <!-- Logo oben -->
            <div class="mb-6 text-center">
                <img
                    v-if="logoUrl"
                    :src="logoUrl"
                    alt="Cinema4me"
                    class="h-20 mx-auto filter invert"
                />
            </div>

            <!-- Bestellliste -->
            <div class="flex-1 space-y-4 overflow-y-auto mb-6">
                <div
                    v-for="(item, index) in cart"
                    :key="index"
                    class="bg-dark-secondary p-6 rounded-lg flex justify-between items-center border-2 border-gray-800"
                >
                    <div>
                        <h3 class="text-white font-bold text-2xl mb-1">{{ item.name }}</h3>
                        <p class="text-gray-400 text-lg">{{ item.quantity }}x {{ formatPrice(item.price) }}</p>
                    </div>
                    <p class="text-red-accent font-bold text-3xl">{{ formatPrice(item.subtotal) }}</p>
                </div>
            </div>

            <!-- Gesamt-Summe -->
            <div class="p-6 bg-dark-secondary rounded-xl border-4 border-red-accent">
                <div class="flex justify-between items-center">
                    <span class="text-white text-3xl font-bold">Gesamt:</span>
                    <span class="text-red-accent text-5xl font-bold">{{ formatPrice(total) }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'CustomerDisplay',
    data() {
        return {
            cart: [],
            employee: null,
            location: null,
            logoUrl: '/cinema4me.svg'
        };
    },
    computed: {
        total() {
            return this.cart.reduce((sum, item) => sum + parseFloat(item.subtotal), 0);
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

