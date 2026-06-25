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
        // 1. Seed Roles
        $adminRole = \App\Models\Role::firstOrCreate(['name' => 'admin']);
        $dosenRole = \App\Models\Role::firstOrCreate(['name' => 'dosen']);
        $mahasiswaRole = \App\Models\Role::firstOrCreate(['name' => 'mahasiswa']);

        // 2. Seed Users
        if (User::where('email', 'admin@example.com')->count() === 0) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role_id' => $adminRole->id,
            ]);
        }

        if (User::where('email', 'dosen@example.com')->count() === 0) {
            User::create([
                'name' => 'Dosen User',
                'email' => 'dosen@example.com',
                'password' => bcrypt('password'),
                'role_id' => $dosenRole->id,
            ]);
        }

        if (User::where('email', 'mahasiswa@example.com')->count() === 0) {
            User::create([
                'name' => 'Mahasiswa User',
                'email' => 'mahasiswa@example.com',
                'password' => bcrypt('password'),
                'role_id' => $mahasiswaRole->id,
            ]);
        }

        if (User::where('email', 'test@example.com')->count() === 0) {
            User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'role_id' => $mahasiswaRole->id,
            ]);
        }

        // 3. Seed dummy loans
        if (\App\Models\Loan::count() === 0) {
            // Active Loan 1
            \App\Models\Loan::create([
                'member_id' => 1,
                'book_id' => 101,
                'borrow_date' => '2026-06-20',
                'status' => 'active',
            ]);

            // Active Loan 2
            \App\Models\Loan::create([
                'member_id' => 2,
                'book_id' => 102,
                'borrow_date' => '2026-06-21',
                'status' => 'active',
            ]);

            // Returned Loan
            \App\Models\Loan::create([
                'member_id' => 3,
                'book_id' => 103,
                'borrow_date' => '2026-06-15',
                'return_date' => '2026-06-22',
                'status' => 'returned',
                'receipt_number' => 'IAE-LOG-2026-RCP0001',
            ]);

            // Active Loan 3
            \App\Models\Loan::create([
                'member_id' => 1,
                'book_id' => 104,
                'borrow_date' => '2026-06-23',
                'status' => 'active',
            ]);

            // Returned Loan 2
            \App\Models\Loan::create([
                'member_id' => 2,
                'book_id' => 105,
                'borrow_date' => '2026-06-10',
                'return_date' => '2026-06-18',
                'status' => 'returned',
                'receipt_number' => 'IAE-LOG-2026-RCP0002',
            ]);
        }
    }
}
