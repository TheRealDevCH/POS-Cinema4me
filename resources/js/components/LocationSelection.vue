<template>
    <div class="min-h-screen flex items-center justify-center bg-dark">
        <div class="w-full max-w-2xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Willkommen, {{ employee.display_name }}</h1>
                <p class="text-gray-400">Bitte Standort auswählen</p>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <button
                    @click="selectLocation('Kino Rex')"
                    class="bg-dark-secondary hover:bg-red-accent p-12 rounded-lg shadow-xl transition-all transform hover:scale-105"
                >
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-white mb-2">Kino Rex</h2>
                        <p class="text-gray-400 text-sm">Bielstrasse 17</p>
                        <p class="text-gray-400 text-sm">2540 Grenchen</p>
                    </div>
                </button>

                <button
                    @click="selectLocation('Palace')"
                    class="bg-dark-secondary hover:bg-red-accent p-12 rounded-lg shadow-xl transition-all transform hover:scale-105"
                >
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-white mb-2">Palace</h2>
                        <p class="text-gray-400 text-sm">Rainstrasse 19</p>
                        <p class="text-gray-400 text-sm">2540 Grenchen</p>
                    </div>
                </button>
            </div>

            <div class="text-center mt-8">
                <button
                    @click="$emit('logout')"
                    class="text-gray-400 hover:text-red-accent transition-colors"
                >
                    Abmelden
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'LocationSelection',
    props: {
        employee: {
            type: Object,
            required: true
        }
    },
    methods: {
        selectLocation(location) {
            this.$emit('location-select', location);
        }
    }
};
</script>

