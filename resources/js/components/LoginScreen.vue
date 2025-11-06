<template>
    <div class="min-h-screen flex items-center justify-center bg-dark">
        <div class="w-full max-w-md p-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Cinema4me POS</h1>
                <p class="text-gray-400">Mitarbeiter-Anmeldung</p>
            </div>

            <div class="bg-dark-secondary p-8 rounded-lg shadow-xl">
                <form @submit.prevent="handleSubmit">
                    <div class="mb-6">
                        <label class="block text-white text-sm font-medium mb-2">
                            Mitarbeitercode
                        </label>
                        <input
                            v-model="employeeCode"
                            type="text"
                            class="w-full px-4 py-3 bg-dark border-2 border-gray-700 rounded-lg text-white focus:border-red-accent focus:outline-none transition-colors"
                            placeholder="Code eingeben"
                            autofocus
                        />
                    </div>

                    <div v-if="error" class="mb-4 p-3 bg-red-900 bg-opacity-50 border border-red-accent rounded-lg">
                        <p class="text-red-accent text-sm">{{ error }}</p>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-red-accent hover:bg-red-hover text-white font-bold py-3 rounded-lg transition-colors"
                        :disabled="loading"
                    >
                        {{ loading ? 'Wird überprüft...' : 'Anmelden' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'LoginScreen',
    data() {
        return {
            employeeCode: '',
            error: '',
            loading: false
        };
    },
    methods: {
        async handleSubmit() {
            if (!this.employeeCode.trim()) {
                this.error = 'Bitte Mitarbeitercode eingeben';
                return;
            }

            this.loading = true;
            this.error = '';

            try {
                const response = await fetch('/api/employees/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ employee_code: this.employeeCode })
                });

                const data = await response.json();

                if (response.ok) {
                    this.$emit('login', data.employee);
                } else {
                    this.error = data.message || 'Ungültiger Mitarbeitercode';
                }
            } catch (err) {
                this.error = 'Verbindungsfehler. Bitte erneut versuchen.';
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>

