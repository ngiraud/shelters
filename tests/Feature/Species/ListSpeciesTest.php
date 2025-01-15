<?php

namespace Tests\Feature\Species;

use App\Models\Species;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ListSpeciesTest extends TestCase
{
    protected User $user;

    protected string $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->route = route('api.species.index');
    }

    public function test_can_list_species(): void
    {
        Species::factory()->create(['name' => 'Chien']);
        Species::factory()->create(['name' => 'Chat']);
        Species::factory()->create(['name' => 'Lapin']);

        $response = $this->authenticate($this->user)->getJson($this->route);

        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) {
            // Species should also be ordered by name column ASC
            $json->has('data', 3)
                 ->where('data.0.name', 'Chat')
                 ->where('data.1.name', 'Chien')
                 ->where('data.2.name', 'Lapin')
                 ->etc();
        });
    }

    public function test_unauthenticated_user_cannot_list_species(): void
    {
        $response = $this->getJson($this->route);

        $response->assertUnauthorized();
    }
}
