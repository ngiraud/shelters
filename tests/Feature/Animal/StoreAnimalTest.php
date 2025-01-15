<?php

namespace Tests\Feature\Animal;

use App\Enums\AnimalGender;
use App\Models\Animal;
use App\Models\Species;
use App\Models\User;
use Database\Seeders\SpeciesSeeder;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class StoreAnimalTest extends TestCase
{
    protected User $user;

    protected string $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->route = route('api.animal.store');

        $this->seed(SpeciesSeeder::class);
    }

    public function test_can_create_an_animal(): void
    {
        $species = Species::factory()->create();

        $response = $this->authenticate($this->user)->postJson($this->route, [
            'name' => 'Gordon',
            'description' => 'Because he looks like Gordon from Batman',
            'birthdate' => '2024-02-04',
            'gender' => AnimalGender::Male,
            'species_id' => $species->id,
        ]);

        $response->assertCreated();

        $animal = Animal::where('name', 'Gordon')->first();

        $response->assertJson(function (AssertableJson $json) use ($species, $animal) {
            $json->has('data', function (AssertableJson $json) use ($species, $animal) {
                return $json->where('name', $animal->name)
                            ->where('description', $animal->description)
                            ->where('birthdate', $animal->birthdate->toJSON())
                            ->where('gender', $animal->gender)
                            ->where('species_id', $species->id)
                            ->etc();
            });
        });
    }

    public function test_validation_rules_are_applied(): void
    {
        $response = $this->authenticate($this->user)->postJson($this->route);

        $response->assertJsonValidationErrors([
            'name',
            'description',
            'birthdate',
            'species_id',
        ]);
    }

    public function test_birthdate_must_be_a_valid_date(): void
    {
        $response = $this->authenticate($this->user)->postJson($this->route, [
            'birthdate' => 'not-a-valid-date',
        ]);

        $response->assertJsonValidationErrorFor('birthdate');
    }

    public function test_species_must_exist_and_be_valid(): void
    {
        $response = $this->authenticate($this->user)->postJson($this->route, [
            'species_id' => 'not-a-valid-species',
        ]);

        $response->assertJsonValidationErrorFor('species_id');
    }

    public function test_gender_must_be_valid(): void
    {
        $response = $this->authenticate($this->user)->postJson($this->route, [
            'gender' => 'invalid-gender',
        ]);

        $response->assertJsonValidationErrorFor('gender');
    }

    public function test_unauthenticated_user_cannot_store_an_animal(): void
    {
        $response = $this->postJson($this->route);

        $response->assertUnauthorized();
    }
}
