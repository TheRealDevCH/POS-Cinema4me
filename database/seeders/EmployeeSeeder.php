<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::create([
            'employee_code' => '4588',
            'first_name' => 'Philipp',
            'last_name' => 'Karnath',
            'is_active' => true
        ]);
    }
}
