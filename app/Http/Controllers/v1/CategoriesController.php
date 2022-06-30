<?php

namespace App\Http\Controllers\v1;

use App\Enums\CategorySourceEnum;
use App\Http\Resources\v1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CategoriesController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'source' => ['required', new Enum(CategorySourceEnum::class)]
        ]);

        $categories = Category::query()
            ->where('source', $data['source'])
            ->get();

        return $this->successResponse(
            CategoryResource::collection(
                $categories
            )
        );
    }

    /**
     * @param Category $category
     * @return StreamedResponse
     */

    public function showIcon(Category $category) : StreamedResponse
    {
        if(Storage::disk('s3')->exists($category->icon_path)) {
            return Storage::disk('s3')->download($category->icon_path);
        }

        abort(404);
    }
}
