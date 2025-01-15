<?php

namespace Tests\Feature\Shelter;

use App\Models\Shelter;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateShelterTest extends TestCase
{
    protected User $user;

    protected Shelter $shelter;

    protected string $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shelter = Shelter::factory()->create([
            'name' => 'My shelter',
        ]);

        $this->user = User::factory()->for($this->shelter)->create();

        $this->route = route('api.shelter.update', $this->shelter);
    }

    public function test_can_update_authenticated_user_shelter(): void
    {
        $response = $this->authenticate($this->user)->putJson($this->route, [
            'name' => 'New name for shelter',
            'address_line_1' => '23 rue des refuges',
            'address_line_2' => 'Le Refuge',
            'postcode' => '87000',
            'city' => 'Limoges',
            'phone_number' => '+33555098765',
        ]);

        $response->assertOk();

        $this->shelter->refresh();

        $this->assertEquals('New name for shelter', $this->shelter->name);
        $this->assertEquals('23 rue des refuges', $this->shelter->address_line_1);
        $this->assertEquals('Le Refuge', $this->shelter->address_line_2);
        $this->assertEquals('87000', $this->shelter->postcode);
        $this->assertEquals('Limoges', $this->shelter->city);
        $this->assertEquals('+33555098765', $this->shelter->phone_number);

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

    public function test_validation_rules_are_applied(): void
    {
        $response = $this->authenticate($this->user)->putJson($this->route);

        $response->assertJsonValidationErrors([
            'name',
            'address_line_1',
            'postcode',
            'city',
        ]);
    }

    public function test_postcode_must_not_be_greater_than_50_chars(): void
    {
        $response = $this->authenticate($this->user)->putJson($this->route, [
            'postcode' => Str::random(60),
        ]);

        $response->assertJsonValidationErrorFor('postcode');
    }

    public function test_phone_number_must_not_be_greater_than_20_chars(): void
    {
        $response = $this->authenticate($this->user)->putJson($this->route, [
            'phone_number' => Str::random(30),
        ]);

        $response->assertJsonValidationErrorFor('phone_number');
    }

    public function test_unauthenticated_user_cannot_update_an_animal(): void
    {
        $response = $this->putJson($this->route);

        $response->assertUnauthorized();
    }

    public function test_user_can_only_update_his_shelter(): void
    {
        $user = User::factory()->create();

        $response = $this->authenticate($user)->putJson($this->route, [
            'name' => 'New name for shelter',
            'address_line_1' => '23 rue des refuges',
            'address_line_2' => 'Le Refuge',
            'postcode' => '87000',
            'city' => 'Limoges',
            'phone_number' => '+33555098765',
        ]);

        $response->assertForbidden();
    }
}
