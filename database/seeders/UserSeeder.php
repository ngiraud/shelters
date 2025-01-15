<?php

namespace Database\Seeders;

use App\Models\Shelter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->create([
                'shelter_id' => Shelter::firstWhere('name', 'Limoges')->id,
                'name' => 'Nicolas Giraud',
                'email' => 'contact@ngiraud.me',
            ]);

        User::factory()
            ->count(30)
            ->state(new Sequence(
                fn(Sequence $sequence) => ['shelter_id' => Shelter::all()->random()],
            ))
            ->create();
    }
}
