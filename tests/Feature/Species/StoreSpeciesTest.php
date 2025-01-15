<?php

namespace Tests\Feature\Species;

use App\Models\Species;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class StoreSpeciesTest extends TestCase
{
    protected User $user;

    protected string $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->route = route('api.species.store');
    }

    public function test_can_create_an_species(): void
    {
        $description = "Le Chien (Canis lupus familiaris) est la sous-espèce domestique de Canis lupus (Loup gris), un mammifère de la famille des Canidés (Canidae), laquelle comprend également le dingo, chien domestique retourné à l'état sauvage.";

        $response = $this->authenticate($this->user)->postJson($this->route, [
            'name' => 'Chien',
            'description' => $description,
        ]);

        $response->assertCreated();

        $species = Species::where('name', 'Chien')->first();

        $response->assertJson(function (AssertableJson $json) use ($species) {
            $json->has('data', function (AssertableJson $json) use ($species) {
                return $json->where('name', $species->name)
                            ->where('description', $species->description)
                            ->etc();
            });
        });
    }

    public function test_validation_rules_are_applied(): void
    {
        $response = $this->authenticate($this->user)->postJson($this->route);

        $response->assertJsonValidationErrors([
            'name',
        ]);
    }

    public function test_unauthenticated_user_cannot_store_an_animal(): void
    {
        $response = $this->postJson($this->route, [
            'name' => 'Chien',
        ]);

        $response->assertUnauthorized();
    }
}
