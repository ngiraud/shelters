<?php

namespace Database\Seeders;

use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Species::factory()->create([
            'name' => 'Uncategorized',
        ]);

        Species::factory()->count(30)->create();
    }
}
