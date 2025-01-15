<?php

namespace Tests\Feature\Species;

use App\Models\Species;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateSpeciesTest extends TestCase
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

        $this->route = route('api.species.update', $this->species);
    }

    public function test_can_update_a_species(): void
    {
        $description = "Le Chien (Canis lupus familiaris) est la sous-espèce domestique de Canis lupus (Loup gris), un mammifère de la famille des Canidés (Canidae), laquelle comprend également le dingo, chien domestique retourné à l'état sauvage.";

        $response = $this->authenticate($this->user)->putJson($this->route, [
            'name' => 'Chien',
            'description' => $description,
        ]);

        $response->assertOk();

        $this->species->refresh();

        $this->assertEquals('Chien', $this->species->name);
        $this->assertEquals($description, $this->species->description);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                return $json->where('name', $this->species->name)
                            ->where('description', $this->species->description)
                            ->etc();
            });
        });
    }

    public function test_validation_rules_are_applied(): void
    {
        $response = $this->authenticate($this->user)->putJson($this->route);

        $response->assertJsonValidationErrors([
            'name',
        ]);
    }

    public function test_unauthenticated_user_cannot_update_a_species(): void
    {
        $response = $this->putJson($this->route, [
            'name' => 'Chien',
        ]);

        $response->assertUnauthorized();
    }
}
