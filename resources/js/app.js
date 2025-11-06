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
        };
    },
    methods: {
        handleLogin(employee) {
            this.employee = employee;
            this.currentScreen = 'location';
        },
        handleLocationSelect(location) {
            this.location = location;
            this.currentScreen = 'pos';
        },
        updateCart(cart) {
            this.cart = cart;
            this.broadcastCartUpdate();
        },
        broadcastCartUpdate() {
            const cartData = {
                cart: this.cart,
                employee: this.employee,
                location: this.location,
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
            this.currentScreen = 'login';
        }
    }
});

app.component('login-screen', LoginScreen);
app.component('location-selection', LocationSelection);
app.component('pos-main', POSMain);
app.component('customer-display', CustomerDisplay);

app.mount('#app');
