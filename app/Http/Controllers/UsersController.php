<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\PostUserRequest;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
    public function create(PostUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'api_token' => $validated['api_token'],
            'is_admin' => $validated['is_admin'],
        ]);
        return new UserResource($user);
    }
}
