<?php

namespace Tests\Feature\Species;

use App\Models\Species;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ShowSpeciesTest extends TestCase
{
    protected User $user;

    protected Species $species;

    protected string $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->species = Species::factory()->create([
            'name' => 'Chien',
        ]);

        $this->route = route('api.species.show', $this->species);
    }

    public function test_can_show_a_species(): void
    {
        $response = $this->authenticate($this->user)->getJson($this->route);

        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                return $json->where('name', $this->species->name)
                            ->etc();
            });
        });
    }

    public function test_unauthenticated_user_cannot_show_a_species(): void
    {
        $response = $this->getJson($this->route);

        $response->assertUnauthorized();
    }
}
