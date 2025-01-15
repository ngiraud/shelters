<?php

namespace Tests\Feature\Animal;

use App\Models\Animal;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ListAnimalTest extends TestCase
{
    protected User $user;

    protected string $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->route = route('api.animal.index');
    }

    public function test_can_list_animals_related_to_shelter_user(): void
    {
        Animal::factory()->for($this->user->shelter)->create(['name' => 'Gordon']);
        $this->travel(5)->minutes();
        Animal::factory()->for($this->user->shelter)->create(['name' => 'Houdini']);

        $response = $this->authenticate($this->user)->getJson($this->route);

        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) {
            // Animals should also be ordered by created_at column DESC
            $json->has('data', 2)
                 ->where('data.0.name', 'Houdini')
                 ->where('data.1.name', 'Gordon')
                 ->etc();
        });
    }

    public function test_cannot_list_animals_from_other_shelters(): void
    {
        Shelter::factory()
               ->has(Animal::factory()->count(10))
               ->count(5)
               ->create();

        $response = $this->authenticate($this->user)->getJson($this->route);

        $response->assertOk();

        $response->assertJsonCount(0, 'data');
    }

    public function test_unauthenticated_user_cannot_list_animals(): void
    {
        $response = $this->getJson($this->route);

        $response->assertUnauthorized();
    }
}
