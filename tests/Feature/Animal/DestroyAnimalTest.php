<?php

namespace Tests\Feature\Animal;

use App\Models\Animal;
use App\Models\User;
use Tests\TestCase;

class DestroyAnimalTest extends TestCase
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
        ]);

        $this->route = route('api.animal.destroy', $this->animal);
    }

    public function test_can_delete_an_animal(): void
    {
        $response = $this->authenticate($this->user)->deleteJson($this->route);

        $response->assertNoContent();

        $this->assertModelMissing($this->animal);
    }

    public function test_unauthenticated_user_cannot_delete_an_animal(): void
    {
        $response = $this->deleteJson($this->route);

        $response->assertUnauthorized();
    }

    public function test_cannot_destroy_an_animal_outside_user_scope(): void
    {
        $animalNotInUserShelter = Animal::factory()->create();

        $response = $this->authenticate($this->user)->deleteJson(route('api.animal.destroy', $animalNotInUserShelter));

        $response->assertForbidden();
    }
}
