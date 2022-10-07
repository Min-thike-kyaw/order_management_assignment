<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\UserRepositoryInterface;

class AuthController extends BaseController
{
    private UserRepositoryInterface $userRepository;
    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function register(Request $request)
    {
        $input = $request->only(['user_name', 'email', 'password', 'password_confirmation']);
        $validator = Validator::make($input, [
            'user_name' => 'required|unique:users|string|min:4|max:20',
            'email' => 'email',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError( $validator->errors());
        }
        return $this->sendResponseFromResult($this->userRepository->registerUser($input));
    }
    public function login(Request $request)
    {
        $input = $request->only(['user_name', 'password']);
        $validator = Validator::make($input, [
            'user_name' => 'required',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError( $validator->errors());
        }
        return $this->sendResponseFromResult($this->userRepository->loginUser($input));
    }
    public function logout()
    {
        return $this->sendResponseFromResult($this->userRepository->logoutUser());
    }
}
