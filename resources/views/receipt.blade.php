<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quittung - Cinema4me</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: white;
            color: black;
            padding: 20px;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .receipt {
            border: 2px solid black;
            padding: 20px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo img {
            width: 150px;
            height: auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed black;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 12px;
            line-height: 1.5;
        }
        
        .info {
            margin-bottom: 20px;
            font-size: 12px;
            border-bottom: 1px solid black;
            padding-bottom: 15px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .items {
            margin-bottom: 20px;
        }
        
        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .item-name {
            flex: 1;
        }
        
        .item-qty {
            width: 40px;
            text-align: center;
        }
        
        .item-price {
            width: 80px;
            text-align: right;
        }
        
        .total-section {
            border-top: 2px solid black;
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .total-row.grand-total {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid black;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .payment-info {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed black;
            font-size: 12px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed black;
            font-size: 12px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .receipt {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt" id="receipt-content">
        <!-- Logo -->
        <div class="logo">
            <img src="/cinema4me.svg" alt="Cinema4me">
        </div>
        
        <!-- Header mit Standort-Info -->
        <div class="header">
            <h1 id="location-name">Cinema4me</h1>
            <p id="location-address">
                <!-- Wird via JavaScript gefüllt -->
            </p>
        </div>
        
        <!-- Transaktions-Info -->
        <div class="info">
            <div class="info-row">
                <span>Datum:</span>
                <span id="receipt-date"></span>
            </div>
            <div class="info-row">
                <span>Uhrzeit:</span>
                <span id="receipt-time"></span>
            </div>
            <div class="info-row">
                <span>Mitarbeiter:</span>
                <span id="receipt-employee"></span>
            </div>
            <div class="info-row">
                <span>Transaktion:</span>
                <span id="receipt-transaction">{{ $transactionId }}</span>
            </div>
        </div>
        
        <!-- Artikel -->
        <div class="items">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-weight: bold; border-bottom: 1px solid black; padding-bottom: 5px;">
                <span>Artikel</span>
                <span style="width: 40px; text-align: center;">Anz.</span>
                <span style="width: 80px; text-align: right;">Preis</span>
            </div>
            <div id="receipt-items">
                <!-- Wird via JavaScript gefüllt -->
            </div>
        </div>
        
        <!-- Gesamt -->
        <div class="total-section">
            <div class="total-row">
                <span>Zwischensumme:</span>
                <span id="receipt-subtotal"></span>
            </div>
            <div class="total-row">
                <span>MwSt. (7.7%):</span>
                <span id="receipt-tax"></span>
            </div>
            <div class="total-row grand-total">
                <span>GESAMT:</span>
                <span id="receipt-total"></span>
            </div>
        </div>
        
        <!-- Zahlungs-Info -->
        <div class="payment-info">
            <div class="info-row">
                <span>Zahlungsmethode:</span>
                <span id="receipt-payment-method"></span>
            </div>
            <div id="cash-details" style="display: none;">
                <div class="info-row">
                    <span>Gegeben:</span>
                    <span id="receipt-cash-received"></span>
                </div>
                <div class="info-row">
                    <span>Rückgeld:</span>
                    <span id="receipt-change"></span>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Vielen Dank für Ihren Besuch!</p>
            <p style="margin-top: 10px;">www.cinema4me.ch</p>
        </div>
    </div>

    <script>
        // Transaktion aus localStorage laden
        const transactionId = '{{ $transactionId }}';
        const transactionData = localStorage.getItem('cinema4me-transaction-' + transactionId);
        
        if (transactionData) {
            const data = JSON.parse(transactionData);
            
            // Standort-Info
            const locationInfo = {
                'Kino Rex': {
                    name: 'Cinema4me - Kino Rex',
                    address: 'Marktplatz 6\n2540 Grenchen\nTel: +41 32 652 26 26'
                },
                'Palace': {
                    name: 'Cinema4me - Palace',
                    address: 'Kirchstrasse 10\n2540 Grenchen\nTel: +41 32 652 26 27'
                }
            };
            
            const location = locationInfo[data.location] || locationInfo['Kino Rex'];
            document.getElementById('location-name').textContent = location.name;
            document.getElementById('location-address').innerHTML = location.address.replace(/\n/g, '<br>');
            
            // Datum und Uhrzeit
            const date = new Date(data.timestamp);
            document.getElementById('receipt-date').textContent = date.toLocaleDateString('de-CH');
            document.getElementById('receipt-time').textContent = date.toLocaleTimeString('de-CH');
            
            // Mitarbeiter
            document.getElementById('receipt-employee').textContent = data.employee;
            
            // Artikel
            let itemsHtml = '';
            data.cart.forEach(item => {
                itemsHtml += `
                    <div class="item">
                        <span class="item-name">${item.name}</span>
                        <span class="item-qty">${item.quantity}x</span>
                        <span class="item-price">${(item.price * item.quantity).toFixed(2)} CHF</span>
                    </div>
                `;
            });
            document.getElementById('receipt-items').innerHTML = itemsHtml;
            
            // Berechnung
            const subtotal = data.total;
            const taxRate = 0.077;
            const tax = subtotal * taxRate / (1 + taxRate);
            const netAmount = subtotal - tax;
            
            document.getElementById('receipt-subtotal').textContent = netAmount.toFixed(2) + ' CHF';
            document.getElementById('receipt-tax').textContent = tax.toFixed(2) + ' CHF';
            document.getElementById('receipt-total').textContent = subtotal.toFixed(2) + ' CHF';
            
            // Zahlungsmethode
            document.getElementById('receipt-payment-method').textContent = data.paymentMethod;
            
            if (data.paymentMethod === 'Bargeld' && data.cashReceived) {
                document.getElementById('cash-details').style.display = 'block';
                document.getElementById('receipt-cash-received').textContent = data.cashReceived.toFixed(2) + ' CHF';
                document.getElementById('receipt-change').textContent = data.change.toFixed(2) + ' CHF';
            }
        }
    </script>
</body>
</html>

