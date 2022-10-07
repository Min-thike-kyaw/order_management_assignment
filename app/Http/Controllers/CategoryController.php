<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Response;

class CategoryController extends BaseController
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendResponseFromResult($this->categoryRepository->getAllCategories());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $input = $request->only(['name', 'name_mm']);
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'name_mm' => 'required|unique:categories',
        ], [
            'name_mm.unique' => 'Myanmar name already exists',
            'name_mm.requried' => 'Myanmar name is required',
        ]);

        if($validator->fails()){
            return $this->sendError( $validator->errors());
        }
        return $this->sendResponseFromResult($this->categoryRepository->createCategory($input));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendResponseFromResult($this->categoryRepository->getCategoryById($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->only(['name', 'name_mm']);
        $validator = Validator::make($input, [
            'name' => 'string',
            'name_mm' => "unique:categories,name_mm,$id",
        ], [
            'name_mm.unique' => 'Myanmar name already exists',
            'name_mm.requried' => 'Myanmar name is required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        return $this->sendResponseFromResult($this->categoryRepository->updateCategory($id, $input));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->sendResponseFromResult($this->categoryRepository->deleteCategory($id));
    }

    public function restore($id)
    {
        return $this->sendResponseFromResult($this->categoryRepository->restoreCategory($id));
    }

    public function permanentDelete($id)
    {
        return $this->sendResponseFromResult($this->categoryRepository->forceDeleteCategory($id));
    }
}
