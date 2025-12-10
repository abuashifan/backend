<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['productCategory', 'productUnit', 'defaultTax'])->paginate();

        return $this->successResponse([
            'items' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
        ], 'Products retrieved.');
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return $this->successResponse(
            new ProductResource($product->load(['productCategory', 'productUnit', 'defaultTax'])),
            'Product created.',
            Response::HTTP_CREATED
        );
    }

    public function show(Product $product)
    {
        return $this->successResponse(
            new ProductResource($product->load(['productCategory', 'productUnit', 'defaultTax'])),
            'Product retrieved.'
        );
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return $this->successResponse(
            new ProductResource($product->load(['productCategory', 'productUnit', 'defaultTax'])),
            'Product updated.'
        );
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return $this->successResponse(null, 'Product deleted.');
    }
}
