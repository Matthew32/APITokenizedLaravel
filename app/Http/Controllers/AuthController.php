<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Abstracts\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * Register new user
     * @param Request $request
     * @return void it will return the new token to the registered user
     * @throws ValidationException
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'email:rfc,dns',
            'username' => [
                'required',
                'string',
                'min:6'
            ],
            'password' => ['required',
                'string',
                'min:6',
                'confirmed',
                'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ]
        ]);

        $user = $this->userRepository->save($request->email, $request->username, $request->password);


        //TODO:Send email to the user to verify his email
        $result = null;
        if ($user) {
            $token = auth()->login($user);
            $result = $this->respondWithToken($token);
        } else {
            $result = response()->json(['error' => 'Error on saving User'], 401);
        }
        return $result;
    }

    /**
     * login user
     * @param Request $request
     * @return void it will return the new user token
     * @throws ValidationException
     */
    public function login(Request $request)
    {

        $this->validate($request, ['email' => 'required|email', 'password' => 'required']);

        $credentials = request(['email', 'password']);

        //TODO:check verified email or not

        if (!$token = auth()->attempt($credentials)) {
            $result = response()->json(['error' => 'Unauthorized'], 401);
        } else
            $result = $this->respondWithToken($token);


        return $result;
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
