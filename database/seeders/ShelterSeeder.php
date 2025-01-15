<?php

namespace Database\Seeders;

use App\Models\Shelter;
use Illuminate\Database\Seeder;

class ShelterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shelter::factory()->create([
            'name' => 'Limoges',
        ]);

        Shelter::factory()->create([
            'name' => 'Guéret',
        ]);
    }
}
