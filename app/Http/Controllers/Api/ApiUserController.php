<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ApiUserController extends Controller
{
    /**
     * Require JWT authentication for all methods except for user creation (store).
     */
    public function __construct() {
        $this->middleware("auth:api", ["except" => ["store"]]);
    }


    public function store(CreateUserRequest $request) {
        try {
            $validatedData = $request->validated();

            $user = User::create([
                'name' => $validatedData['name'],
                'surname' => $validatedData['surname'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
            ]);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show() {
        $user = auth()->user();

        return response()->json([
            "message" => "User profile retrieved successfully",
            "user" => $user,
        ]);
    }

    public function update(UpdateUserRequest $request) {
        try {
            $user = auth()->user();

            $validatedData = $request->validated();

            if ($request->filled("password")) {
                $validatedData["password"] = bcrypt($validatedData["password"]);
            } else {
                unset($validatedData["password"]);
            }

            $user->update($validatedData);

            return response()->json([
                "message" => "User profile updated successfully",
                "user" => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy() {
        $user = auth()->user();

        $user->delete();

        return response()->json([
            "message" => "User account deleted successfully"
        ], 200);
    }

}
