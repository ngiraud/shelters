<?php

namespace Tests\Feature\Animal;

use App\Enums\AnimalGender;
use App\Models\Animal;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateAnimalTest extends TestCase
{
    protected User $user;

    protected Animal $animal;

    protected string $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->animal = Animal::factory()->for($this->user->shelter)->create([
            'name' => 'Houdini',
            'description' => 'This is actually my dog.',
            'birthdate' => '2013-09-01',
            'gender' => AnimalGender::Male,
        ]);

        $this->route = route('api.animal.update', $this->animal);
    }

    public function test_can_update_an_animal(): void
    {
        $response = $this->authenticate($this->user)->putJson($this->route, [
            'name' => 'Gordon',
            'description' => 'Because he looks like Gordon from Batman',
            'birthdate' => '2024-02-04',
            'gender' => AnimalGender::Male,
        ]);

        $response->assertOk();

        $this->animal->refresh();

        $this->assertEquals('Gordon', $this->animal->name);
        $this->assertEquals('Because he looks like Gordon from Batman', $this->animal->description);
        $this->assertEquals('2024-02-04', $this->animal->birthdate->toDateString());

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                return $json->where('name', $this->animal->name)
                            ->where('description', $this->animal->description)
                            ->where('birthdate', $this->animal->birthdate->toJSON())
                            ->where('gender', $this->animal->gender)
                            ->etc();
            });
        });
    }

    public function test_validation_rules_are_applied(): void
    {
        $response = $this->authenticate($this->user)->putJson($this->route);

        $response->assertJsonValidationErrors([
            'name',
            'description',
            'birthdate',
        ]);
    }

    public function test_birthdate_must_be_a_valid_date(): void
    {
        $response = $this->authenticate($this->user)->putJson($this->route, [
            'birthdate' => 'not-a-valid-date',
        ]);

        $response->assertJsonValidationErrorFor('birthdate');
    }

    public function test_gender_must_be_valid(): void
    {
        $response = $this->authenticate($this->user)->putJson($this->route, [
            'gender' => 'invalid-gender',
        ]);

        $response->assertJsonValidationErrorFor('gender');
    }

    public function test_unauthenticated_user_cannot_update_an_animal(): void
    {
        $response = $this->putJson($this->route);

        $response->assertUnauthorized();
    }

    public function test_cannot_update_an_animal_outside_user_scope(): void
    {
        $animalNotInUserShelter = Animal::factory()->create();

        $response = $this->authenticate($this->user)->putJson(route('api.animal.update', $animalNotInUserShelter), [
            'name' => 'Gordon',
            'description' => 'Because he looks like Gordon from Batman',
            'birthdate' => '2024-02-04',
            'gender' => AnimalGender::Male,
        ]);

        $response->assertForbidden();
    }
}
