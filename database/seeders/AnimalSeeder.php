<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Shelter;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class AnimalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shelters = Shelter::all();

        $species = [
            'Chien',
            'Chat',
            'Oiseau',
            'NAC',
            'Poisson',
            'Ane',
            'Cheval'
        ];

        foreach ($species as $specie) {
            Species::factory()->create(['name' => $specie]);
        }

        $species = Species::all();

        Animal::factory()
              ->count(10000)
              ->state(new Sequence(
                  fn(Sequence $sequence) => [
                      'shelter_id' => $shelters->random(),
                      'species_id' => $species->random(),
                  ],
              ))
              ->create();
    }
}
