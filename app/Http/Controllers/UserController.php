<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\UserRepositoryInterface;

class UserController extends BaseController
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendResponseFromResult($this->userRepository->getAllUsers());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'user_name' => 'required|string|unique:users',
            'email' => 'email|unique:users',
            'password' => 'required|min:6|string'
        ], [
            'user_name.unique' => 'User name is already taken',
        ]);

        if($validator->fails()){
            return $this->sendError( $validator->errors());
        }
        return $this->sendResponseFromResult($this->userRepository->createUser($input));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendResponseFromResult($this->userRepository->getUserById($id));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'string',
            'user_name' => "string|unique:users,user_name,$id",
            'email' => "email|unique:users,email,$id",
            'password' => 'min:6|string'
        ], [
            'user_name.unique' => 'User name is already taken',
        ]);
        // return $id;
        if($validator->fails()){
            return $this->sendError($validator->errors());
            // return $id;
        }
        return $this->sendResponseFromResult($this->userRepository->updateUser($id, $input));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->sendResponseFromResult($this->userRepository->deleteUser($id));
    }

    public function restore($id)
    {
        return $this->sendResponseFromResult($this->userRepository->restoreUser($id));
    }

    public function permanentDelete($id)
    {
        return $this->sendResponseFromResult($this->userRepository->forceDeleteUser($id));
    }
}
