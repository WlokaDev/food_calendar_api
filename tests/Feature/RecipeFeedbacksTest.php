<?php

namespace Tests\Feature;

use App\Enums\CurrencyEnum;
use App\Models\AcceptableChange;
use App\Models\Recipe;
use App\Models\RecipeFeedback;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RecipeFeedbacksTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var Collection|Model|User
     */

    protected User|Collection|Model $user;

    /**
     * @var Recipe|Collection|Model
     */

    protected Recipe|Collection|Model $recipe;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->recipe = Recipe::factory()->create([
            'title' => $this->faker->name,
            'description' => $this->faker->text,
            'price' => 10.20,
            'currency' => CurrencyEnum::PLN->value,
            'calories' => $this->faker->numberBetween(1000, 2000),
            'user_id' => $this->user->id,
            'difficulty_of_execution' => 2,
            'execution_time' => 40,
        ]);
    }

    public function testAddNewFeedbackToRecipeWhenFeedbackTextProvided()
    {
        Storage::fake('s3');
        app()->setLocale('pl');

        Sanctum::actingAs(
            $this->user
        );

        $data = [
            'feedback' => $this->faker->text,
            'images' => [
                UploadedFile::fake()->image('image_1.jpg'),
                UploadedFile::fake()->image('image_2.jpg'),
                UploadedFile::fake()->image('image_3.jpg')
            ]
        ];

        $response = $this->post("/api/v1/recipes/{$this->recipe->id}/feedbacks", $data);
        $response->assertStatus(200);
        $this->assertDatabaseCount(AcceptableChange::class, 4);
        $this->assertDatabaseCount(RecipeFeedback::class, 0);
    }

    public function testAddNewFeedbackToRecipeWhenFeedbackStarProvided()
    {
        Storage::fake('s3');
        app()->setLocale('pl');

        Sanctum::actingAs(
            $this->user
        );

        $data = [
            'feedback_star' => 4,
            'images' => [
                UploadedFile::fake()->image('image_1.jpg'),
                UploadedFile::fake()->image('image_2.jpg'),
                UploadedFile::fake()->image('image_3.jpg')
            ]
        ];

        $response = $this->post("/api/v1/recipes/{$this->recipe->id}/feedbacks", $data);
        $response->assertStatus(200);
        $this->assertDatabaseCount(AcceptableChange::class, 4);
        $this->assertDatabaseCount(RecipeFeedback::class, 0);
    }
}
