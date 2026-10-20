<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * The token owner's profile, without the password or remember token.
     */
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user()->load('role'));
    }
}
