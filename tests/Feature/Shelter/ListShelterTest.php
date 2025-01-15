<?php

namespace Tests\Feature\Shelter;

use App\Models\Shelter;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ListShelterTest extends TestCase
{
    protected User $user;

    protected string $route;

    protected function setUp(): void
    {
        parent::setUp();

        $shelter = Shelter::factory()->create(['name' => 'Limoges Shelter']);

        $this->user = User::factory()->for($shelter)->create();

        $this->route = route('api.shelter.index');
    }

    public function test_can_list_shelters(): void
    {
        Shelter::factory()->create(['name' => 'Guéret Shelter']);
        $this->travel(5)->minutes();
        Shelter::factory()->create(['name' => 'Paris Shelter']);

        $response = $this->authenticate($this->user)->getJson($this->route);

        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) {
            // Shelters should be ordered by name column ASC
            // "Limoges Shelter" is the auth user shelter created in setUp
            $json->has('data', 3)
                 ->where('data.0.name', 'Guéret Shelter')
                 ->where('data.1.name', 'Limoges Shelter')
                 ->where('data.2.name', 'Paris Shelter')
                 ->etc();
        });
    }

    public function test_unauthenticated_user_cannot_list_shelters(): void
    {
        $response = $this->getJson($this->route);

        $response->assertUnauthorized();
    }
}
