<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display the authenticated user (aka me)
     */
    public function me(Request $request): UserResource
    {
        return UserResource::make($request->user());
    }
}
