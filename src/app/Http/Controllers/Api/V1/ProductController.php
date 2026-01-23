<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection
    {
        $this->authorize('viewAny', Product::class);

        $perPage = $request->query('per_page', 20);
        $perPage = max(1, min($perPage, 100));

        $q = Product::query()
            ->with(['categories.parent']);

        if($categoryId = $request->query('category_id')) {
            $ids = Category::descendantIdsAndSelf($categoryId);
            $q->whereHas('categories', function ($sub) use ($ids) {
                $sub->whereIn('categories.id', $ids);
            })->distinct();
        }

        $products = $q->latest()->paginate($perPage)->withQueryString();

        return ProductResource::collection($products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Product::class);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);

        $data = $request->validated();
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);

        $product = Product::query()->create($data);
        if (!empty($categoryIds)) {
            $product->categories()->sync($categoryIds);
        }
        return (new ProductResource($product->load(['categories.parent'])))
            ->response()
            ->setStatusCode(201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $this->authorize('view', Product::class);
        return new ProductResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $this->authorize('update', Product::class);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', Product::class);

        $data = $request->validated();

        if (array_key_exists('category_ids', $data)) {
            $categoryIds = $data['category_ids'] ?? [];
            unset($data['category_ids']);

            $product->categories()->sync($categoryIds);
        }

        $product->update($data);

        return new ProductResource($product->fresh()->load(['categories.parent']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', Product::class);

        $product->delete();

        return response()->json(['message' => 'Produit supprimé.']);
    }
}
