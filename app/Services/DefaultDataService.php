<?php

namespace App\Services;

use App\Models\User;

class DefaultDataService
{
    public static function createFor(User $user): void
    {
        $user->wallets()->create(['name' => 'Cash', 'type' => 'cash']);

        $expense = [
            ['Food & Dining', '#ef4444'], ['Transport', '#f59e0b'],
            ['Bills & Utilities', '#3b82f6'], ['Shopping', '#ec4899'],
            ['Health', '#10b981'], ['Entertainment', '#8b5cf6'],
            ['Education', '#14b8a6'], ['Other', '#6b7280'],
        ];
        $income = [
            ['Salary', '#22c55e'], ['Freelance', '#06b6d4'], ['Other Income', '#84cc16'],
        ];

        foreach ($expense as [$name, $color]) {
            $user->categories()->create(['name' => $name, 'type' => 'expense', 'color' => $color]);
        }
        foreach ($income as [$name, $color]) {
            $user->categories()->create(['name' => $name, 'type' => 'income', 'color' => $color]);
        }
    }
}