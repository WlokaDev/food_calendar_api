<?php

namespace Tests\Feature;

use App\Enums\AcceptableChangeActionTypeEnum;
use App\Enums\CurrencyEnum;
use App\Enums\UnitEnum;
use App\Models\AcceptableChange;
use App\Models\Image;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeProduct;
use App\Models\RecipeStage;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RecipesTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * @var Collection|Model|User
     */

    protected User|Collection|Model $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testStoreNewRecipeWithProvideValidatedData()
    {
        Sanctum::actingAs(
            $this->user
        );

        Storage::fake('s3');
        app()->setLocale('pl');

        $data = [
            'title' => 'Dobra zupa',
            'description' => $this->faker->text,
            'price' => 10,
            'currency' => 'zł',
            'calories' => 956,
            'difficulty_of_execution' => 2,
            'execution_time' => 40,
            'stages' => [
                [
                    'sort' => 0,
                    'description' => $this->faker->text
                ],
                [
                    'sort' => 1,
                    'description' => $this->faker->text
                ]
            ],
            'images' => [
                [
                    'file' => UploadedFile::fake()->image('a.jpg'),
                    'main' => true
                ],
                [
                    'file' => UploadedFile::fake()->image('b.jpg'),
                    'main' => false
                ],
                [
                    'file' => UploadedFile::fake()->image('c.jpg'),
                    'main' => false
                ]
            ],
            'products' => [
                [
                    'id' => Product::factory()->create()->id,
                    'unit' => UnitEnum::GRAM->value,
                    'value' => "20"
                ],
                [
                    'id' => Product::factory()->create()->id,
                    'unit' => UnitEnum::LITERS->value,
                    'value' => "0.5"
                ],
                [
                    'id' => Product::factory()->create()->id,
                    'unit' => UnitEnum::PIECE->value,
                    'value' => "5"
                ],
            ]
        ];

        $response = $this->post('/api/v1/recipes', $data);
        $response->assertStatus(200);

        $this->assertEquals([
            'title' => [
                'pl' => 'Dobra zupa'
            ],
            'description' => [
                'pl' => $data['description']
            ],
            'price' => 10,
            'currency' => 'zł',
            'calories' => 956,
            'difficulty_of_execution' => 2,
            'execution_time' => 40
        ], AcceptableChange
            ::query()
            ->where('model', Recipe::class)
            ->first()
            ->changed_attributes
        );

        $this->assertEquals([
            'sort' => 0,
            'description' => [
                'pl' => $data['stages'][0]['description']
            ]
        ], AcceptableChange
            ::query()
            ->where('model', RecipeStage::class)
            ->whereJsonContains('changed_attributes->sort', 0)
            ->first()
            ->changed_attributes
        );

        $this->assertEquals([
            'sort' => 1,
            'description' => [
                'pl' => $data['stages'][1]['description']
            ]
        ], AcceptableChange
            ::query()
            ->where('model', RecipeStage::class)
            ->whereJsonContains('changed_attributes->sort', 1)
            ->first()
            ->changed_attributes
        );

        $this->assertEquals([
            'product_id' => $data['products'][0]['id'],
            'unit' => UnitEnum::GRAM->value,
            'value' => 20
        ], AcceptableChange
            ::query()
            ->where('model', RecipeProduct::class)
            ->whereJsonContains('changed_attributes->product_id', $data['products'][0]['id'])
            ->first()
            ->changed_attributes
        );

        $this->assertEquals([
            'product_id' => $data['products'][1]['id'],
            'unit' => UnitEnum::LITERS->value,
            'value' => 0.5
        ], AcceptableChange
            ::query()
            ->where('model', RecipeProduct::class)
            ->whereJsonContains('changed_attributes->product_id', $data['products'][1]['id'])
            ->first()
            ->changed_attributes
        );

        $this->assertEquals(3, AcceptableChange
            ::query()
            ->where('model', Image::class)
            ->count()
        );

        $this->assertDatabaseCount(Recipe::class, 0);
        $this->assertDatabaseCount(Image::class, 0);
        $this->assertDatabaseCount(RecipeStage::class, 0);
        $this->assertDatabaseCount(RecipeProduct::class, 0);
    }

    public function testUpdateRecipeWhenProvidedValidatedData()
    {
        Sanctum::actingAs(
            $this->user
        );

        Storage::fake('s3');
        app()->setLocale('pl');

        $recipe = Recipe::factory()->create([
            'title' => $this->faker->name,
            'description' => $this->faker->text,
            'price' => 10.20,
            'currency' => CurrencyEnum::PLN->value,
            'calories' => $this->faker->numberBetween(1000, 2000),
            'user_id' => $this->user->id,
            'difficulty_of_execution' => 2,
            'execution_time' => 40,
        ]);

        $stage = RecipeStage::factory()->create([
            'recipe_id' => $recipe->id,
            'description' => $this->faker->text,
            'sort' => 0
        ]);

        $product = Product::factory()->create();

        $recipeProduct = RecipeProduct::factory()
            ->for($product, 'product')
            ->for($recipe, 'recipe')
            ->create([
                'unit' => UnitEnum::GRAM->value,
                'value' => 30
            ]);

        $data = [
            'title' => 'Dobra zupa',
            'description' => $this->faker->text,
            'price' => 10,
            'currency' => 'zł',
            'calories' => 956,
            'difficulty_of_execution' => 5,
            'execution_time' => 35,
            'stages' => [
                [
                    'sort' => 1,
                    'description' => $this->faker->text,
                    'id' => $stage->id
                ],
                [
                    'sort' => 0,
                    'description' => $this->faker->text
                ]
            ],
            'products' => [
                [
                    'id' => $product->id,
                    'unit' => UnitEnum::GRAM->value,
                    'value' => "20"
                ],
                [
                    'id' => Product::factory()->create()->id,
                    'unit' => UnitEnum::LITERS->value,
                    'value' => "0.5"
                ],
                [
                    'id' => Product::factory()->create()->id,
                    'unit' => UnitEnum::PIECE->value,
                    'value' => "5"
                ],
            ]
        ];

        $response = $this->put("/api/v1/recipes/$recipe->id", $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas(Recipe::class, [
            'title->pl' => $recipe->title,
            'description->pl' => $recipe->description,
            'price' => 10.20,
            'currency' => CurrencyEnum::PLN->value,
            'calories' => $recipe->calories,
            'user_id' => $this->user->id,
            'difficulty_of_execution' => 2,
            'execution_time' => 40,
        ]);

        $this->assertDatabaseHas(RecipeStage::class, [
            'description->pl' => $stage->description,
            'recipe_id' => $recipe->id,
            'sort' => 0
        ]);

        $this->assertDatabaseHas(RecipeProduct::class, [
            'id' => $recipeProduct->id,
            'unit' => UnitEnum::GRAM->value,
            'value' => 30
        ]);

        $this->assertEquals([
            'title' => [
                'pl' => 'Dobra zupa'
            ],
            'description' => [
                'pl' => $data['description']
            ],
            'price' => 10,
            'currency' => 'zł',
            'calories' => 956,
            'difficulty_of_execution' => 5,
            'execution_time' => 35,
            'user_id' => $this->user->id,
            'id' => $recipe->id,
            'created_at' => $recipe->created_at->toISOString(),
            'updated_at' => $recipe->created_at->toISOString(),
            'deleted_at' => null
        ], AcceptableChange
            ::query()
            ->where('model', Recipe::class)
            ->first()
            ->changed_attributes
        );

        $this->assertEquals([
            'sort' => 1,
            'description' => [
                'pl' => $data['stages'][0]['description']
            ],
            'id' => $stage->id,
            'recipe_id' => $recipe->id,
            'created_at' => $recipe->created_at->toISOString(),
            'updated_at' => $recipe->created_at->toISOString(),
            'deleted_at' => null
        ], AcceptableChange
            ::query()
            ->where('model', RecipeStage::class)
            ->whereJsonContains('changed_attributes->sort', 1)
            ->first()
            ->changed_attributes
        );

        $this->assertEquals([
            'sort' => 0,
            'description' => [
                'pl' => $data['stages'][1]['description']
            ]
        ], AcceptableChange
            ::query()
            ->where('model', RecipeStage::class)
            ->whereJsonContains('changed_attributes->sort', 0)
            ->first()
            ->changed_attributes
        );

        $this->assertEquals([
            'product_id' => $data['products'][0]['id'],
            'unit' => UnitEnum::GRAM->value,
            'value' => '20',
            'id' => $recipeProduct->id,
            'recipe_id' => $recipe->id,
            'created_at' => $recipe->created_at->toISOString(),
            'updated_at' => $recipe->created_at->toISOString(),
            'deleted_at' => null
        ], AcceptableChange
            ::query()
            ->where('model', RecipeProduct::class)
            ->whereJsonContains('changed_attributes->product_id', $data['products'][0]['id'])
            ->first()
            ->changed_attributes
        );

        $this->assertEquals([
            'product_id' => $data['products'][1]['id'],
            'unit' => UnitEnum::LITERS->value,
            'value' => 0.5
        ], AcceptableChange
            ::query()
            ->where('model', RecipeProduct::class)
            ->whereJsonContains('changed_attributes->product_id', $data['products'][1]['id'])
            ->first()
            ->changed_attributes
        );
    }
}
