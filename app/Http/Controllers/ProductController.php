<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\ProductResource;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Interfaces\ProductRepositoryInterface;

class ProductController extends BaseController
{

    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository) {
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendResponseFromResult($this->productRepository->getAllProducts());
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

        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|string',
            'name_mm' => 'required|unique:products',
            'category_id' => ['required', Rule::exists('categories', 'id')->where(function($query){
                $query->whereNull('deleted_at' );
            })],
            'price' => 'required|integer|gt:0',
            'image' => 'image',
            'weight' => 'required',
            'is_available' => 'required|boolean'
        ], [
            'name_mm.unique' => 'Myanmar name already exists',
            'name_mm.requried' => 'Myanmar name is required',
        ]);
        if(!array_key_exists('is_available', $input)) {
            $input['is_available'] = true;
        }
        if($validator->fails()){
            return $this->sendError( $validator->errors());
        }
        return $this->sendResponseFromResult($this->productRepository->createProduct($input));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendResponseFromResult($this->productRepository->getProductById($id));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'name_mm' => "unique:products,$id",
            'category_id' => [ Rule::exists('categories', 'id')->where(function($query){
                $query->whereNull('deleted_at' );
            })],
            'price' => 'integer|gt:0',
            'image' => 'image',
            'weight' => 'string',
            'is_available' => 'boolean'
        ], [
            'name_mm.unique' => 'Myanmar name already exists',
            'name_mm.requried' => 'Myanmar name is required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        return $this->sendResponseFromResult($this->productRepository->updateProduct($id, $input));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->sendResponseFromResult($this->productRepository->deleteProduct($id));
    }


    public function restore($id)
    {
        return $this->sendResponseFromResult($this->productRepository->restoreProduct($id));
    }

    public function permanentDelete($id)
    {
        return $this->sendResponseFromResult($this->productRepository->forceDeleteProduct($id));
    }
}
