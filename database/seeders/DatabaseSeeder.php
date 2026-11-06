<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Models\Role;
use Modules\Products\Models\Category;
use Modules\Products\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed roles
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
            ]
        );

        $userRole = Role::firstOrCreate(
            ['slug' => 'user'],
            [
                'name' => 'User',
            ]
        );

        // 2. Seed admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
            ]
        );

        // 3. Hubungkan admin dengan role
        if (
            !$admin->roles()
                ->where('roles.id', $adminRole->id)
                ->exists()
        ) {
            $admin->roles()->attach($adminRole);
        }

        // 4. Buat pengguna sampel
        User::factory(5)->create()->each(function (User $user) use ($userRole): void {
            $user->roles()->attach($userRole);
        });

        // 5. Seed categories
        $categories = Category::factory()
            ->count(10)
            ->create();

        // 6. Seed products
        $products = Product::factory()
            ->count(50)
            ->create();

        // 7. Hubungkan categories dengan products
        $products->each(function ($product) use ($categories) {
            $product->categories()->attach(
                $categories
                    ->random(rand(1, 3))
                    ->pluck('id')
                    ->toArray(),
                [
                    'added_at' => now(),
                ]
            );
        });

        // 8. Jalankan OrderSeeder
        $this->call(OrderSeeder::class);
    }
}

