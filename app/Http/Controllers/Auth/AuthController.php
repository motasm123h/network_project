<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    use ResponseTrait;
    public function register(RegisterRequest $request)
    {
        $atter = $request->validated();
        $atter['password'] = Hash::make($atter['password']);
        $user = User::create($atter);
        $user->token = $user->createToken('secret')->plainTextToken;
        return $this->apiResponse("Done", $user, 200);
    }

    public function login(LoginRequest $request)
    {
        $atter = $request->validated();
        if (Auth::attempt($atter)) {
            $user = Auth::user();
            $user->token = $user->createToken('secret')->plainTextToken;
            return $this->apiResponse('success', $user);
        }
        return $this->apiResponse('Invalid credentials', null, 401);
    }

    public function logout()
    {
        $user = auth()->user();

        if ($user) {
            auth()->user()->tokens()->delete();
            return $this->apiResponse('Done', true, 200);
        }
        return $this->apiResponse('User not found', null, 401);
    }

    public function profile()
    {
        $data = auth()->user();
        return $this->apiResponse('Done', $data, 200);
    }
}
