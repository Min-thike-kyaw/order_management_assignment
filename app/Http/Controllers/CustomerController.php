<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Interfaces\CustomerRepositoryInterface;

class CustomerController extends BaseController
{
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository) {
        $this->customerRepository = $customerRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendResponseFromResult($this->customerRepository->getAllCustomers());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $input = $request->only(['name',  'phone_no', 'city_name','zone_name','is_active', 'address']);
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'phone_no' => 'required|unique:customers|string',
            'address' => 'required|string',
            'city_name' => 'string',
            'zone_name' => 'string',
            'is_active' => 'boolean'
        ], [
            'phone_no.unique' => 'Phone number already exists',
        ]);


        if($validator->fails()){
            return $this->sendError( $validator->errors());
        }

        return $this->sendResponseFromResult($this->customerRepository->createCustomer($input));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendResponseFromResult($this->customerRepository->getCustomerById($id));
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
        $input = $request->only(['name',  'phone_no', 'city_name','zone_name','is_active', 'address']);
        $validator = Validator::make($input, [
            'name' => 'string',
            'phone_no' => 'unique:customers',
            'address' => 'string',
            'city_name' => 'string',
            'zone_name' => 'string',
            'is_active' => 'boolean'
        ], [
            'phone_no.unique' => 'Phone number already exists',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        return $this->sendResponseFromResult($this->customerRepository->updateCustomer($id, $input));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->sendResponseFromResult($this->customerRepository->deleteCustomer($id));
    }

    public function restore($id)
    {
        return $this->sendResponseFromResult($this->customerRepository->restoreCustomer($id));
    }

    public function permanentDelete($id)
    {
        return $this->sendResponseFromResult($this->customerRepository->forceDeleteCustomer($id));
    }
}
