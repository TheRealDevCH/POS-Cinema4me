<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        // Einfache Validierung - wir speichern nichts mehr in der DB
        // Nur für Quittungen wird localStorage verwendet
        $request->validate([
            'employee_id' => 'required',
            'location' => 'required|string',
            'payment_method' => 'required|string',
            'total_amount' => 'required|numeric',
            'items' => 'required|array',
        ]);

        // Generiere eine Transaction-ID
        $transactionId = time() . rand(1000, 9999);

        return response()->json([
            'message' => 'Transaktion erfolgreich',
            'transaction_id' => $transactionId
        ], 201);
    }
}
