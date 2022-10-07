<?php

namespace App\Repositories;

use Exception;
use App\Models\Product;
use Illuminate\Http\Response;
use App\Filters\ProductFilter;
use App\Http\Resources\ProductResource;
use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllProducts(){
        $result['success'] = true;
        try{
            $categories = ProductResource::collection(Product::filter(app(ProductFilter::class))->paginate(request('per_page') ?? config('emart.default_paginate')));
            $result['data'] = $categories;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function getProductById($productId)
    {
        $result['success'] = true;
        try{
            $product = new ProductResource(Product::with('category')->findOrFail($productId));
            $result['data'] = $product;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }



    public function createProduct(array $productDetails)
    {
        $result['success'] = true;
        try{
            $file_path = '';
            if(request()->hasFile('image')) {
                $file_path = request()->file('image')->storeAs(config('emart.folder.product'), time().request()->file('image')->getClientOriginalName());
                $productDetails['image'] = $file_path;
            }

            $product = Product::create($productDetails);
            $result['data'] = new ProductResource($product->load('category'));
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;


    }
    public function updateProduct($productId, array $newDetails)
    {
        $result['success'] = true;
        try{
            $product = Product::findOrFail($productId);

            if(request()->hasFile('image')) {
                if(Storage::exists($product->image)) {
                    Storage::delete($product->image);
                }
                $file_path = request()->file('image')->storeAs(config('emart.folder.product'),time(). request()->file('image')->getClientOriginalName());
                $productDetails['image'] = $file_path;
            }
            $product->update($newDetails);
            $result['data'] = new ProductResource($product->load('category'));
        } catch (Exception $e){
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
    public function deleteProduct($productId)
    {
        $result['success'] = true;
        try{
            $product = Product::findOrFail($productId);
            $product->delete();
            $result['data'] = null;
            $result['code'] = Response::HTTP_NO_CONTENT;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function restoreProduct($productId)
    {
        try{
            $product = Product::withTrashed()->findOrFail($productId);
            $product->restore();
            $result = ['success' => true, 'data' => new ProductResource($product)];
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function forceDeleteProduct($productId)
    {
        try{
            $product = Product::withTrashed()->findOrFail($productId);
            $product->forceDelete();
            $result = ['success' => true, 'data' => NULL, 'code' => Response::HTTP_NO_CONTENT];
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
}
