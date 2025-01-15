<?php

namespace Tests\Feature\Species;

use App\Models\Animal;
use App\Models\Species;
use App\Models\User;
use Database\Seeders\SpeciesSeeder;
use Tests\TestCase;

class DestroySpeciesTest extends TestCase
{
    protected User $user;

    protected Species $species;

    protected Species $uncategorizedSpecies;

    protected string $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->species = Species::factory()->create([
            'name' => 'Chien',
        ]);

        $this->seed(SpeciesSeeder::class);

        $this->uncategorizedSpecies = Species::where('name', 'Uncategorized')->firstOrFail();

        $this->route = route('api.species.destroy', $this->species);
    }

    public function test_can_delete_a_species_without_animals_attached(): void
    {
        $response = $this->authenticate($this->user)->deleteJson($this->route);

        $response->assertNoContent();

        $this->assertModelMissing($this->species);
    }

    public function test_deleting_a_species_with_animals_attached_will_change_their_species_to_uncategorized(): void
    {
        $animals = Animal::factory()->for($this->species)->count(5)->create();

        $response = $this->authenticate($this->user)->deleteJson($this->route);

        $response->assertNoContent();

        $this->assertModelMissing($this->species);

        $uncategorizedSpecies = Species::where('name', 'Uncategorized')->first();

        $animals->each(function (Animal $animal) use ($uncategorizedSpecies) {
            $this->assertTrue($animal->refresh()->species->is($uncategorizedSpecies));
        });
    }

    public function test_unauthenticated_user_cannot_delete_a_species(): void
    {
        $response = $this->deleteJson($this->route);

        $response->assertUnauthorized();
    }
}
