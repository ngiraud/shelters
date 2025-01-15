<?php

namespace Tests\Feature\Shelter;

use App\Models\Shelter;
use App\Models\User;
use Tests\TestCase;

class DestroyShelterTest extends TestCase
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

        $this->route = route('api.shelter.destroy', $this->shelter);
    }

    public function test_cannot_destroy_shelter(): void
    {
        $response = $this->authenticate($this->user)->deleteJson($this->route);

        $response->assertForbidden();
    }
}
