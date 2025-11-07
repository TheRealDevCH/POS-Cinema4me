<template>
    <div class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4">
        <div class="bg-dark-secondary rounded-lg shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="p-6 border-b-2 border-gray-700 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Abrechnung</h2>
                <button
                    @click="$emit('close')"
                    class="text-gray-400 hover:text-white text-3xl font-bold leading-none"
                >
                    ×
                </button>
            </div>

            <!-- Content -->
            <div class="p-8">
                <div v-if="loading" class="text-center py-12">
                    <p class="text-gray-400">Lade Abrechnung...</p>
                </div>

                <div v-else class="border-2 border-gray-600 p-6 bg-dark rounded-lg">
                    <!-- Kopfzeile -->
                    <div class="grid grid-cols-3 gap-6 mb-6 pb-4 border-b-2 border-gray-600">
                        <div>
                            <span class="font-bold text-white">Datum:</span>
                            <span class="ml-2 text-gray-300">{{ currentDate }}</span>
                        </div>
                        <div>
                            <span class="font-bold text-white">Ort:</span>
                            <span class="ml-2 text-gray-300">{{ location }}</span>
                        </div>
                        <div>
                            <span class="font-bold text-white">Mitarbeiter:</span>
                            <span class="ml-2 text-gray-300">{{ employeeCode }} ({{ employeeName }})</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <span class="font-bold text-white">Film:</span>
                        <span class="ml-2 text-gray-500">_______________________</span>
                    </div>

                    <!-- Besucher Total -->
                    <div class="mb-6 pb-4 border-b border-gray-600">
                        <div class="flex items-center">
                            <span class="font-bold w-40 text-white">Besucher:</span>
                            <span class="text-2xl font-bold text-white">{{ totalVisitors }}</span>
                        </div>
                    </div>

                    <!-- Ticketpreise -->
                    <div v-if="groupedByPrice.length > 0" class="mb-6 space-y-3">
                        <div
                            v-for="priceGroup in groupedByPrice"
                            :key="priceGroup.price"
                            class="space-y-2"
                        >
                            <!-- Bar -->
                            <div v-if="priceGroup.bar > 0" class="flex items-center font-mono text-white">
                                <span class="w-48">{{ priceGroup.bar }} × {{ formatPrice(priceGroup.price) }}</span>
                                <span class="mx-3">=</span>
                                <span class="font-bold w-32">{{ formatPrice(priceGroup.bar * priceGroup.price) }}</span>
                                <span class="ml-4 text-gray-400">(Bar)</span>
                            </div>

                            <!-- EC -->
                            <div v-if="priceGroup.ec > 0" class="flex items-center font-mono text-white">
                                <span class="w-48">{{ priceGroup.ec }} × {{ formatPrice(priceGroup.price) }}</span>
                                <span class="mx-3">=</span>
                                <span class="font-bold w-32">{{ formatPrice(priceGroup.ec * priceGroup.price) }}</span>
                                <span class="ml-4 text-gray-400">(EC)</span>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8 text-gray-400">
                        <p>Keine Verkäufe vorhanden</p>
                    </div>

                    <!-- Total -->
                    <div class="border-t-2 border-gray-600 pt-4 mb-4">
                        <div class="flex items-center font-mono text-white">
                            <span class="font-bold w-48">Total:</span>
                            <span class="text-2xl font-bold">{{ formatPrice(grandTotal) }} CHF</span>
                        </div>
                    </div>

                    <!-- Total Bar -->
                    <div class="border-t border-gray-600 pt-4 mb-4">
                        <div class="flex items-center font-mono text-white">
                            <span class="font-bold w-48">Total Bar:</span>
                            <span class="text-2xl font-bold">{{ formatPrice(totalBar) }} CHF</span>
                        </div>
                    </div>

                    <!-- EC (leer lassen) -->
                    <div class="border-t border-gray-600 pt-4 mb-4">
                        <div class="flex items-center font-mono text-white">
                            <span class="font-bold w-48">EC:</span>
                            <span class="text-gray-500">_______________________</span>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 mt-8">
                    <button
                        @click="showResetConfirm = true"
                        class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-colors"
                    >
                        Abrechnung zurücksetzen
                    </button>
                    <button
                        @click="$emit('close')"
                        class="flex-1 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg transition-colors"
                    >
                        Schließen
                    </button>
                </div>
            </div>

            <!-- Reset Confirmation Modal -->
            <div v-if="showResetConfirm" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Abrechnung zurücksetzen?</h3>
                    <p class="text-gray-600 mb-6">
                        Wirklich alle Einträge löschen? Diese Aktion kann nicht rückgängig gemacht werden.
                    </p>
                    
                    <div class="flex gap-4">
                        <button
                            @click="resetReport"
                            class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-colors"
                        >
                            Ja, zurücksetzen
                        </button>
                        <button
                            @click="showResetConfirm = false"
                            class="flex-1 px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg transition-colors"
                        >
                            Abbrechen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ReportView',
    props: {
        employeeCode: String,
        employeeName: String,
        location: String
    },
    data() {
        return {
            report: [],
            totalBar: 0,
            totalEC: 0,
            grandTotal: 0,
            loading: true,
            showResetConfirm: false
        };
    },
    computed: {
        currentDate() {
            const now = new Date();
            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = now.getFullYear();
            return `${day}.${month}.${year}`;
        },
        totalVisitors() {
            return this.report.reduce((sum, item) => sum + item.quantity, 0);
        },
        groupedByPrice() {
            const groups = {};
            
            this.report.forEach(item => {
                if (!groups[item.price]) {
                    groups[item.price] = {
                        price: item.price,
                        bar: 0,
                        ec: 0
                    };
                }
                
                if (item.payment_method === 'Bargeld') {
                    groups[item.price].bar += item.quantity;
                } else {
                    groups[item.price].ec += item.quantity;
                }
            });
            
            // Sortiere nach Preis absteigend
            return Object.values(groups).sort((a, b) => b.price - a.price);
        }
    },
    mounted() {
        this.loadReport();
    },
    methods: {
        async loadReport() {
            this.loading = true;
            try {
                const response = await fetch(`/api/report?employee_code=${this.employeeCode}`);
                if (response.ok) {
                    const data = await response.json();
                    this.report = data.report;
                    this.totalBar = data.total_bar;
                    this.totalEC = data.total_ec;
                    this.grandTotal = data.total;
                } else {
                    alert('Fehler beim Laden der Abrechnung');
                }
            } catch (err) {
                console.error('Fehler:', err);
                alert('Verbindungsfehler');
            } finally {
                this.loading = false;
            }
        },
        async resetReport() {
            try {
                const response = await fetch('/api/report/reset', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        employee_code: this.employeeCode
                    })
                });

                if (response.ok) {
                    this.showResetConfirm = false;
                    this.loadReport(); // Neu laden
                } else {
                    alert('Fehler beim Zurücksetzen');
                }
            } catch (err) {
                console.error('Fehler:', err);
                alert('Verbindungsfehler');
            }
        },
        formatPrice(price) {
            return parseFloat(price).toFixed(2);
        }
    }
};
</script>

