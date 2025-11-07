<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alte Tabelle löschen
        Schema::dropIfExists('transactions');

        // Neue Tabelle mit korrekten Spalten erstellen
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code'); // Mitarbeiter-Code
            $table->string('location'); // Kino-Standort
            $table->string('product_name'); // Name des Produkts (z.B. "3D Filme")
            $table->decimal('product_price', 10, 2); // Preis des Produkts
            $table->integer('quantity'); // Anzahl
            $table->decimal('subtotal', 10, 2); // Zwischensumme
            $table->string('payment_method'); // Bargeld, Kreditkarte, Twint
            $table->decimal('total', 10, 2); // Gesamtsumme der Transaktion
            $table->string('category'); // Kategorie (Eintritte, Kiosk, Gutscheine)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
