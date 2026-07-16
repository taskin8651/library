<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Plan;
use App\Models\Library;
use App\Models\User;
use App\Models\Shift;
use App\Models\Seat;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Plans
        $starter = Plan::create([
            'name' => 'Starter',
            'description' => 'Perfect for small study libraries',
            'price' => 399,
            'trial_days' => 14,
            'max_branches' => 1,
            'staff_accounts' => false,
            'white_label' => false,
            'is_active' => true,
        ]);

        $pro = Plan::create([
            'name' => 'Pro',
            'description' => 'For growing libraries with multiple branches',
            'price' => 699,
            'trial_days' => 14,
            'max_branches' => 3,
            'staff_accounts' => true,
            'white_label' => false,
            'is_active' => true,
        ]);

        $premium = Plan::create([
            'name' => 'Premium',
            'description' => 'Unlimited branches with white-label branding',
            'price' => 999,
            'trial_days' => 14,
            'max_branches' => -1,
            'staff_accounts' => true,
            'white_label' => true,
            'is_active' => true,
        ]);

        // Create Super Admin
        User::create([
            'library_id' => null,
            'name' => 'Super Admin',
            'email' => 'admin@librarycrm.com',
            'phone' => '9999999999',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        // Create Demo Library
        $library = Library::create([
            'name' => 'Demo Study Library',
            'slug' => 'demo',
            'email' => 'owner@demo.com',
            'phone' => '9876543210',
            'address' => 'Gandhi Maidan, Patna',
            'city' => 'Patna',
            'state' => 'Bihar',
            'theme_color' => '#0d6efd',
            'plan_id' => $starter->id,
            'status' => 'active',
            'trial_ends_at' => Carbon::now()->addDays(14),
            'plan_expires_at' => Carbon::now()->addDays(14),
        ]);

        // Create Owner user
        $owner = User::create([
            'library_id' => $library->id,
            'name' => 'Rahul Kumar',
            'email' => 'owner@demo.com',
            'phone' => '9876543210',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'is_active' => true,
        ]);

        // Create Shifts
        $morning = Shift::create([
            'library_id' => $library->id,
            'name' => 'Morning',
            'start_time' => '06:00:00',
            'end_time' => '12:00:00',
            'price' => 500,
            'is_active' => true,
        ]);

        $evening = Shift::create([
            'library_id' => $library->id,
            'name' => 'Evening',
            'start_time' => '14:00:00',
            'end_time' => '20:00:00',
            'price' => 500,
            'is_active' => true,
        ]);

        $fullday = Shift::create([
            'library_id' => $library->id,
            'name' => 'Full Day',
            'start_time' => '06:00:00',
            'end_time' => '22:00:00',
            'price' => 800,
            'is_active' => true,
        ]);

        // Create 20 Seats (4 rows x 5 seats)
        $rows = ['A','B','C','D'];
        foreach ($rows as $row) {
            for ($i = 1; $i <= 5; $i++) {
                Seat::create([
                    'library_id' => $library->id,
                    'seat_number' => $row . $i,
                    'row_label' => $row,
                    'type' => 'regular',
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('📧 Super Admin: admin@librarycrm.com / password');
        $this->command->info('📧 Library Owner: owner@demo.com / password');
    }
}
