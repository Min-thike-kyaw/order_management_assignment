<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getAllUsers();
    public function getUserById($userId);
    public function deleteUser($userId);
    public function createUser(array $userDetails);
    public function updateUser($userId, array $newDetails);
    public function loginUser($newDetails);
    public function registerUser($newDetails);
    public function logoutUser();
    public function restoreUser($userId);
    public function forceDeleteUser($userId);
}
