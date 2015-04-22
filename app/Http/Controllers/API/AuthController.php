<?php namespace App\Http\Controllers\API;


class AuthController extends ApiController{

    public function __construct()
    {
        $this->middleware('auth.token', ['except' => ['index']]);
    }

    public function login(AuthRequest $request){

    }
}