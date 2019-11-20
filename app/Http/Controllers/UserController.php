<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Abstracts\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
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
                'email:rfc,dns',
            'username' => [
                'string',
                'min:6'
            ],
            'password' => [
                'string',
                'min:6',
                'confirmed',
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

    public function picture()
    {


        header('Content-type: image/png');

        imagejpeg(imagecreatefromjpeg(!file_exists("pic2" . DIRECTORY_SEPARATOR . auth()->id() . ".jpg")
            ?
            $this->userRepository->createPicture(auth()->id()) :
            "pic2" . DIRECTORY_SEPARATOR . auth()->id() . ".jpg"));

        exit;
    }

}
