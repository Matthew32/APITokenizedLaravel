<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Abstracts\Controller;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(auth()->user());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $result = null;
        $validator = Validator::make($request->all(), [
            'email' =>
                'email',
            'username' => [
                'string',
                'min:6'
            ],
            'password' => [
                'string',
                'min:6',
                'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ]
        ]);
        if ($validator->fails())
            $result = response()->json($validator->messages(), 200);
        else {
            if (isset($request->password) && !Hash::check($request->password_old, auth()->user()->getAuthPassword()))
                //check password
                $result = response()->json(["error" => 'Wrong old password'], 403);
            else {
                //update user
                $toSave = array();
                $attributes = ["username", "email", "password"];
                foreach ($attributes as $value)
                    if (isset($request->$value))
                        $toSave[$value] = $request->$value;


                $result = $this->userRepository->update(auth()->id(), $toSave) ?
                    response()->json("User Updated", 200) :
                    response()->json(["error" => 'Error saving user'], 403);
            }
        }


        return $result;


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function destroy()
    {
        $result = null;
        //remove user
        $removeData = $this->userRepository->delete(auth()->id());
        if ($removeData) {
            //remove token session
            auth()->logout();
            $result = response()->json("User Deleted", 200);
        } else
            $result = response()->json(["error" => 'Error saving user'], 403);

        return $result;
    }

    /**
     * Create user picture
     * @return \Illuminate\Http\JsonResponse
     */
    public function picture()
    {
        //create avatar if doesn't exist
        if (!Storage::exists('public' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . auth()->id() . ".jpg"))
            $this->userRepository->createPicture(auth()->id());

        return response()->json($url = URL::to("/") . '/api/user/avatar', 200);
    }

    /**
     * Show user avatar
     * @return \Illuminate\Http\Response
     */
    public function avatar()
    {
        //if the avatar is not create make it
        if (!Storage::exists('public' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . auth()->id() . ".jpg"))
            $this->userRepository->createPicture(auth()->id());

        $path = storage_path(
            'app' . DIRECTORY_SEPARATOR .
            'public' . DIRECTORY_SEPARATOR .
            'avatars' . DIRECTORY_SEPARATOR .
            auth()->id() . ".jpg");

        //just in case the avatar is not created
        if (!File::exists($path)) {
            abort(404);
        }

        //show avatar
        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

}
