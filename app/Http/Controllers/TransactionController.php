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
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'location' => 'required|string',
            'payment_method' => 'required|string',
            'total_amount' => 'required|numeric',
            'cash_received' => 'nullable|numeric',
            'change_given' => 'nullable|numeric',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.subtotal' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $transaction = Transaction::create([
                'employee_id' => $request->employee_id,
                'location' => $request->location,
                'payment_method' => $request->payment_method,
                'total_amount' => $request->total_amount,
                'cash_received' => $request->cash_received,
                'change_given' => $request->change_given,
            ]);

            foreach ($request->items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Transaktion erfolgreich',
                'transaction_id' => $transaction->id
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Fehler bei der Transaktion',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
