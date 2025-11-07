<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'employee_code' => 'required|string'
        ]);

        $employee = Employee::where('employee_code', $request->employee_code)
            ->where('is_active', true)
            ->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Ungültiger Mitarbeitercode oder Mitarbeiter ist nicht aktiv'
            ], 404);
        }

        return response()->json([
            'employee' => [
                'id' => $employee->id,
                'employee_code' => $employee->employee_code,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'display_name' => $employee->display_name
            ]
        ]);
    }
}
