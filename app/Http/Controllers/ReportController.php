<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // Abrechnungsdaten abrufen (nur Eintritte)
    public function index(Request $request)
    {
        $employeeCode = $request->query('employee_code');

        // Nur Eintritte-Transaktionen
        $transactions = Transaction::where('category', 'Eintritte')
            ->where('employee_code', $employeeCode)
            ->get();

        // Gruppierung nach Preis und Zahlungsart
        $report = [];

        foreach ($transactions as $transaction) {
            $price = $transaction->product_price;
            $paymentMethod = $transaction->payment_method;

            // Kreditkarte und Twint zusammenfassen als "EC"
            if ($paymentMethod === 'Kreditkarte' || $paymentMethod === 'Twint') {
                $paymentMethod = 'EC';
            }

            $key = $price . '_' . $paymentMethod;

            if (!isset($report[$key])) {
                $report[$key] = [
                    'price' => $price,
                    'payment_method' => $paymentMethod,
                    'quantity' => 0,
                    'total' => 0,
                ];
            }

            $report[$key]['quantity'] += $transaction->quantity;
            $report[$key]['total'] += $transaction->subtotal;
        }

        // Totale berechnen
        $totalBar = 0;
        $totalEC = 0;

        foreach ($report as $item) {
            if ($item['payment_method'] === 'Bargeld') {
                $totalBar += $item['total'];
            } else {
                $totalEC += $item['total'];
            }
        }

        return response()->json([
            'report' => array_values($report),
            'total_bar' => $totalBar,
            'total_ec' => $totalEC,
            'total' => $totalBar + $totalEC,
        ]);
    }

    // Transaktion speichern
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => 'required|string',
            'location' => 'required|string',
            'payment_method' => 'required|string',
            'total' => 'required|numeric',
            'items' => 'required|array',
            'items.*.product_name' => 'required|string',
            'items.*.product_price' => 'required|numeric',
            'items.*.quantity' => 'required|integer',
            'items.*.subtotal' => 'required|numeric',
            'items.*.category' => 'required|string',
        ]);

        // Jedes Item als separate Transaktion speichern
        foreach ($validated['items'] as $item) {
            Transaction::create([
                'employee_code' => $validated['employee_code'],
                'location' => $validated['location'],
                'product_name' => $item['product_name'],
                'product_price' => $item['product_price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
                'payment_method' => $validated['payment_method'],
                'total' => $validated['total'],
                'category' => $item['category'],
            ]);
        }

        return response()->json(['message' => 'Transaktion gespeichert']);
    }

    // Alle Transaktionen zurücksetzen (für neuen Film)
    public function reset(Request $request)
    {
        $employeeCode = $request->input('employee_code');

        // Nur Eintritte-Transaktionen des Mitarbeiters löschen
        Transaction::where('category', 'Eintritte')
            ->where('employee_code', $employeeCode)
            ->delete();

        return response()->json(['message' => 'Abrechnung zurückgesetzt']);
    }

    // Mitarbeiter-Code verifizieren
    public function verifyCode(Request $request)
    {
        $code = $request->input('code');

        $employee = Employee::where('employee_code', $code)->first();

        if ($employee) {
            return response()->json(['valid' => true]);
        }

        return response()->json(['valid' => false], 401);
    }
}
