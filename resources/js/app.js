import './bootstrap';
import { createApp } from 'vue';

// Import Components
import LoginScreen from './components/LoginScreen.vue';
import LocationSelection from './components/LocationSelection.vue';
import POSMain from './components/POSMain.vue';
import CustomerDisplay from './components/CustomerDisplay.vue';

const app = createApp({
    data() {
        return {
            currentScreen: 'login',
            employee: null,
            location: null,
            cart: [],
            paymentMethod: null,
            cashReceived: null,
            change: null,
            transactionId: null,
        };
    },
    mounted() {
        this.loadSession();
    },
    methods: {
        saveSession() {
            const sessionData = {
                employee: this.employee,
                location: this.location,
                currentScreen: this.currentScreen
            };
            localStorage.setItem('pos_session', JSON.stringify(sessionData));
        },

        loadSession() {
            const savedSession = localStorage.getItem('pos_session');
            if (savedSession) {
                try {
                    const sessionData = JSON.parse(savedSession);
                    if (sessionData.employee && sessionData.location) {
                        this.employee = sessionData.employee;
                        this.location = sessionData.location;
                        this.currentScreen = 'pos';
                    }
                } catch (err) {
                    console.error('Fehler beim Laden der Session:', err);
                }
            }
        },
        handleLogin(employee) {
            this.employee = employee;
            this.currentScreen = 'location';
            this.saveSession();
        },
        handleLocationSelect(location) {
            this.location = location;
            this.currentScreen = 'pos';
            this.saveSession();
        },
        updateCart(cart) {
            this.cart = cart;
            this.broadcastCartUpdate();
        },
        handlePaymentMethodSelected(method) {
            console.log('App.js - Zahlungsmethode empfangen:', method);
            this.paymentMethod = method;
            // Bargeld-Details zurücksetzen bei neuer Zahlungsmethode
            this.cashReceived = null;
            this.change = null;
            this.broadcastCartUpdate();
        },
        handleCashPaymentDetails(details) {
            this.cashReceived = details.cashReceived;
            this.change = details.change;
            this.broadcastCartUpdate();
        },
        handleTransactionCompleted(transactionId) {
            this.transactionId = transactionId;
            this.broadcastCartUpdate();
        },
        handlePaymentCancelled() {
            // Zahlung wurde abgebrochen - zurück zum Warenkorb
            this.paymentMethod = null;
            this.cashReceived = null;
            this.change = null;
            this.transactionId = null;
            this.broadcastCartUpdate();
        },
        broadcastCartUpdate() {
            const cartData = {
                cart: this.cart,
                employee: this.employee,
                location: this.location,
                paymentMethod: this.paymentMethod,
                cashReceived: this.cashReceived,
                change: this.change,
                transactionId: this.transactionId,
                timestamp: Date.now()
            };
            localStorage.setItem('cinema4me-cart', JSON.stringify(cartData));
            window.dispatchEvent(new CustomEvent('cart-updated', {
                detail: cartData
            }));
        },
        logout() {
            this.employee = null;
            this.location = null;
            this.cart = [];
            this.paymentMethod = null;
            this.cashReceived = null;
            this.change = null;
            this.transactionId = null;
            this.currentScreen = 'login';
            localStorage.removeItem('pos_session');
            localStorage.removeItem('pos_cart');
        }
    }
});

app.component('login-screen', LoginScreen);
app.component('location-selection', LocationSelection);
app.component('pos-main', POSMain);
app.component('customer-display', CustomerDisplay);

app.mount('#app');
