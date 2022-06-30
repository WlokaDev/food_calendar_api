<?php

namespace Tests\Feature;

use App\Models\AcceptableChange;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Collection|Model|ProductCategory
     */

    protected ProductCategory|Collection|Model $productCategory;

    /**
     * @var Collection|Model|User
     */

    protected User|Collection|Model $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productCategory = ProductCategory::factory()->create();
        $this->user = User::factory()->create();

    }

    public function testStoreNewProductForAcceptWhenAllDataAreProvidedAndAreCorrect()
    {
        Sanctum::actingAs(
            $this->user
        );

        $data = [
            'name' => 'Jabłkoo',
            'description' => 'Dobry owoc',
            'category_id' => $this->productCategory->id,
            'image' => UploadedFile::fake()->image('apple.jpg', 600, 600)->size(1000)
        ];

        $response = $this->post('/api/v1/products', $data);
        $response->assertStatus(200);
        $this->assertDatabaseCount(Product::class, 0);
        $this->assertDatabaseHas(AcceptableChange::class,
        [
            'model' => Product::class
        ]);
    }

    public function testUpdateProductForAcceptWhenAllDataAreProvidedAndAreCorrect()
    {
        Sanctum::actingAs(
            $this->user
        );

        app()->setLocale('pl');

        $product = Product
            ::factory()
            ->for($this->productCategory, 'category')
            ->create([
                'name' => ['pl' => 'Test'],
                'description' => ['pl' => 'Test example']
            ]);

        $data = [
            'name' => 'Jabłkoo',
            'description' => 'Dobry owoc',
            'category_id' => $this->productCategory->id,
            'image' => UploadedFile::fake()->image('apple.jpg', 600, 600)->size(1000)
        ];

        $response = $this->put("/api/v1/products/$product->id", $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas(Product::class, [
            'name->pl' => $product->name,
            'description->pl' => $product->description,
            'category_id' => $product->category->id,
            'image_path' => $product->image_path
        ]);
        $this->assertDatabaseHas(AcceptableChange::class,
            [
                'model' => Product::class
            ]);
    }
}
