<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (\App\Models\User::where('email', 'test@example.com')->count() === 0) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // Seed dummy loans
        if (\App\Models\Loan::count() === 0) {
            \App\Models\Loan::create([
                'member_id' => 1,
                'book_id' => 101,
                'borrow_date' => '2026-06-20',
                'status' => 'active',
            ]);

            \App\Models\Loan::create([
                'member_id' => 2,
                'book_id' => 102,
                'borrow_date' => '2026-06-21',
                'status' => 'active',
            ]);

            \App\Models\Loan::create([
                'member_id' => 3,
                'book_id' => 103,
                'borrow_date' => '2026-06-15',
                'return_date' => '2026-06-22',
                'status' => 'returned',
                'receipt_number' => 'RCP-20260622-001',
            ]);
        }
    }
}
