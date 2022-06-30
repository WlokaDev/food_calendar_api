<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserPreferencesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Collection|Model|User
     */

    protected User|Collection|Model $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testAddingNewPreferencesForUserWhenDataAreCorrect()
    {
        Sanctum::actingAs(
            $this->user
        );

        $data = [
            'max_price' => 18.40,
            'min_calories' => 800,
            'max_difficulty_of_execution' => 6,
            'max_execution_time' => 30
        ];

        $response = $this->post('/api/v1/users/preferences', $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas(UserPreference::class, [
            'max_price' => 18.40,
            'min_calories' => 800,
            'max_difficulty_of_execution' => 6,
            'max_execution_time' => 30
        ]);
    }

    public function testUpdatePreferencesForUserWhenDataAreCorrect()
    {
        Sanctum::actingAs(
            $this->user
        );

        $data = [
            'max_price' => 18.40,
            'min_calories' => 800,
            'max_difficulty_of_execution' => 6,
            'max_execution_time' => 30
        ];

        UserPreference::factory()->for($this->user, 'user')->create($data);

        $response = $this->post('/api/v1/users/preferences', [
            'min_price' => 1,
            'max_price' => 18.40,
            'min_calories' => 800,
            'max_difficulty_of_execution' => 6,
            'max_execution_time' => 45
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas(UserPreference::class, [
            'min_price' => 1,
            'max_price' => 18.40,
            'min_calories' => 800,
            'max_difficulty_of_execution' => 6,
            'max_execution_time' => 45
        ]);
    }

    public function updateExcludedProductsListForUser()
    {
        Sanctum::actingAs(
            $this->user
        );

        $products = Product::factory(10)->create();

        $response = $this->post('/api/v1/users/excluded-products', [
            'products' => $products->pluck('id')->toArray()
        ]);

        $response->assertStatus(200);

        $this->assertEquals(10, $this->user->excludedProducts()->count());
    }
}
