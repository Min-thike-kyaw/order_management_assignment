<?php

namespace App\Interfaces;

interface CustomerRepositoryInterface
{
    public function getAllCustomers();
    public function getCustomerById($customerId);
    public function deleteCustomer($customerId);
    public function createCustomer(array $customerDetails);
    public function updateCustomer($customerId, array $newDetails);
    public function restoreCustomer($customerId);
    public function forceDeleteCustomer($customerId);
}
