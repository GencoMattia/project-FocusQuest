<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        $user->makeHidden(['password']);
        return response()->json([
            "message" => "User profile retrieved successfully",
            "user" => $user,
        ]);
    }

    public function update(UpdateUserRequest $request) {
        try {
            $user = auth()->user();
            $validatedData = $request->validated();

            // Miglior gestione della password: aggiorna solo se presente e non vuota
            if (array_key_exists('password', $validatedData) && !empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }
            $user->update($validatedData);
            $user->makeHidden(['password']);

            return response()->json([
                "message" => "User profile updated successfully",
                "user" => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user profile',
                'error' => app()->environment('production') ? 'Internal server error' : $e->getMessage()
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
