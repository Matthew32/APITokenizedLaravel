<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Abstracts\Controller;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->middleware('jwt', ['except' => ['login', 'register']]);
        $this->userRepository = $userRepo;
    }

    /**
     * Register new user
     * @param Request $request
     * @return void it will return the new token to the registered user
     */
    public function register(Request $request)
    {
        $result = null;

        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email'
            ],
            'username' => [
                'required',
                'string',
                'min:6'
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ]
        ]);

        if (!$validator->fails()) {
            $userRegistered = $this->userRepository->save($request->email, $request->username, $request->password);

            //TODO:Send email to the user to verify his email

            $result = $userRegistered ?
                response()->json('User Created', 200) :
                response()->json(['error' => 'Error saving User'], 401);
        } else
            $result = response()->json($validator->messages(), 200);

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
        $validator = Validator::make($request->all(), ['email' => 'required|email', 'password' => 'required']);

        $credentials = request(['email', 'password']);

        //TODO:check verified email or not
        if (!$validator->fails()) {

            if (!$token = auth()->attempt($credentials)) {
                $result = response()->json(['error' => 'Unauthorized'], 401);
            } else
                $result = $this->respondWithToken($token);
        } else
            $result = response()->json($validator->messages(), 200);

        return $result;
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function payload()
    {
        return response()->json(auth()->payload());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
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
