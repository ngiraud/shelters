<?php

namespace Tests\Feature\Shelter;

use App\Models\Shelter;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ShowShelterTest extends TestCase
{
    protected User $user;

    protected Shelter $shelter;

    protected string $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shelter = Shelter::factory()->create();

        $this->user = User::factory()->for($this->shelter)->create();

        $this->route = route('api.shelter.show', $this->shelter);
    }

    public function test_can_show_a_shelter(): void
    {
        $response = $this->authenticate($this->user)->getJson($this->route);

        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                return $json->where('name', $this->shelter->name)
                            ->where('address_line_1', $this->shelter->address_line_1)
                            ->where('address_line_2', $this->shelter->address_line_2)
                            ->where('postcode', $this->shelter->postcode)
                            ->where('city', $this->shelter->city)
                            ->where('phone_number', $this->shelter->phone_number)
                            ->etc();
            });
        });
    }

    public function test_unauthenticated_user_cannot_show_a_shelter(): void
    {
        $response = $this->getJson($this->route);

        $response->assertUnauthorized();
    }
}
