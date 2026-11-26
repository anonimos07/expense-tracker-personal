<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['type' => ['nullable', 'in:expense,income']]);

        $categories = $request->user()->categories()
            ->when($request->query('type'), fn ($q, $type) => $q->where('type', $type))
            ->withCount('transactions')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function store(CategoryRequest $request)
    {
        $category = $request->user()->categories()->create($request->validated());

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    public function show(Request $request, int $id)
    {
        $category = $request->user()->categories()->withCount('transactions')->findOrFail($id);

        return new CategoryResource($category);
    }

    public function update(CategoryRequest $request, int $id)
    {
        $category = $request->user()->categories()->findOrFail($id);
        $category->update($request->validated());

        return new CategoryResource($category);
    }

    public function destroy(Request $request, int $id)
    {
        $request->user()->categories()->findOrFail($id)->delete();

        return response()->noContent();
    }
}