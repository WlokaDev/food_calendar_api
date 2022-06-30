<?php

namespace Tests\Feature;

use App\Enums\AcceptableChangeActionTypeEnum;
use App\Enums\AcceptableChangeStatusEnum;
use App\Models\AcceptableChange;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AcceptableChangesTest extends TestCase
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

    public function testAcceptChangesWithAddingProduct() : void
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = ProductCategory::factory()->create();

        $acceptableChangeProduct = AcceptableChange::factory()->create([
            'model' => Product::class,
            'model_id' => null,
            'author_id' => $this->user->id,
            'action' => AcceptableChangeActionTypeEnum::NEW,
            'changed_attributes' => [
                'category_id' => $category->id,
                'image_path' => 'images/products/abc.jpg',
                'name' => ['pl' => 'Jabłko'],
                'description' => ['pl' => 'Dobre jabłko']
            ],
            'status' => AcceptableChangeStatusEnum::PROCESSING
        ]);


        $response = $this->post("/api/v1/admin-panel/changes-for-accept/$acceptableChangeProduct->id/accept");
        $response->assertStatus(200);
        $this->assertDatabaseHas(Product::class, [
            'image_path' => 'images/products/abc.jpg',
            'category_id' => $category->id
        ]);
        $this->assertDatabaseHas(AcceptableChange::class, [
            'id' => $acceptableChangeProduct->id,
            'status' => AcceptableChangeStatusEnum::ACCEPTED->value
        ]);
    }

    public function testRejectChangesWithAddingProductAndTranslations()
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = ProductCategory::factory()->create();

        $acceptableChangeProduct = AcceptableChange::factory()->create([
            'model' => Product::class,
            'model_id' => null,
            'author_id' => $this->user->id,
            'action' => AcceptableChangeActionTypeEnum::NEW->value,
            'changed_attributes' => [
                'category_id' => $category->id,
                'image_path' => 'images/products/abc.jpg',
                'name' => ['pl' => 'Jabłko'],
                'description' => ['pl' => 'Dobre jabłko']
            ],
            'status' => AcceptableChangeStatusEnum::PROCESSING->value
        ]);

        $response = $this->post("/api/v1/admin-panel/changes-for-accept/$acceptableChangeProduct->id/reject", [
            'reason' => 'Example reason'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseMissing(Product::class, [
            'image_path' => 'images/products/abc.jpg',
            'category_id' => $category->id
        ]);
        $this->assertDatabaseHas(AcceptableChange::class, [
            'id' => $acceptableChangeProduct->id,
            'status' => AcceptableChangeStatusEnum::REJECTED->value,
        ]);
    }

    public function testFetchAcceptableChanges() : void
    {
        Sanctum::actingAs(
            $this->user
        );

        $category = ProductCategory::factory()->create();


        AcceptableChange::factory()->count(30)->create([
            'model' => Product::class,
            'model_id' => null,
            'author_id' => $this->user->id,
            'action' => AcceptableChangeActionTypeEnum::NEW->value,
            'changed_attributes' => [
                'category_id' => $category->id,
                'image_path' => 'images/products/abc.jpg',
                'name' => ['pl' => 'Jabłko'],
                'description' => ['pl' => 'Dobre jabłko']
            ],
            'status' => AcceptableChangeStatusEnum::PROCESSING->value
        ]);

        $response = $this->get('/api/v1/admin-panel/changes-for-accept?per_page=20');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'model',
                        'model_id',
                        'action',
                        'status',
                        'changed_attributes',
                        'created_at'
                    ]
                ],
                'pagination' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                    'total_pages'
                ]
            ],
            'code'
        ]);

        $this->assertEquals(20, $response['data']['pagination']['count']);
    }
}
