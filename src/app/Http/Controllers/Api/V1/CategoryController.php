<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::all();

        return CategoryResource::collection($categories);
    }

    public function tree()
    {
        $this->authorize('viewAny', \App\Models\Category::class);

        $categories = \App\Models\Category::query()
            ->select(['id', 'name', 'slug', 'parent_id', 'active', 'level'])
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        $nodes = [];
        foreach ($categories as $cat) {
            $nodes[$cat->id] = [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'parent_id' => $cat->parent_id,
                'active' => $cat->active,
                'level' => $cat->level,
                'children' => [],
            ];
        }

        $tree = [];
        foreach ($categories as $cat) {
            if ($cat->parent_id && isset($nodes[$cat->parent_id])) {
                $nodes[$cat->parent_id]['children'][] = &$nodes[$cat->id];
            } else {
                $tree[] = &$nodes[$cat->id];
            }
        }

        return response()->json($tree);
    }

    public function products(Request $request, Category $category)
    {
        // Accès backoffice (admin + restricted)
        $this->authorize('view', $category);

        $perPage = max(1, min((int) $request->query('per_page', 15), 100));

        // IDs de la catégorie + tous ses descendants (CTE Postgres)
        $ids = Category::descendantIdsAndSelf($category->id);

        $q = Product::query()
            ->with(['categories.parent']) // populate categories + parent
            ->whereHas('categories', function ($sub) use ($ids) {
                $sub->whereIn('categories.id', $ids);
            })
            ->distinct()
            ->latest();

        // Optionnel: filtres rapides
        if (!is_null($request->query('active'))) {
            $active = filter_var($request->query('active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if (!is_null($active)) {
                $q->where('active', $active);
            }
        }

        if ($search = $request->query('search')) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'ilike', "%{$search}%")
                    ->orWhere('sku', 'ilike', "%{$search}%");
            });
        }

        $products = $q->paginate($perPage)->withQueryString();

        return ProductResource::collection($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Category::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $this->authorize('create', Category::class);

        $category = Category::query()->create($request->validated());

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $this->authorize('view', $category);
        return new CategoryResource($category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $this->authorize('update', $category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->authorize('update', $category);
        $category->update($request->validated());
        return (new CategoryResource($category));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();
        return response()->json(['message' => 'Categorie supprimé..']);
    }

}
