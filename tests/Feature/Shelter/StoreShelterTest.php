<?php

namespace Tests\Feature\Shelter;

use App\Models\User;
use Tests\TestCase;

class StoreShelterTest extends TestCase
{
    protected User $user;

    protected string $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->route = route('api.shelter.store');
    }

    public function test_cannot_create_new_shelter(): void
    {
        $response = $this->authenticate($this->user)->postJson($this->route);

        $response->assertForbidden();
    }
}
