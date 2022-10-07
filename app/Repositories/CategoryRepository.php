<?php

namespace App\Repositories;

use Exception;
use App\Models\Category;
use Illuminate\Http\Response;
use App\Filters\CategoryFilter;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\QueryException;
use App\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories(){
        $result['success'] = true;
        try{
            $categories = CategoryResource::collection(Category::filter(app(CategoryFilter::class))->paginate(request('per_page') ?? config('emart.default_paginate')));
            $result['data'] = $categories;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function getCategoryById($categoryId)
    {
        $result['success'] = true;
        try{
            $category = new CategoryResource(Category::with('products')->findOrFail($categoryId));
            $result['data'] = $category;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }



    public function createCategory(array $categoryDetails)
    {
        $result['success'] = true;
        try{
            $category = Category::create($categoryDetails);
            $result['data'] = new CategoryResource($category);
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;


    }
    public function updateCategory($categoryId, array $newDetails)
    {
        $result['success'] = true;
        try{
            $category = Category::findOrFail($categoryId);
            $category->update($newDetails);
            $result['data'] = new CategoryResource($category);
        } catch (Exception $e){
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
    public function deleteCategory($categoryId)
    {
        $result['success'] = true;
        try{
            Category::findOrFail($categoryId)->delete();
            $result['data'] = null;
            $result['code'] = Response::HTTP_NO_CONTENT;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function restoreCategory($categoryId)
    {
        try{
            $category = Category::withTrashed()->findOrFail($categoryId);
            $category->restore();
            $result = ['success' => true, 'data' => new CategoryResource($category)];
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function forceDeleteCategory($categoryId)
    {
        try {
            $category = Category::withTrashed()->findOrFail($categoryId);
            if(!$category->canSecureDelete('products')) {
                return ['success' => false, 'message' => "Cannot delete. Remove all the related associations.", 'code' => 419];
            }
            $category->forceDeleteWithRelations('products');
            $result = ['success' => true, 'data' => NULL, 'code' => Response::HTTP_NO_CONTENT];
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
}
