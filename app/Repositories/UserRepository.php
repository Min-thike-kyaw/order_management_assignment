<?php

namespace App\Repositories;

use Exception;
use App\Models\User;
use App\Filters\UserFilter;
use Illuminate\Http\Response;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers(){
        $result['success'] = true;
        try{
            $users = UserResource::collection(User::filter(app(UserFilter::class))->paginate(request('per_page') ?? config('emart.default_paginate')));
            $result['data'] = $users;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function getUserById($userId)
    {
        $result['success'] = true;
        try{
            $user = new UserResource(User::findOrFail($userId));
            $result['data'] = $user;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function createUser(array $userDetails)
    {
        $result['success'] = true;
        try{
            $userDetails['password'] = Hash::make($userDetails['password']);
            $user = User::create($userDetails);
            $result['data'] = new UserResource($user);
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;


    }

    public function updateUser($userId, array $newDetails)
    {
        $result['success'] = true;
        try{

            $user = User::findOrFail($userId);

            if(array_key_exists('password', $newDetails)) {
                $newDetails['password'] = Hash::make($newDetails['password']);
            }

            $user->update($newDetails);
            $result['data'] = new UserResource($user);
        } catch (Exception $e){
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function deleteUser($userId)
    {
        $result['success'] = true;
        try{
            User::findOrFail($userId)->delete();
            $result['data'] = null;
            $result['code'] = Response::HTTP_NO_CONTENT;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function loginUser($newDetails)
    {
        $result['success'] = true;
        try {
            if(Auth::attempt(['user_name' => $newDetails['user_name'], 'password' => $newDetails['password']])){
                $user = Auth::user();

                $result['data']['token'] =  $user->createToken('e_mart')->accessToken;

                $result['data']['user'] = new UserResource($user);
                // $result['data'] = (new UserResource($user))->additional(["token" => $token]);
            }
            else{
                $result = ['success' => false, 'message' => "Unauthorized", 'code' => 403];
            }
        } catch( Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function registerUser($newDetails)
    {
        $result['success'] = true;
        try{

            $newDetails['password'] = Hash::make($newDetails['password']);
            $user = User::create($newDetails);
            $result['data']['token'] =  $user->createToken('e_mart')->accessToken;
            $result['data']['user'] = new UserResource($user);
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function logoutUser()
    {
        $result['success'] = true;
        try{

            $result['data'] = auth()->user()->tokens()->delete();
            // $result['data'] = auth()->user()->token()->revoke();
            $result['code'] = Response::HTTP_NO_CONTENT;
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
    public function restoreUser($userId)
    {
        try{
            $user = User::withTrashed()->findOrFail($userId);
            $user->restore();

            $result = ['success' => true, 'data' => new UserResource($user)];
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }

    public function forceDeleteUser($userId)
    {
        try{
            $user = User::withTrashed()->findOrFail($userId);
            $user->tokens()->delete();
            $user->forceDelete();
            $result = ['success' => true, 'data' => NULL, 'code' => Response::HTTP_NO_CONTENT];
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage(), 'code' => 500];
        }
        return $result;
    }
}
