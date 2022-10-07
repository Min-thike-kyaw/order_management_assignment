<?php

namespace App\Repositories;

use Exception;
use App\Models\Customer;
use Illuminate\Http\Response;
use App\Filters\CustomerFilter;
use App\Http\Resources\CustomerResource;
use App\Interfaces\CustomerRepositoryInterface;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function getAllCustomers(){
        $result['success'] = true;
        try{
            $customers = CustomerResource::collection(Customer::filter(app(CustomerFilter::class))->paginate(request('per_page') ?? config('emart.default_paginate')));
            $result['data'] = $customers;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function getCustomerById($customerId)
    {
        $result['success'] = true;
        try{
            $customer = new CustomerResource(Customer::findOrFail($customerId));
            $result['data'] = $customer;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }



    public function createCustomer(array $cusomerDetails)
    {
        $result['success'] = true;
        try{
            $customer = Customer::create($cusomerDetails);
            $result['data'] = new CustomerResource($customer);
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;


    }
    public function updateCustomer($customerId, array $newDetails)
    {
        $result['success'] = true;
        try{
            $customer = Customer::findOrFail($customerId);

            $customer->update($newDetails);
            $result['data'] = new CustomerResource($customer);
        } catch (Exception $e){
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
    public function deleteCustomer($customerId)
    {
        $result['success'] = true;
        try{
            Customer::findOrFail($customerId)->delete();
            $result['data'] = null;
            $result['code'] = Response::HTTP_NO_CONTENT;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function restoreCustomer($customerId)
    {
        try{
            $customer = Customer::withTrashed()->findOrFail($customerId);
            $customer->restore();
            $result = ['success' => true, 'data' => new CustomerResource($customer)];
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function forceDeleteCustomer($customerId)
    {
        try {
            $customer = Customer::withTrashed()->findOrFail($customerId);

            $customer->forceDelete();
            
            $result = ['success' => true, 'data' => NULL, 'code' => Response::HTTP_NO_CONTENT];
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
}
