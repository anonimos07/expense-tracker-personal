<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    DefaultDataService::createFor($user);

    $wallet = $user->wallets()->first();
    $categories = $user->categories()->where('type', 'expense')->get();

    for ($i = 0; $i < 60; $i++) {
        $user->transactions()->create([
            'wallet_id'   => $wallet->id,
            'category_id' => $categories->random()->id,
            'type'        => 'expense',
            'amount'      => fake()->randomFloat(2, 20, 2500),
            'description' => fake()->sentence(3),
            'date'        => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }
}
}
